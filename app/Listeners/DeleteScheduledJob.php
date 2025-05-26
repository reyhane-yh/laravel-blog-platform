<?php

namespace App\Listeners;

use App\Models\Post;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DeleteScheduledJob
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
    public function handle($event): void
    {
        $post = $event->post;

        $jobId = Post::getScheduledJob($post);

        if ($jobId) {
            // Delete the job
            DB::table('jobs')->where('id', $jobId)->delete();
            Log::info("Deleted scheduled job with ID {$jobId} for post ID {$post->id}.");
            return;
        }

        Log::warning("No scheduled jobs found for post ID {$post->id}.");
    }
}
