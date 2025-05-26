<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(CommentRequest $request, Post $post)
    {
        $data = $request->validated();
        $user = Auth::user();

        $comment = Comment::createComment($user, $post, $data);

        return new CommentResource($comment->load('user', 'children.user'));
    }

    public function show(Post $post, Comment $comment)
    {
        return new CommentResource($comment->load('user', 'children.user'));
    }

    public function update(CommentRequest $request, Post $post, Comment $comment)
    {
        $data = $request->validated();
        $comment->update($data);

        return new CommentResource($comment->load('user'));
    }

    public function destroy(Comment $comment)
    {
        $comment->delete();

        return response()->json([
            'message' => 'Comment deleted successfully.']);
    }

    public function storeReply(CommentRequest $request, Post $post, Comment $comment)
    {
        $data = $request->validated();
        $user = Auth::user();

        $data['parent_id'] = $comment->id;

        $reply = Comment::createComment($user, $post, $data);

        return new CommentResource($reply->load('user'));
    }
}
