<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\View\View;

class VideoController extends Controller
{
    public function index(): View
    {
        $videos = Video::query()->active()->ordered()->get();

        return view('pages.tv', [
            'featured' => $videos->firstWhere('is_featured', true) ?? $videos->first(),
            'videos' => $videos,
        ]);
    }
}
