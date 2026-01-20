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

    /**
     * Get the middleware the job should pass through.
     *
     * @return array
     */
    public function middleware()
    {
        // Retry en cas d'erreur SMTP (timeout), 3 tentatives, pause de 10-60s
        return [(new \Illuminate\Queue\Middleware\ThrottlesExceptions(3, 60))->backoff(30)];
    }

    /**
     * Determine the time at which the job should timeout.
     */
    public function retryUntil()
    {
        return now()->addHours(2);
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
            // RATE LIMITING MANUEL (SMTP Protection)
            // On dort 1 seconde pour limiter à ~60 mails/min max par worker
            // Cela évite de se faire bannir par le provider SMTP (limites horaires)
            sleep(1); 

            Mail::to($subscriber->email)->send(new NewsletterCampaignMail($campaign, $subscriber, $send->id));

            $send->update([
                'status' => NewsletterSend::STATUS_SENT,
                'sent_at' => now(),
                'error' => null,
            ]);

            $this->finalizeCampaignIfDone($campaign->id);

        } catch (\Throwable $e) {
            $errorMsg = mb_substr($e->getMessage(), 0, 2000);
            
            // On ne marque FAILED définitif que si on a épuisé les retries (géré par le framework)
            // Mais ici on veut voir l'erreur dans la table 'newsletter_sends' immédiatement pour debug
            // On met à jour l'erreur mais on garde le status QUEUED si on veut que le Job Retry le tente encore ?
            // Non, si ça fail ici, le Job va throw, et Laravel va le remettre en queue ou failed_jobs.
            // On marque failed dans notre table tracking pour info.
            
            $send->update([
                'status' => NewsletterSend::STATUS_FAILED, // Temporaire, le retry pourrait réussir
                'error' => $errorMsg,
            ]);

            $this->finalizeCampaignIfDone($campaign->id);

            // Important: Re-throw pour que Laravel gère le retry/backoff
            throw $e;
        }
    }
}
