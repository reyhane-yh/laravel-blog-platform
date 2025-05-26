<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = ['title'];
    public $timestamps = false;

    public function posts()
    {
        return $this->belongsToMany(
            Post::class,
            'post_tags',
            'tag_id',
            'post_id');
    }

    public static function findOrCreateTags(array $tagTitles): array
    {
        $tagIds = [];

        foreach ($tagTitles as $title) {
            $tag = Tag::firstOrCreate(['title' => $title]);
            $tagIds[] = $tag->id;
        }

        return $tagIds;
    }

    public static function deleteUnusedTags(array $tagIds)
    {
        if (empty($tagIds)) {
            return;
        }

        static::whereIn('id', $tagIds)
            ->doesntHave('posts')
            ->delete();
    }

    public static function TagList()
    {
        return Tag::withCount('posts')
            ->get()
            ->map(function ($tag) {
                return [
                    'title' => $tag->title,
                    'post_count' => $tag->posts_count,
                ];
            });
    }
}
