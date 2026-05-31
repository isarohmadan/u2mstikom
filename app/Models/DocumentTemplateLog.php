<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentTemplateLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'template_id',
        'version_id',
        'user_id',
        'downloaded_at',
        'ip_address',
    ];

    protected $dates = [
        'downloaded_at',
    ];

    public function template()
    {
        return $this->belongsTo(DocumentTemplate::class, 'template_id');
    }

    public function version()
    {
        return $this->belongsTo(DocumentTemplateVersion::class, 'version_id');
    }
}


