<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(): View
    {
        return view('pages.projects.index', [
            'projects' => Project::query()->active()->ordered()->get(),
        ]);
    }

    public function show(Project $project): View
    {
        abort_unless($project->is_active, 404);

        return view('pages.projects.show', [
            'project' => $project,
            'others' => Project::query()
                ->active()
                ->whereKeyNot($project->getKey())
                ->ordered()
                ->take(3)
                ->get(),
        ]);
    }
}
