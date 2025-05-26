<?php

namespace App\Http\Controllers;

use App\Models\Tag;

class TagController extends Controller
{
    public function __invoke()
    {
        $tags = Tag::TagList();

        return response()->json([
            'tags' => $tags
        ]);
    }

}
