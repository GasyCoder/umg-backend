<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PresidentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'title' => $this->title,
            'mandate_start' => $this->mandate_start,
            'mandate_end' => $this->mandate_end,
            'mandate_period' => $this->mandate_period,
            'bio' => $this->bio,
            'photo' => $this->whenLoaded('photo', fn() => [
                'id' => $this->photo->id,
                'url' => $this->photo->url,
            ]),
            'is_current' => $this->is_current,
            'order' => $this->order,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
