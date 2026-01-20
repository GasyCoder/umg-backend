<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EtablissementResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'acronym' => $this->acronym,
            'description' => $this->description,

            'director_name' => $this->director_name,
            'director_title' => $this->director_title,

            'address' => $this->address,
            'phone' => $this->phone,
            'email' => $this->email,
            'website' => $this->website,

            'facebook' => $this->facebook,
            'twitter' => $this->twitter,
            'linkedin' => $this->linkedin,

            'sigle' => $this->sigle,
            'type_id' => $this->type_id,
            'is_doctoral' => (bool) $this->is_doctoral,
            'uuid' => $this->uuid,
            'status' => $this->status,

            'logo' => $this->whenLoaded('logo', fn() => [
                'id' => $this->logo->id,
                'url' => $this->logo->url,
            ]),
            'cover_image' => $this->whenLoaded('coverImage', fn() => [
                'id' => $this->coverImage->id,
                'url' => $this->coverImage->url,
            ]),
            'formations' => $this->whenLoaded('formations', fn() => $this->formations->map(fn ($formation) => [
                'id' => $formation->id,
                'title' => $formation->title,
                'level' => $formation->level,
                'description' => $formation->description,
            ])),
            'parcours' => $this->whenLoaded('parcours', fn() => $this->parcours->map(fn ($parcours) => [
                'id' => $parcours->id,
                'title' => $parcours->title,
                'mode' => $parcours->mode,
                'description' => $parcours->description,
            ])),
            'doctoral_teams' => $this->whenLoaded('doctoralTeams', fn() => $this->doctoralTeams->map(fn ($team) => [
                'id' => $team->id,
                'name' => $team->name,
                'focus' => $team->focus,
            ])),
            'order' => $this->order,
            'is_active' => $this->is_active,

            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
