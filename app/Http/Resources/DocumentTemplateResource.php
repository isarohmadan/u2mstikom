<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DocumentTemplateResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'latest_version_number' => $this->latest_version_number,
            'latest_version' => $this->whenLoaded('latestVersion', fn () => new DocumentTemplateVersionResource($this->latestVersion)),
            'versions' => $this->whenLoaded('versions', fn () => DocumentTemplateVersionResource::collection($this->versions()->orderByDesc('version_number')->get())),
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}


