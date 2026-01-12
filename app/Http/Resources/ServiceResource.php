<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'chef_service' => $this->chef_service,
            'address' => $this->address,
            'contact' => $this->contact,
            
            'logo' => $this->whenLoaded('logo', fn() => [
                'id' => $this->logo->id,
                'url' => $this->logo->url,
            ]),
            'document' => $this->whenLoaded('document', fn() => [
                'id' => $this->document->id,
                'title' => $this->document->title,
                'slug' => $this->document->slug,
                'file_url' => $this->document->file?->url ?? null,
            ]),
            
            'order' => $this->order,
            'is_active' => $this->is_active,
            
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
