<?php

namespace App\Jobs;

use App\Mail\NewsletterCampaignMail;
use App\Models\NewsletterCampaign;
use App\Models\NewsletterSend;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendNewsletterToSubscriberJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $sendId) {}

    private function finalizeCampaignIfDone(int $campaignId): void
    {
        $hasQueued = NewsletterSend::query()
            ->where('newsletter_campaign_id', $campaignId)
            ->where('status', NewsletterSend::STATUS_QUEUED)
            ->exists();

        if ($hasQueued) return;

        NewsletterCampaign::query()
            ->where('id', $campaignId)
            ->where('status', NewsletterCampaign::STATUS_SENDING)
            ->update([
                'status' => NewsletterCampaign::STATUS_SENT,
                'sent_at' => now(),
            ]);
    }

    public function handle(): void
    {
        $send = NewsletterSend::with(['campaign', 'subscriber'])->find($this->sendId);

        if (!$send) return;

        // Si déjà traité, on sort (idempotence)
        if ($send->status !== NewsletterSend::STATUS_QUEUED) return;

        $campaign = $send->campaign;
        $subscriber = $send->subscriber;

        // Si subscriber n'est plus actif, on marque failed (ou on supprime)
        if (!$subscriber || $subscriber->status !== 'active') {
            $send->update([
                'status' => NewsletterSend::STATUS_FAILED,
                'error' => 'Subscriber not active or missing.',
            ]);
            if ($campaign) $this->finalizeCampaignIfDone($campaign->id);
            return;
        }

        try {
            Mail::to($subscriber->email)->send(new NewsletterCampaignMail($campaign, $subscriber));

            $send->update([
                'status' => NewsletterSend::STATUS_SENT,
                'sent_at' => now(),
                'error' => null,
            ]);

            $this->finalizeCampaignIfDone($campaign->id);

        } catch (\Throwable $e) {
            $send->update([
                'status' => NewsletterSend::STATUS_FAILED,
                'error' => mb_substr($e->getMessage(), 0, 2000),
            ]);

            $this->finalizeCampaignIfDone($campaign->id);

            // relancer si vous voulez
            throw $e;
        }
    }
}
