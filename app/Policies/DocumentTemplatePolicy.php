<?php

namespace App\Policies;

use App\Models\DocumentTemplate;
use App\Models\User;

class DocumentTemplatePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['administrator', 'pengurus', 'anggota']);
    }

    public function view(User $user, DocumentTemplate $template): bool
    {
        return $user->hasAnyRole(['administrator', 'pengurus', 'anggota']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['administrator', 'pengurus']);
    }

    public function update(User $user, DocumentTemplate $template): bool
    {
        return $user->hasAnyRole(['administrator', 'pengurus']);
    }

    public function uploadVersion(User $user, DocumentTemplate $template): bool
    {
        return $user->hasAnyRole(['administrator', 'pengurus']);
    }

    public function download(User $user, DocumentTemplate $template): bool
    {
        return $user->hasAnyRole(['administrator', 'pengurus', 'anggota']);
    }

    public function delete(User $user, DocumentTemplate $template): bool
    {
        return $user->hasRole('administrator');
    }
}


