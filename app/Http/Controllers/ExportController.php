<?php

namespace App\Http\Controllers;

use App\Exports\PostsExport;
use App\Models\Post;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    public function __invoke()
    {
        $posts = Post::getAllPosts();
        return Excel::download(new PostsExport($posts), 'posts.xlsx');
    }
}
