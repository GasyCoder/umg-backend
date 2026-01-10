<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'url' => $this->url, // via accessor
            'disk' => $this->disk,
            'path' => $this->path,
            'mime' => $this->mime,
            'size' => $this->size,
            'alt' => $this->alt,
            'width' => $this->width,
            'height' => $this->height,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
