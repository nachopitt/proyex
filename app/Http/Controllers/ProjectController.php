<?php

namespace App\Http\Controllers;

use App\Enums\Priority;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use Inertia\Inertia;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projects = Project::latest()
            ->with(['reporter', 'owner'])
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
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProjectRequest $request)
    {
        $validated = $request->validated();
        $validated['reporter_id'] = auth()->id();

        if (empty($validated['owner_id'])) {
            $validated['owner_id'] = auth()->id();
        }

        $project = Project::create($validated);

        return redirect()->route('projects.show', $project);
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        $project->load(['tags', 'reporter', 'owner', 'projectUpdates.updater']);

        return Inertia::render('projects/Show', [
            'project' => $project,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        return Inertia::render('projects/Edit', [
            'project' => $project,
            'priorities' => Priority::asArray(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProjectRequest $request, Project $project)
    {
        $project->update($request->validated());

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
