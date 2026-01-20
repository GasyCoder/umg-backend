<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreNewsletterSubscriberRequest;
use App\Http\Resources\NewsletterSubscriberResource;
use App\Models\NewsletterSubscriber;
use App\Support\Audit;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NewsletterSubscriberAdminController extends Controller
{
    public function index(Request $request)
    {
        $this->ensureRole($request);

        $q = NewsletterSubscriber::query()->orderByDesc('id');

        if ($request->filled('status')) {
            $q->where('status', $request->string('status'));
        }

        if ($request->filled('q')) {
            $term = $request->string('q')->toString();
            $q->where(fn($w) => $w
                ->where('email', 'like', "%$term%")
                ->orWhere('name', 'like', "%$term%")
            );
        }

        $per = min((int)$request->get('per_page', 50), 200);

        return NewsletterSubscriberResource::collection($q->paginate($per));
    }

    /**
     * GET /v1/admin/newsletter/subscribers/counts
     * Retourne le nombre d'abonnés par status
     */
    public function counts(Request $request)
    {
        $this->ensureRole($request);

        $counts = NewsletterSubscriber::query()
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $total = array_sum($counts);

        return response()->json([
            'data' => [
                'total' => $total,
                'active' => (int) ($counts['active'] ?? 0),
                'pending' => (int) ($counts['pending'] ?? 0),
                'unsubscribed' => (int) ($counts['unsubscribed'] ?? 0),
            ],
        ]);
    }

    public function store(StoreNewsletterSubscriberRequest $request)
    {
        $data = $request->validated();

        $subscriber = NewsletterSubscriber::query()->firstOrNew(['email' => $data['email']]);

        $subscriber->name = $data['name'] ?? $subscriber->name;
        $subscriber->status = $data['status'] ?? 'active';
        $subscriber->token = $subscriber->token ?: Str::random(64);

        if ($subscriber->status === 'active') {
            $subscriber->subscribed_at = $subscriber->subscribed_at ?: now();
            $subscriber->unsubscribed_at = null;
        }

        if ($subscriber->status === 'unsubscribed') {
            $subscriber->unsubscribed_at = $subscriber->unsubscribed_at ?: now();
        }

        $subscriber->save();

        Audit::log($request, 'newsletter.subscriber.upsert', 'NewsletterSubscriber', $subscriber->id, [
            'email' => $subscriber->email,
            'status' => $subscriber->status,
        ]);

        return new NewsletterSubscriberResource($subscriber);
    }

    public function update(Request $request, int $id)
    {
        $this->ensureRole($request);

        $subscriber = NewsletterSubscriber::findOrFail($id);

        $data = $request->validate([
            'name' => ['nullable','string','max:255'],
            'status' => ['required','in:active,unsubscribed,pending'],
        ]);

        $subscriber->name = $data['name'] ?? $subscriber->name;
        $subscriber->status = $data['status'];

        if ($data['status'] === 'active') {
            $subscriber->subscribed_at = $subscriber->subscribed_at ?: now();
            $subscriber->unsubscribed_at = null;
        }

        if ($data['status'] === 'unsubscribed') {
            $subscriber->unsubscribed_at = now();
        }

        $subscriber->save();

        Audit::log($request, 'newsletter.subscriber.update', 'NewsletterSubscriber', $subscriber->id, [
            'email' => $subscriber->email,
            'status' => $subscriber->status,
        ]);

        return new NewsletterSubscriberResource($subscriber);
    }

    public function destroy(Request $request, int $id)
    {
        $this->ensureRole($request);

        $subscriber = NewsletterSubscriber::findOrFail($id);

        Audit::log($request, 'newsletter.subscriber.delete', 'NewsletterSubscriber', $subscriber->id, [
            'email' => $subscriber->email,
        ]);

        $subscriber->delete();

        return response()->json(['data' => true]);
    }

    /**
     * Import subscribers in bulk from a list of emails
     */
    public function bulkStore(Request $request)
    {
        $this->ensureRole($request);

        $data = $request->validate([
            'emails' => ['required', 'string'],
            'status' => ['nullable', 'in:active,pending'],
        ]);

        $status = $data['status'] ?? 'active';
        $lines = preg_split('/\r\n|\r|\n/', $data['emails']);

        $imported = 0;
        $duplicates = 0;
        $invalid = 0;
        $importedEmails = [];

        foreach ($lines as $line) {
            $email = strtolower(trim($line));

            // Skip empty lines
            if (empty($email)) {
                continue;
            }

            // Validate email format
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $invalid++;
                continue;
            }

            // Check if already exists
            $existing = NewsletterSubscriber::where('email', $email)->first();
            if ($existing) {
                $duplicates++;
                continue;
            }

            // Create new subscriber
            $subscriber = NewsletterSubscriber::create([
                'email' => $email,
                'status' => $status,
                'token' => Str::random(64),
                'subscribed_at' => $status === 'active' ? now() : null,
            ]);

            $importedEmails[] = $email;
            $imported++;
        }

        // Log the bulk import
        if ($imported > 0) {
            Audit::log($request, 'newsletter.subscriber.bulk_import', 'NewsletterSubscriber', null, [
                'imported_count' => $imported,
                'status' => $status,
            ]);
        }

        return response()->json([
            'data' => [
                'imported' => $imported,
                'duplicates' => $duplicates,
                'invalid' => $invalid,
                'total_processed' => $imported + $duplicates + $invalid,
            ],
            'message' => "{$imported} email(s) importé(s) avec succès.",
        ]);
    }

    private function ensureRole(Request $request): void
    {
        abort_unless($request->user()?->hasAnyRole(['SuperAdmin','Validateur']), 403);
    }
}
