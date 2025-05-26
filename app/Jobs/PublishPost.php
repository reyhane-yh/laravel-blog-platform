<?php

namespace App\Jobs;

use App\Events\PostPublished;
use App\Events\PostUpdated;
use App\Models\Post;
use App\Models\User;
use App\Notifications\PublishNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class PublishPost implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public Post $post)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->post->update(['is_published' => true]);

        log::info('ghable post updated');

        event(new postUpdated($this->post));

        log::info('INJAA');
        event(new PostPublished($this->post));
    }
}
