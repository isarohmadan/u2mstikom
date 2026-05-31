<?php

namespace App\Policies;

use App\Models\Kelas;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class KelasPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasRole('pengurus') || $user->hasRole('administrator');
    }

    public function view(User $user, Kelas $kelas)
    {
        return $user->id === $kelas->user_id || $user->hasRole('administrator');
    }

    public function create(User $user)
    {
        return $user->hasRole('pengurus') || $user->hasRole('administrator');
    }

    public function update(User $user, Kelas $kelas)
    {
        return $user->id === $kelas->user_id || $user->hasRole('administrator');
    }

    public function delete(User $user, Kelas $kelas)
    {
        return $user->id === $kelas->user_id || $user->hasRole('administrator');
    }

    public function restore(User $user, Kelas $kelas)
    {
        return $user->hasRole('administrator');
    }

    public function forceDelete(User $user, Kelas $kelas)
    {
        return $user->hasRole('administrator');
    }

    public function manageMembers(User $user, Kelas $kelas)
    {
        return $user->id === $kelas->user_id || $user->hasRole('administrator');
    }
}
