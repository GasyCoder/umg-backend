<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DocumentResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,

            'status' => $this->status,
            'published_at' => $this->published_at?->toISOString(),
            'download_count' => (int) $this->download_count,

            'category' => $this->whenLoaded('category', fn() => new DocumentCategoryResource($this->category)),
            'file' => $this->whenLoaded('file', fn() => new MediaResource($this->file)),
        ];
    }
}
