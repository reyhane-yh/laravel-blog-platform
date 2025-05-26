<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PostPolicy
{
    public function view(?User $user, Post $post)
    {
        if ($user) {
            Log::info('User ID: ' . $user->id . $user->is_admin);
        } else {
            Log::warning('User is unauthenticated.');
        }

        // Unauthenticated users can only see published posts
        if (!$user) {
            return $post->is_published;
        }

        // Admins can see all posts
        if ($user->is_admin) {
            return true;
        }

        return $post->is_published || $post->author_id === $user->id;
    }
    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Post $post): bool
    {
        return $user->id === $post->author_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Post $post): bool
    {
        return $user->id === $post->author_id;
    }

    public function schedule(User $user, Post $post)
    {
        return $user->id === $post->author_id;
    }
}
