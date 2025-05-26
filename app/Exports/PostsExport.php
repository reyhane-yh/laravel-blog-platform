<?php

namespace App\Exports;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PostsExport implements FromCollection, WithHeadings
{
    public function __construct(private Collection $posts)
    {

    }
    public function collection()
    {
        return $this->posts
            ->map(function ($post) {
                return [
                    'post_id' => $post->id,
                    'title' => $post->title,
                    'body' => $post->body,
                    'author_id' => $post->author->id,
                    'author_first_name' => $post->author->first_name,
                    'author_last_name' => $post->author->last_name,
                    'like_count' => $post->likes_count,
                    'tags' => $post->tags->pluck('title')->implode(', '),
                    'comment_count' => $post->comments_count,
                    'created_at' => $post->created_at,
                    'updated_at' => $post->updated_at,
                ];
            });
    }

    public function headings(): array
    {
        return [
            'post_id',
            'title',
            'body',
            'author_id',
            'author_first_name',
            'author_last_name',
            'like_count',
            'tags',
            'comment_count',
            'created_at',
            'updated_at'
            ];
    }
}
