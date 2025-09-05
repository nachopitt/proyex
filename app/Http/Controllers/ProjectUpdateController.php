<?php

namespace App\Http\Controllers;

use App\Enums\Status;
use App\Http\Requests\StoreProjectUpdateRequest;
use App\Http\Requests\UpdateProjectUpdateRequest;
use App\Models\Project;
use App\Models\ProjectUpdate;
use Inertia\Inertia;

class ProjectUpdateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Project $project)
    {
        return Inertia::render('Projects/Update/Index', [
            'project' => $project,
            'updates' => $project->projectUpdates()->latest()->paginate(10),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Project $project)
    {
        return Inertia::render('Projects/Update/Create', [
            'project' => $project,
            'statuses' => Status::asArray(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProjectUpdateRequest $request, Project $project)
    {
        $validated = $request->validated();

        $project->projectUpdates()->create($validated + ['updater_id' => auth()->id()]);

        return redirect()->route('projects.show', $project);
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project, ProjectUpdate $update)
    {
        // TODO: do something with this
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project, ProjectUpdate $update)
    {
        return Inertia::render('Projects/Update/Edit', [
            'project' => $project,
            'update' => $update,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProjectUpdateRequest $request, Project $project, ProjectUpdate $update)
    {
        $validated = $request->validated();

        $update->update($validated);

        return redirect()->route('projects.show', $project);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project, ProjectUpdate $update)
    {
        $update->delete();

        return redirect()->route('projects.show', $project);
    }
}
