<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NewsletterCampaignResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'subject' => $this->subject,
            'status' => $this->status,
            'post_id' => $this->post_id,
            'scheduled_at' => $this->scheduled_at?->toISOString(),
            'sent_at' => $this->sent_at?->toISOString(),
            'created_by' => $this->created_by,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),

            // Counts from withCount
            'recipients_count' => (int) ($this->sends_count ?? 0),
            'opens_count' => (int) ($this->opens_count ?? 0),
            'clicks_count' => 0, // TODO: Implement click tracking

            // Optional content (for preview/edit)
            'content_html' => $this->when($request->boolean('include_content'), $this->content_html),
            'content_text' => $this->when($request->boolean('include_content'), $this->content_text),
        ];
    }
}
