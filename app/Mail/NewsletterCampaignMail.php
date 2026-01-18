<?php

namespace App\Mail;

use App\Models\NewsletterCampaign;
use App\Models\NewsletterSubscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewsletterCampaignMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public NewsletterCampaign $campaign,
        public NewsletterSubscriber $subscriber
    ) {}

    public function build()
    {
        $frontendBase = rtrim((string) config('app.frontend_url', 'http://localhost:3000'), '/');

        $unsubscribeUrl = rtrim((string) config('app.frontend_url', 'http://localhost:3000'), '/')
            . '/newsletter/unsubscribe?token=' . $this->subscriber->token;

        $post = $this->campaign->post;
        $readMoreUrl = null;
        if ($post && is_string($post->slug) && $post->slug !== '') {
            $readMoreUrl = $frontendBase . '/actualites/' . ltrim($post->slug, '/');
        }

        return $this->subject($this->campaign->subject)
            ->view('emails.newsletter.campaign')
            ->with([
                'campaign' => $this->campaign,
                'subscriber' => $this->subscriber,
                'unsubscribeUrl' => $unsubscribeUrl,
                'readMoreUrl' => $readMoreUrl,
            ]);
    }
}
