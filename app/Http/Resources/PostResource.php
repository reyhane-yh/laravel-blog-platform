<?php

namespace App\Http\Resources;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        $response =  [
            'post_id' => $this->id,
            'title' => $this->title,
            'body' => $this->body,
            'author' => [
                'id' => $this->author->id,
                'first_name' => $this->author->first_name,
                'last_name' => $this->author->last_name,
            ],
            'like_count' => $this->likes_count ?? 0,
            'tags' => $this->tags->pluck('title')->implode(', '),
            'comment_count' => $this->comments_count ?? 0,
            'comments' => CommentResource::collection($this->whenLoaded('comments')),
        ];

        // Check if comments are paginated
        if ($this->comments instanceof LengthAwarePaginator) {
            $response['pagination'] = [
                'current_page' => $this->comments->currentPage(),
                'last_page' => $this->comments->lastPage(),
                'total' => $this->comments->total(),
            ];
        }

        return $response;
    }
}
