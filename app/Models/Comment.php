<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use PhpParser\Node\Expr\Array_;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_id',
        'user_id',
        'parent_id',
        'body'
    ];

    public function post()
    {
        return $this->belongsTo(
            Post::class);
    }

    public function user()
    {
        return $this->belongsTo(
            User::class);
    }

    // Parent comment
    public function parent()
    {
        return $this->belongsTo(
            Comment::class,
            'parent_id');
    }

    // Child comment
    public function children()
    {
        return $this->hasMany(
            Comment::class,
            'parent_id',
            'id');
    }

    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public static function createComment(User $user, Post $post, $data)
    {
        $comment = $post->comments()->make([
            'user_id' => $user->id,
            'body' => $data['body'],
        ]);

        // Check if the comment is replied to a parent comment
        if (isset($data['parent_id'])) {
            $comment->parent_id = $data['parent_id'];
        }

        $comment->save();
        return $comment;
    }

}
