<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
use App\Models\Kelas;
use App\Models\Tugas;

class Announcement extends Model
{

    protected $fillable = [
        'content',
        'user_id',
        'title'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }
    
    public function tugas(): BelongsTo
    {
        return $this->belongsTo(Tugas::class);
    }
}