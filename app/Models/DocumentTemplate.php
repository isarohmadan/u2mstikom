<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'latest_version_id',
        'latest_version_number',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'latest_version_number' => 'integer',
    ];

    public function versions()
    {
        return $this->hasMany(DocumentTemplateVersion::class, 'template_id');
    }

    public function latestVersion()
    {
        return $this->belongsTo(DocumentTemplateVersion::class, 'latest_version_id');
    }

    public function logs()
    {
        return $this->hasMany(DocumentTemplateLog::class, 'template_id');
    }
}


