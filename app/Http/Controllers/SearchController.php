<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;

class SearchController extends Controller
{
    public function __invoke(SearchRequest $request)
    {
        // Split the search terms
        $terms = explode(' ', $request->get('q'));

        $posts = Post::search($terms);

        if ($posts->isEmpty()) {
            return response()->json([
                'message' => 'No posts found.'
            ], 404);
        }

        return PostResource::collection($posts);
    }
}
