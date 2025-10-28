<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;

class CommentPolicy
{
    public function before(User $user)
    {
        if ($user->hasRole('Admin')) return true;
    }

    public function create(User $user): bool { return true; }

    public function delete(User $user, Comment $comment): bool
    {
        return $comment->user_id === $user->id;
    }
}

