<?php

namespace App\Http\Controllers;

use App\Enums\Priority;
use App\Enums\Status;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use App\Models\Tag;
use App\Models\User;
use Inertia\Inertia;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projects = Project::latest()
            ->with(['reporterUser', 'assignedUser', 'tags'])
            ->paginate(10);

        return Inertia::render('projects/Index', [
            'projects' => $projects,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('projects/Create', [
            'priorities' => Priority::asArray(),
            'users' => User::all(['id', 'name']),
            'tags' => Tag::all(['id', 'name']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProjectRequest $request)
    {
        $validated = $request->validated();

        $project = Project::create($validated);

        $project->projectUpdates()->create([
            'description' => 'Project created.',
            'status' => $project->current_status,
            'progress_percentage' => $project->current_progress_percentage,
            'updater_user_id' => auth()->id(),
        ]);

        $tagNames = $request->input('tags', []);
        $tagIds = [];

        foreach ($tagNames as $tagName) {
            $tag = Tag::firstOrCreate(['name' => $tagName]);
            $tagIds[] = $tag['id'];
        }

        $project->tags()->sync($tagIds);

        return redirect()->route('projects.show', $project);
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        $project->load(['tags', 'reporterUser', 'assignedUser']);

        $projectUpdates = $project->projectUpdates()
            ->latest()
            ->with('updaterUser')
            ->paginate(10)
            ->withPath(route('projects.show', $project));

        return Inertia::render('projects/Show', [
            'project' => $project,
            'project_updates' => $projectUpdates,
            'statuses' => Status::asArray(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        return Inertia::render('projects/Edit', [
            'project' => $project->load('tags'),
            'priorities' => Priority::asArray(),
            'statuses' => Status::asArray(),
            'users' => User::all(['id', 'name']),
            'tags' => Tag::all(['id', 'name']),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProjectRequest $request, Project $project)
    {
        $project->update($request->validated());

        $tagNames = $request->input('tags', []);
        $tagIds = [];

        foreach ($tagNames as $tagName) {
            $tag = Tag::firstOrCreate(['name' => $tagName]);
            $tagIds[] = $tag['id'];
        }

        $project->tags()->sync($tagIds);

        return redirect()->route('projects.show', $project);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->route('projects.index');
    }
}
