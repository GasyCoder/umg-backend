<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreNewsletterCampaignRequest;
use App\Http\Resources\NewsletterCampaignResource;
use App\Jobs\SendNewsletterToSubscriberJob;
use App\Models\NewsletterCampaign;
use App\Models\Post;
use App\Models\Setting;
use App\Models\NewsletterSend;
use App\Models\NewsletterSubscriber;
use App\Support\Audit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NewsletterCampaignAdminController extends Controller
{
    private function absoluteMediaUrl(?string $url): ?string
    {
        if (!is_string($url) || $url === '') return null;
        if (str_starts_with($url, 'http')) return $url;
        return rtrim((string) config('app.url', ''), '/') . '/' . ltrim($url, '/');
    }

    public function index(Request $request)
    {
        $this->ensureRoleAny($request);

        $q = NewsletterCampaign::query()
            ->withCount('sends')
            ->withCount(['sends as opens_count' => function ($query) {
                $query->whereNotNull('opened_at');
            }])
            ->orderByDesc('id');

        if ($request->filled('status')) {
            $q->where('status', $request->string('status'));
        } elseif (!$request->boolean('include_archived')) {
            // By default, exclude archived campaigns
            $q->where('status', '!=', 'archived');
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
     * POST /v1/admin/newsletter/campaigns/from-posts
     * - crée une campagne à partir de plusieurs articles (cards)
     * - optionnellement envoie immédiatement (send_now=true par défaut)
     *
     * Modes d'envoi:
     * - mode=subscribers (défaut): envoie à tous les abonnés avec le status donné
     * - mode=custom: envoie uniquement aux subscriber_ids et/ou extra_emails fournis
     */
    public function fromPosts(Request $request)
    {
        $this->ensureRoleSend($request);

        $data = $request->validate([
            'post_ids' => ['required', 'array', 'min:1', 'max:20'],
            'post_ids.*' => ['integer', 'distinct', 'min:1'],
            'subject' => ['sometimes', 'string', 'max:255'],
            'send_now' => ['sometimes', 'boolean'],
            // Nouveaux champs pour le ciblage des destinataires
            'mode' => ['sometimes', 'string', 'in:subscribers,custom'],
            'status' => ['sometimes', 'string', 'in:active,pending,unsubscribed'],
            'subscriber_ids' => ['sometimes', 'array'],
            'subscriber_ids.*' => ['integer', 'distinct', 'min:1'],
            'extra_emails' => ['sometimes', 'array'],
            'extra_emails.*' => ['email', 'max:255'],
        ]);

        $postIds = array_values(array_unique(array_map('intval', $data['post_ids'] ?? [])));
        if (count($postIds) === 0) {
            return response()->json(['message' => 'post_ids is required.'], 422);
        }

        $frontendBase = rtrim((string) config('app.frontend_url', 'http://localhost:3000'), '/');

        $siteName = (string) (Setting::get('site_name') ?? 'Université de Mahajanga');
        $logoUrl = null;
        $logoId = (int) (Setting::get('logo_id') ?? 0);
        $faviconId = (int) (Setting::get('favicon_id') ?? 0);
        $mediaId = $logoId > 0 ? $logoId : ($faviconId > 0 ? $faviconId : 0);
        if ($mediaId > 0) {
            $media = \App\Models\Media::find($mediaId);
            $logoUrl = $this->absoluteMediaUrl($media?->url);
        }

        $postsById = Post::query()
            ->whereIn('id', $postIds)
            ->with(['coverImage', 'categories'])
            ->get()
            ->keyBy('id');

        $missing = array_values(array_diff($postIds, $postsById->keys()->all()));
        if (count($missing)) {
            return response()->json([
                'message' => 'Some posts were not found.',
                'code' => 'POSTS_NOT_FOUND',
                'data' => ['missing_ids' => $missing],
            ], 422);
        }

        $invalid = [];
        foreach ($postIds as $id) {
            $p = $postsById[$id];
            if ($p->status !== 'published' || !$p->published_at) {
                $invalid[] = $p->id;
            }
        }
        if (count($invalid)) {
            return response()->json([
                'message' => 'Only published posts can be sent.',
                'code' => 'POSTS_NOT_PUBLISHED',
                'data' => ['invalid_ids' => $invalid],
            ], 422);
        }

        $cards = [];
        foreach ($postIds as $id) {
            $p = $postsById[$id];
            $url = $frontendBase . '/actualites/' . ltrim((string) $p->slug, '/');
            $excerpt = $p->excerpt ?: Str::limit(trim(preg_replace('/\s+/u', ' ', strip_tags((string) $p->content_html))), 160);
            $category = $p->categories?->first()?->name;

            $coverUrl = $p->coverImage?->url;
            $coverUrl = $this->absoluteMediaUrl(is_string($coverUrl) ? $coverUrl : null);

            $cards[] = [
                'id' => $p->id,
                'title' => $p->title,
                'url' => $url,
                'excerpt' => $excerpt,
                'category' => $category,
                'cover_image_url' => $coverUrl,
            ];
        }

        $week = now()->format('W');
        $date = now()->format('d/m/Y');

        $defaultSubject = 'Université de Mahajanga — Revue hebdomadaire • S' . $week . ' • ' . $date;
        $subject = trim((string) ($data['subject'] ?? $defaultSubject));
        if ($subject === '') $subject = $defaultSubject;

        $contentHtml = view('emails.newsletter.digest_content', [
            'posts' => $cards,
            'issue_label' => 'Semaine ' . $week,
            'date_label' => $date,
            'site_name' => $siteName,
            'frontend_base' => $frontendBase,
            'logo_url' => $logoUrl,
        ])->render();

        $campaign = NewsletterCampaign::create([
            'subject' => $subject,
            'content_html' => $contentHtml,
            'content_text' => null,
            'status' => 'draft',
            'post_id' => null,
            'created_by' => $request->user()->id,
        ]);

        $sendNow = (bool) ($data['send_now'] ?? true);
        if (!$sendNow) {
            return new NewsletterCampaignResource($campaign);
        }

        $updated = NewsletterCampaign::query()
            ->where('id', $campaign->id)
            ->where('status', 'draft')
            ->update(['status' => 'sending']);

        if ($updated === 0) {
            return response()->json([
                'message' => 'Campaign must be draft to send.',
                'code' => 'CAMPAIGN_NOT_DRAFT',
            ], 409);
        }

        $queuedCount = 0;

        // Déterminer le mode d'envoi
        $mode = $data['mode'] ?? 'subscribers';
        $targetStatus = $data['status'] ?? 'active';
        $subscriberIds = array_values(array_unique(array_map('intval', $data['subscriber_ids'] ?? [])));
        $extraEmails = array_values(array_unique(array_map('strtolower', array_map('trim', $data['extra_emails'] ?? []))));

        DB::transaction(function () use ($campaign, &$queuedCount, $mode, $targetStatus, $subscriberIds, $extraEmails) {
            if ($mode === 'custom') {
                // Mode personnalisé: utiliser subscriber_ids + extra_emails
                $targetSubscriberIds = [];

                // Ajouter les IDs fournis (vérifier qu'ils existent)
                if (count($subscriberIds) > 0) {
                    $existingIds = NewsletterSubscriber::query()
                        ->whereIn('id', $subscriberIds)
                        ->pluck('id')
                        ->toArray();
                    $targetSubscriberIds = array_merge($targetSubscriberIds, $existingIds);
                }

                // Traiter les extra_emails: créer ou récupérer les subscribers
                if (count($extraEmails) > 0) {
                    // Récupérer les emails déjà existants
                    $existingByEmail = NewsletterSubscriber::query()
                        ->whereIn('email', $extraEmails)
                        ->pluck('id', 'email')
                        ->toArray();

                    foreach ($extraEmails as $email) {
                        $emailLower = strtolower($email);
                        if (isset($existingByEmail[$emailLower])) {
                            $targetSubscriberIds[] = $existingByEmail[$emailLower];
                        } else {
                            // Créer un nouvel abonné avec status=active
                            $newSub = NewsletterSubscriber::create([
                                'email' => $emailLower,
                                'name' => null,
                                'status' => 'active',
                                'token' => bin2hex(random_bytes(32)),
                                'subscribed_at' => now(),
                            ]);
                            $targetSubscriberIds[] = $newSub->id;
                        }
                    }
                }

                // Dédupliquer
                $targetSubscriberIds = array_values(array_unique($targetSubscriberIds));

                if (count($targetSubscriberIds) > 0) {
                    $rows = [];
                    foreach ($targetSubscriberIds as $subId) {
                        $rows[] = [
                            'newsletter_campaign_id' => $campaign->id,
                            'newsletter_subscriber_id' => $subId,
                            'status' => 'queued',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                    $queuedCount = count($rows);
                    NewsletterSend::query()->insertOrIgnore($rows);
                }
            } else {
                // Mode subscribers: envoyer à tous les abonnés avec le status ciblé
                NewsletterSubscriber::query()
                    ->where('status', $targetStatus)
                    ->select(['id'])
                    ->orderBy('id')
                    ->chunkById(500, function ($subs) use ($campaign, &$queuedCount) {
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
                        $queuedCount += count($rows);
                        NewsletterSend::query()->insertOrIgnore($rows);
                    });
            }
        });

        // Dispatch jobs
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

        Audit::log($request, 'newsletter.campaign.send', 'NewsletterCampaign', $campaign->id, [
            'campaign_id' => $campaign->id,
            'source' => 'posts',
            'posts_count' => count($postIds),
        ]);

        return response()->json([
            'data' => true,
            'meta' => [
                'newsletter' => [
                    'campaign_id' => $campaign->id,
                    'queued' => $queuedCount,
                ],
            ],
        ]);
    }

    /**
     * POST /v1/admin/newsletter/campaigns/{id}/send
     * - verrouille le statut (draft -> sending)
     * - crée newsletter_sends (queued)
     * - dispatch les jobs
     *
     * Modes d'envoi (identique à fromPosts):
     * - mode=subscribers (défaut): envoie à tous les abonnés avec le status donné
     * - mode=custom: envoie uniquement aux subscriber_ids et/ou extra_emails fournis
     */
    public function send(Request $request, int $id)
    {
        $this->ensureRoleSend($request);

        $data = $request->validate([
            'mode' => ['sometimes', 'string', 'in:subscribers,custom'],
            'status' => ['sometimes', 'string', 'in:active,pending,unsubscribed'],
            'subscriber_ids' => ['sometimes', 'array'],
            'subscriber_ids.*' => ['integer', 'distinct', 'min:1'],
            'extra_emails' => ['sometimes', 'array'],
            'extra_emails.*' => ['email', 'max:255'],
        ]);

        $campaign = NewsletterCampaign::findOrFail($id);

        // Anti double-send strict
        $updated = NewsletterCampaign::query()
            ->where('id', $campaign->id)
            ->where('status', 'draft')
            ->update(['status' => 'sending']);

        if ($updated === 0) {
            return response()->json([
                'message' => 'Campaign must be draft to send.',
                'code' => 'CAMPAIGN_NOT_DRAFT',
            ], 409);
        }

        $queuedCount = 0;

        // Déterminer le mode d'envoi
        $mode = $data['mode'] ?? 'subscribers';
        $targetStatus = $data['status'] ?? 'active';
        $subscriberIds = array_values(array_unique(array_map('intval', $data['subscriber_ids'] ?? [])));
        $extraEmails = array_values(array_unique(array_map('strtolower', array_map('trim', $data['extra_emails'] ?? []))));

        DB::transaction(function () use ($campaign, &$queuedCount, $mode, $targetStatus, $subscriberIds, $extraEmails) {
            if ($mode === 'custom') {
                // Mode personnalisé: utiliser subscriber_ids + extra_emails
                $targetSubscriberIds = [];

                // Ajouter les IDs fournis (vérifier qu'ils existent)
                if (count($subscriberIds) > 0) {
                    $existingIds = NewsletterSubscriber::query()
                        ->whereIn('id', $subscriberIds)
                        ->pluck('id')
                        ->toArray();
                    $targetSubscriberIds = array_merge($targetSubscriberIds, $existingIds);
                }

                // Traiter les extra_emails: créer ou récupérer les subscribers
                if (count($extraEmails) > 0) {
                    $existingByEmail = NewsletterSubscriber::query()
                        ->whereIn('email', $extraEmails)
                        ->pluck('id', 'email')
                        ->toArray();

                    foreach ($extraEmails as $email) {
                        $emailLower = strtolower($email);
                        if (isset($existingByEmail[$emailLower])) {
                            $targetSubscriberIds[] = $existingByEmail[$emailLower];
                        } else {
                            $newSub = NewsletterSubscriber::create([
                                'email' => $emailLower,
                                'name' => null,
                                'status' => 'active',
                                'token' => bin2hex(random_bytes(32)),
                                'subscribed_at' => now(),
                            ]);
                            $targetSubscriberIds[] = $newSub->id;
                        }
                    }
                }

                // Dédupliquer
                $targetSubscriberIds = array_values(array_unique($targetSubscriberIds));

                if (count($targetSubscriberIds) > 0) {
                    $rows = [];
                    foreach ($targetSubscriberIds as $subId) {
                        $rows[] = [
                            'newsletter_campaign_id' => $campaign->id,
                            'newsletter_subscriber_id' => $subId,
                            'status' => 'queued',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                    $queuedCount = count($rows);
                    NewsletterSend::query()->insertOrIgnore($rows);
                }
            } else {
                // Mode subscribers: envoyer à tous les abonnés avec le status ciblé
                NewsletterSubscriber::query()
                    ->where('status', $targetStatus)
                    ->select(['id'])
                    ->orderBy('id')
                    ->chunkById(500, function ($subs) use ($campaign, &$queuedCount) {
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
                        $queuedCount += count($rows);
                        NewsletterSend::query()->insertOrIgnore($rows);
                    });
            }
        });

        // Dispatch jobs
        NewsletterSend::query()
            ->where('newsletter_campaign_id', $campaign->id)
            ->where('status', 'queued')
            ->select(['id'])
            ->orderBy('id')
            ->chunkById(500, function ($sends) {
                foreach ($sends as $send) {
                    \App\Jobs\SendNewsletterToSubscriberJob::dispatch($send->id);
                }
            });

        Audit::log($request, 'newsletter.campaign.send', 'NewsletterCampaign', $campaign->id, [
            'campaign_id' => $campaign->id,
            'mode' => $mode,
            'queued_count' => $queuedCount,
        ]);

        return response()->json([
            'data' => true,
            'meta' => [
                'queued' => $queuedCount,
            ],
        ]);
    }

    /**
     * GET /v1/admin/newsletter/campaigns/{id}/stats
     */
    public function stats(Request $request, int $id)
    {
        $this->ensureRoleAny($request);

        $campaign = NewsletterCampaign::findOrFail($id);

        $total = NewsletterSend::query()
            ->where('newsletter_campaign_id', $campaign->id)
            ->count();

        $byStatus = NewsletterSend::query()
            ->where('newsletter_campaign_id', $campaign->id)
            ->selectRaw('status, COUNT(*) as c')
            ->groupBy('status')
            ->pluck('c', 'status');

        $queued = (int) ($byStatus['queued'] ?? 0);
        $sent   = (int) ($byStatus['sent'] ?? 0);
        $failed = (int) ($byStatus['failed'] ?? 0);

        return response()->json([
            'data' => [
                'campaign' => [
                    'id' => $campaign->id,
                    'status' => $campaign->status,
                    'sent_at' => $campaign->sent_at?->toISOString(),
                ],
                'counts' => [
                    'total' => $total,
                    'queued' => $queued,
                    'sent' => $sent,
                    'failed' => $failed,
                ],
                'is_done' => ($total > 0 && $queued === 0),
            ],
        ]);
    }

    /**
     * POST /v1/admin/newsletter/campaigns/{id}/finalize
     * Marque status=sent si queued=0 et status=sending
     */
    public function finalize(Request $request, int $id)
    {
        $this->ensureRoleSend($request);

        $campaign = NewsletterCampaign::findOrFail($id);

        if ($campaign->status !== 'sending') {
            return response()->json([
                'message' => 'Campaign must be sending to finalize.',
                'code' => 'CAMPAIGN_NOT_SENDING',
            ], 409);
        }

        $queued = NewsletterSend::query()
            ->where('newsletter_campaign_id', $campaign->id)
            ->where('status', 'queued')
            ->count();

        if ($queued > 0) {
            return response()->json([
                'message' => 'Campaign still has queued sends.',
                'code' => 'CAMPAIGN_NOT_FINISHED',
                'data' => ['queued' => $queued],
            ], 409);
        }

        $campaign->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        Audit::log($request, 'newsletter.campaign.finalize', 'NewsletterCampaign', $campaign->id, [
            'campaign_id' => $campaign->id,
        ]);

        return response()->json(['data' => true]);
    }

    public function update(Request $request, int $id)
    {
        $this->ensureRoleAny($request);

        $campaign = NewsletterCampaign::findOrFail($id);

        // Only allow editing drafts
        if ($campaign->status !== 'draft') {
            return response()->json([
                'message' => 'Only draft campaigns can be edited.',
                'code' => 'CAMPAIGN_NOT_EDITABLE',
            ], 409);
        }

        $data = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'content_html' => ['required', 'string'],
            'content_text' => ['nullable', 'string'],
        ]);

        $campaign->update($data);

        Audit::log($request, 'newsletter.campaign.update', 'NewsletterCampaign', $campaign->id, [
            'subject' => $campaign->subject,
        ]);

        return new NewsletterCampaignResource($campaign);
    }

    public function destroy(Request $request, int $id)
    {
        $this->ensureRoleSend($request);

        $campaign = NewsletterCampaign::findOrFail($id);

        // Only allow deleting drafts and archived
        if (!in_array($campaign->status, ['draft', 'archived'])) {
            return response()->json([
                'message' => 'Only draft or archived campaigns can be deleted.',
                'code' => 'CAMPAIGN_NOT_DELETABLE',
            ], 409);
        }

        Audit::log($request, 'newsletter.campaign.delete', 'NewsletterCampaign', $campaign->id, [
            'subject' => $campaign->subject,
        ]);

        $campaign->delete();

        return response()->json(['data' => true]);
    }

    /**
     * POST /v1/admin/newsletter/campaigns/{id}/archive
     * Archive a campaign (draft, sending, sent -> archived)
     */
    public function archive(Request $request, int $id)
    {
        $this->ensureRoleSend($request);

        $campaign = NewsletterCampaign::findOrFail($id);

        if ($campaign->status === 'archived') {
            return response()->json([
                'message' => 'Campaign is already archived.',
                'code' => 'ALREADY_ARCHIVED',
            ], 409);
        }

        // Store original status for potential restore
        $originalStatus = $campaign->status;
        
        $campaign->update([
            'status' => 'archived',
        ]);

        Audit::log($request, 'newsletter.campaign.archive', 'NewsletterCampaign', $campaign->id, [
            'subject' => $campaign->subject,
            'previous_status' => $originalStatus,
        ]);

        return new NewsletterCampaignResource($campaign);
    }

    /**
     * POST /v1/admin/newsletter/campaigns/{id}/restore
     * Restore an archived campaign back to draft
     */
    public function restore(Request $request, int $id)
    {
        $this->ensureRoleSend($request);

        $campaign = NewsletterCampaign::findOrFail($id);

        if ($campaign->status !== 'archived') {
            return response()->json([
                'message' => 'Only archived campaigns can be restored.',
                'code' => 'NOT_ARCHIVED',
            ], 409);
        }

        $campaign->update([
            'status' => 'draft',
        ]);

        Audit::log($request, 'newsletter.campaign.restore', 'NewsletterCampaign', $campaign->id, [
            'subject' => $campaign->subject,
        ]);

        return new NewsletterCampaignResource($campaign);
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
