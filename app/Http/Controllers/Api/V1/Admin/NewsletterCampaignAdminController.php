<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreNewsletterCampaignRequest;
use App\Http\Resources\NewsletterCampaignResource;
use App\Jobs\SendNewsletterToSubscriberJob;
use App\Models\NewsletterCampaign;
use App\Models\NewsletterSend;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NewsletterCampaignAdminController extends Controller
{
    public function index(Request $request)
    {
        $this->ensureRoleAny($request);

        $q = NewsletterCampaign::query()->orderByDesc('id');

        if ($request->filled('status')) {
            $q->where('status', $request->string('status'));
        }

        $per = min((int)$request->get('per_page', 20), 100);

        return NewsletterCampaignResource::collection($q->paginate($per));
    }

    public function show(Request $request, int $id)
    {
        $this->ensureRoleAny($request);

        $campaign = NewsletterCampaign::findOrFail($id);

        // include content with ?include_content=1
        return new NewsletterCampaignResource($campaign);
    }

    public function store(StoreNewsletterCampaignRequest $request)
    {
        $data = $request->validated();

        $campaign = NewsletterCampaign::create([
            'subject' => $data['subject'],
            'content_html' => $data['content_html'],
            'content_text' => $data['content_text'] ?? null,
            'status' => 'draft',
            'post_id' => $data['post_id'] ?? null,
            'created_by' => $request->user()->id,
        ]);

        return new NewsletterCampaignResource($campaign);
    }

    /**
     * POST /v1/admin/newsletter/campaigns/{id}/send
     * - verrouille le statut (draft -> sending)
     * - crée newsletter_sends (queued)
     * - dispatch les jobs
     */
    public function send(Request $request, int $id)
    {
        $this->ensureRoleSend($request);

        $campaign = NewsletterCampaign::findOrFail($id);

        if ($campaign->status !== 'draft') {
            return response()->json([
                'message' => 'Campaign must be draft to send.',
                'code' => 'CAMPAIGN_NOT_DRAFT',
            ], 409);
        }

        DB::transaction(function () use ($campaign) {
            $campaign->update(['status' => 'sending']);

            NewsletterSubscriber::query()
                ->where('status', 'active')
                ->select(['id'])
                ->orderBy('id')
                ->chunkById(500, function ($subs) use ($campaign) {
                    $rows = [];
                    foreach ($subs as $s) {
                        $rows[] = [
                            'newsletter_campaign_id' => $campaign->id,
                            'newsletter_subscriber_id' => $s->id,
                            'status' => 'queued',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }

                    // évite doublons si relancé
                    NewsletterSend::query()->insertOrIgnore($rows);
                });
        });

        // Dispatch jobs par send_id (plus fiable)
        NewsletterSend::query()
            ->where('newsletter_campaign_id', $campaign->id)
            ->where('status', 'queued')
            ->select(['id'])
            ->orderBy('id')
            ->chunkById(500, function ($sends) {
                foreach ($sends as $send) {
                    SendNewsletterToSubscriberJob::dispatch($send->id);
                }
            });

        return response()->json(['data' => true]);
    }

    private function ensureRoleAny(Request $request): void
    {
        abort_unless($request->user()?->hasAnyRole(['SuperAdmin','Validateur','Redacteur']), 403);
    }

    private function ensureRoleSend(Request $request): void
    {
        // Envoi : uniquement Validateur / SuperAdmin
        abort_unless($request->user()?->hasAnyRole(['SuperAdmin','Validateur']), 403);
    }
}
