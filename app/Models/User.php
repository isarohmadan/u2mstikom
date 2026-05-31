<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use App\Notifications\ResetPasswordNotification;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The kelas that belong to the user.
     */
    /*
    // Model App\Models\Kelas does not exist
    public function kelas()
    {
        return $this->belongsToMany(Kelas::class, 'kelas_user', 'user_id', 'kelas_id')
            ->withTimestamps()
            ->withPivot(['created_at', 'updated_at']);
    }
    */

    /**
     * Get the topics created by the user.
     */
    public function topics()
    {
        return $this->hasMany(Topics::class, 'user_id');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'bookmarks'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'bookmarks' => 'array',
    ];

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * Check if user is an administrator
     */
    public function isAdministrator(): bool
    {
        return $this->role === 'administrator';
    }

    /**
     * Check if user is a pengurus
     */
    public function isPengurus(): bool
    {
        return $this->hasRole('pengurus');
    }

    /**
     * Check if user is an anggota
     */
    public function isAnggota(): bool
    {
        return $this->hasRole('anggota');
    }

    /**
     * Check if the user has bookmarked a specific topic
     */
    public function hasBookmarked($topicId)
    {
        $bookmarks = $this->bookmarks ?? [];
        $topicBookmarks = $bookmarks['topics'] ?? [];
        return in_array($topicId, $topicBookmarks);
    }

    /**
     * Toggle bookmark for a topic
     */
    public function toggleBookmark($topicId)
    {
        $bookmarks = $this->bookmarks ?? [];
        $topicBookmarks = $bookmarks['topics'] ?? [];

        if (in_array($topicId, $topicBookmarks)) {
            $topicBookmarks = array_diff($topicBookmarks, [$topicId]);
        } else {
            $topicBookmarks[] = $topicId;
        }

        $bookmarks['topics'] = array_values($topicBookmarks);
        $this->bookmarks = $bookmarks;
        $this->save();

        return in_array($topicId, $bookmarks['topics']);
    }

    /**
     * Get bookmarked topics
     */
    public function getBookmarkedTopics()
    {
        $bookmarks = $this->bookmarks ?? [];
        $topicIds = $bookmarks['topics'] ?? [];

        if (empty($topicIds)) {
            return collect([]);
        }

        return Topics::whereIn('id', $topicIds)->get(); // Note: Topics model is plural, based on file check
    }
}
