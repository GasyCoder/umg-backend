<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
{
    public function toArray($request): array
    {
        $entryType = $this->type === 'folder' ? 'folder' : 'file';

        return [
            'id' => $this->id,
            'entry_type' => $entryType,
            'name' => $this->name,
            'parent_id' => $this->parent_id,
            'children_count' => $entryType === 'folder' ? (int) ($this->children_count ?? 0) : null,
            'files_count' => $entryType === 'folder' ? (int) ($this->files_count ?? 0) : null,
            'folders_count' => $entryType === 'folder' ? (int) ($this->folders_count ?? 0) : null,
            'files_size' => $entryType === 'folder' ? (int) ($this->files_size ?? 0) : null,
            'url' => $this->url, // via accessor
            'filename' => $entryType === 'file' ? basename((string) $this->path) : null,
            'type' => $this->mime,
            'disk' => $this->disk,
            'path' => $this->path,
            'mime' => $this->mime,
            'size' => $this->size,
            'alt' => $this->alt,
            'width' => $this->width,
            'height' => $this->height,
            'uploaded_at' => $this->created_at?->toISOString(),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
