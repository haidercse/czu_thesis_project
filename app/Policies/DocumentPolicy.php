<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;

class DocumentPolicy
{
    public function view(User $user, Document $document): bool
    {
        return $user->id === $document->user_id || $user->is_admin || $user->hasAnyRole(['Super Admin', 'Admission Officer']);
    }

    public function delete(User $user, Document $document): bool
    {
        return $user->id === $document->user_id;
    }

    public function review(User $user, Document $document): bool
    {
        return $user->is_admin || $user->hasAnyRole(['Super Admin', 'Admission Officer']);
    }
}