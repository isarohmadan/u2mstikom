<?php

namespace App\Policies;

use App\Models\Material;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MaterialPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Material $material)
    {
        return $user->id === $material->uploaded_by || $user->hasRole('administrator');
    }

    public function create(User $user)
    {
        return $user->hasRole('pengurus') || $user->hasRole('administrator');
    }

    public function update(User $user, Material $material)
    {
        return $user->id === $material->uploaded_by || $user->hasRole('administrator');
    }

    public function delete(User $user, Material $material)
    {
        return $user->id === $material->uploaded_by || $user->hasRole('administrator');
    }
}
