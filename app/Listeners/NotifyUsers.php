<?php

namespace App\Listeners;

use App\Events\PostPublished;
use App\Models\Post;
use App\Models\User;
use App\Notifications\PublishNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class NotifyUsers
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(PostPublished $event): void
    {
        $post = $event->post;

        $users = User::where('id', '!=', $post->author_id)->get();

       Notification::send($users, new PublishNotification($post));
    }
}
