<?php

namespace App\Http\Controllers;

use App\Events\PostUpdated;
use App\Exports\PostsExport;
use App\Http\Requests\PostRequest;
use App\Http\Requests\PublishPostRequest;
use App\Http\Requests\SearchRequest;
use App\Http\Resources\PostResource;
use App\Jobs\PublishPost;
use App\Models\Post;
use App\Models\Tag;
use Carbon\Carbon;
use http\Env\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class PostController extends Controller
{
    public function index()
    {
        // Get all posts
        $user = auth()->user();

        // Get the posts visible to user
        $posts = Post::getVisiblePosts($user);

        return PostResource::collection($posts);
    }

    public function show(Post $post)
    {
        $post->load(['author:id,first_name,last_name', 'tags:title']);

        $comments = $post->comments()
            ->with([
                'user:id,first_name,last_name',
                'children' => function ($query) {
                $query->take(1);}])

            ->paginate(2);

        $post->setRelation('comments', $comments);

        return new PostResource($post);
    }

    public function store(PostRequest $request)
    {
        $tagTitles = $request->validated('tags');

        // Split the tags
        $tagTitles = array_filter($tagTitles, function ($tag) {
            return is_string($tag) && !empty(trim($tag));
        });

        $user = auth()->user();

        $data = [
            'title' => $request->title,
            'body' => $request->body,
        ];

        $post = Post::create($user, $data);

        // Create tags
        $post->syncTags($tagTitles);

        return new PostResource($post->load('author', 'tags'));
    }

    public function update(PostRequest $request, Post $post)
    {
        $data = [
            'title' => $request->title,
            'body' => $request->body,
            'is_published' => false,
        ];

        $post->update($data);

        event(new PostUpdated($post));

        $tagTitles = $request->validated('tags');

        // Split the tags
        $tagTitles = array_filter($tagTitles, function ($tag) {
            return is_string($tag) && !empty(trim($tag));
        });

        // Update the tags
        $post->syncTags($tagTitles);

        return new PostResource($post->load('author', 'tags'));
    }

    public function destroy(Post $post)
    {
        // Detach the tags associated with this post
        $post->tags()->detach();

        event(new PostUpdated($post));

        // Delete the post
        $post->delete();

        // Delete the unused tags
        Tag::doesntHave('posts')->delete();

        return response()->json([
            'message' => 'Post deleted successfully.'
        ], 200);
    }

    public function schedulePublish(PublishPostRequest $request, Post $post)
    {
        if (Post::getScheduledJob($post)) {
            return response()->json([
                'message' => 'This post is already scheduled.',
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
