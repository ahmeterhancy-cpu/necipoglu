<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\View\View;

class PostController extends Controller
{
    public function index(): View
    {
        return view('pages.blog.index', [
            'posts' => Post::query()->published()->latestFirst()->paginate(12),
        ]);
    }

    public function show(Post $post): View
    {
        abort_unless($post->is_published && $post->published_at?->isPast(), 404);

        return view('pages.blog.show', [
            'post' => $post,
            'others' => Post::query()
                ->published()
                ->whereKeyNot($post->getKey())
                ->latestFirst()
                ->take(3)
                ->get(),
        ]);
    }
}
