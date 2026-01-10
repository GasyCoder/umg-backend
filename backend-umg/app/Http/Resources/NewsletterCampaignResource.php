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
            'sent_at' => $this->sent_at?->toISOString(),
            'created_by' => $this->created_by,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),

            // Optionnel (utile en admin preview)
            'content_html' => $this->when($request->boolean('include_content'), $this->content_html),
            'content_text' => $this->when($request->boolean('include_content'), $this->content_text),
        ];
    }
}
