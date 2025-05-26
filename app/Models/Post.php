<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'author_id',
        'title',
        'body',
        'is_published'
    ];

    public function author()
    {
        return $this->belongsTo(
            User::class,
            'author_id');
    }

    public function tags()
    {
        return $this->belongsToMany(
            Tag::class,
            'post_tags',
            'post_id',
            'tag_id');
    }

    public function likes()
    {
        return $this->morphMany(
            Like::class,
            'likeable');
    }

    public function comments()
    {
        return $this->hasMany(
            Comment::class,
            'post_id')
            ->whereNull('parent_id')
            ->with('children.user');
    }

    public function getIsPublishedAttribute()
    {
        return $this->attributes['is_published'];
    }

    public static function getAllPosts()
    {
        return Post::with(['author:id,first_name,last_name',
            'tags:title'])
            ->withCount(['likes', 'comments'])
            ->get();
    }

    public static function getPostsByDate($startDate, $endDate)
    {
        return Post::with(['author:id,first_name,last_name', 'tags:title'])
            ->withCount(['likes', 'comments'])
            ->where('created_at', '>=', $startDate)
            ->where('created_at', '<', $endDate)
            ->get();
    }

    public static function create(User $user, $data)
    {
        return  $user->posts()->create([
            'title' => $data['title'],
            'body' => $data['body'],
            'is_published' => false,
            ]);
    }

    public function syncTags($tagTitles)
    {
        if (empty($tagTitles)) {
            return;
        }

        // Find or create the tags
        $tagIds = Tag::findOrCreateTags($tagTitles);

        // Get the currently associated tags
        $currentTags = $this->tags()->get();

        $tagsToDetach = [];
        foreach ($currentTags as $tag) {
            // Check if the tag is not in the updated list
            if (!in_array($tag->id, $tagIds)) {
                // Add the tag to the list
                $tagsToDetach[] = $tag->id;
            }
        }

        // Sync the new tags
        $this->tags()->sync($tagIds);

        Tag::deleteUnusedTags($tagsToDetach);
    }

    public static function search($terms)
    {
        $query = self::with('author')->withCount('likes');

        $query->where(function ($mainQuery) use ($terms) {
            foreach ($terms as $term) {
                // Add wildcards
                $term = '%' . $term . '%';

                // Search in post titles
                $mainQuery->orWhere('title', 'like', $term)
                // Search in post bodies
                ->orWhere('body', 'like', $term)

                // Search in authors first and last names
                ->orWhereRelation('author', 'first_name', 'like', $term)
                ->orWhereRelation('author', 'last_name', 'like', $term);
            }
        });

        return $query->get();
    }


    public static function scopeVisible(Builder $query, User $user=null)
    {
        // Unauthenticated users can see only published posts
        if (!$user) {
            return $query->where('is_published', true);
        }

        if ($user->is_admin) {
            // Admins can see all posts
            return $query;
        }

        // Authors can see their own unpublished posts
        return $query->where('is_published', true)
            ->orWhere('author_id', $user->id);
    }

    public static function getVisiblePosts(User $user=null)
    {
        return self::visible($user)
            ->with([
                'author:id,first_name,last_name',
                'tags:title',
                'comments' => function ($query) {
                    $query->take(2)
                    ->with([
                        'user:id,first_name,last_name',
                        'children' => function ($query) {
                            $query->take(1)
                            ->with('user:id,first_name,last_name');
                        }
                    ]);
                }
            ])
            ->withCount(['likes', 'comments'])
            ->get();
    }

    public static function getScheduledJob(Post $post)
    {
        // Get all jobs
        $jobs = DB::table('jobs')->get();

        foreach ($jobs as $job) {
            // Decode the payload
            $payloadData = json_decode($job->payload, true);

            $command = unserialize($payloadData['data']['command']);

            // Check if it's a PublishPost job and matches the post id
            if (isset($command->post) && $command->post->id === $post->id) {
                return $job->id;
            }
        }
        return false;
    }
}
