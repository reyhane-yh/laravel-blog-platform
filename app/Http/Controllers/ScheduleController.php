<?php

namespace App\Http\Controllers;

use App\Http\Requests\PublishPostRequest;
use App\Jobs\PublishPost;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function __invoke(PublishPostRequest $request, Post $post)
    {
        if (Post::getScheduledJob($post) || $post->isPublished) {
            return response()->json([
                'message' => 'This post is already published or scheduled.',
            ], 409);
        }

        $data =  $request->validated();

        $publishAt = Carbon::parse($data['publish_at']);
        PublishPost::dispatch($post)->delay($publishAt);

        return response()->json([
            'message' => 'Post scheduled for publication at ' . $publishAt,
        ]);
    }
}
