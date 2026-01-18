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
        $this->campaign->loadMissing(['post.coverImage']);

        $frontendBase = rtrim((string) config('app.frontend_url', 'http://localhost:3000'), '/');

        $unsubscribeUrl = $frontendBase . '/newsletter/unsubscribe?token=' . $this->subscriber->token;

        $post = $this->campaign->post;
        $readMoreUrl = null;
        if ($post && is_string($post->slug) && $post->slug !== '') {
            $readMoreUrl = $frontendBase . '/actualites/' . ltrim($post->slug, '/');
        }

        $coverImageUrl = null;
        $coverImageAlt = null;
        if ($post && $post->coverImage) {
            $coverImageAlt = $post->coverImage->alt ?: $post->title;
            $coverImageUrl = $post->coverImage->url;
            if (is_string($coverImageUrl) && $coverImageUrl !== '' && !str_starts_with($coverImageUrl, 'http')) {
                $coverImageUrl = rtrim((string) config('app.url', ''), '/') . '/' . ltrim($coverImageUrl, '/');
            }
        }

        $contentHtmlEmail = $this->normalizeHtmlForEmail($this->campaign->content_html);

        return $this->subject($this->campaign->subject)
            ->view('emails.newsletter.campaign')
            ->with([
                'campaign' => $this->campaign,
                'subscriber' => $this->subscriber,
                'unsubscribeUrl' => $unsubscribeUrl,
                'readMoreUrl' => $readMoreUrl,
                'coverImageUrl' => $coverImageUrl,
                'coverImageAlt' => $coverImageAlt,
                'postExcerpt' => $post?->excerpt,
                'frontendBase' => $frontendBase,
                'contentHtmlEmail' => $contentHtmlEmail,
            ]);
    }

    private function addInlineStyle(string $attrs, string $styleToAdd): string
    {
        if (preg_match('/\sstyle\s*=\s*([\'"])(.*?)\1/i', $attrs, $m)) {
            $quote = $m[1];
            $existing = trim($m[2] ?? '');
            if ($existing !== '' && !str_ends_with($existing, ';')) {
                $existing .= ';';
            }
            $next = trim($existing . ' ' . $styleToAdd);

            return preg_replace(
                '/\sstyle\s*=\s*([\'"])(.*?)\1/i',
                ' style=' . $quote . $next . $quote,
                $attrs,
                1
            ) ?? $attrs;
        }

        return rtrim($attrs) . ' style="' . $styleToAdd . '"';
    }

    private function normalizeHtmlForEmail(?string $html): string
    {
        $html = (string) $html;

        // Make content responsive in email clients (Gmail/Outlook).
        $html = preg_replace_callback('/<img\b([^>]*)>/i', function ($m) {
            $attrs = $m[1] ?? '';
            $attrs = $this->addInlineStyle($attrs, 'max-width:100% !important;height:auto !important;');
            return '<img' . $attrs . '>';
        }, $html) ?? $html;

        $html = preg_replace_callback('/<table\b([^>]*)>/i', function ($m) {
            $attrs = $m[1] ?? '';
            $attrs = $this->addInlineStyle($attrs, 'width:100% !important;max-width:100% !important;border-collapse:collapse;');
            return '<table' . $attrs . '>';
        }, $html) ?? $html;

        $html = preg_replace_callback('/<pre\b([^>]*)>/i', function ($m) {
            $attrs = $m[1] ?? '';
            $attrs = $this->addInlineStyle($attrs, 'white-space:pre-wrap;word-break:break-word;overflow:auto;max-width:100%;');
            return '<pre' . $attrs . '>';
        }, $html) ?? $html;

        return $html;
    }
}
