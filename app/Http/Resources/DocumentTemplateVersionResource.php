<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DocumentTemplateVersionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'version_number' => $this->version_number,
            'file_path' => $this->file_path,
            'original_filename' => $this->original_filename,
            'mime_type' => $this->mime_type,
            'file_size' => $this->file_size,
            'uploaded_by' => $this->uploaded_by,
            'created_at' => $this->created_at,
        ];
    }
}


