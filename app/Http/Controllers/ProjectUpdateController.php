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
     * Store a newly created resource in storage.
     */
    public function store(StoreProjectUpdateRequest $request, Project $project)
    {
        $validated = $request->validated();

        $project->update($validated['project']);

        $project->projectUpdates()->create([
            'description' => $validated['description'],
            'status' => $validated['project']['current_status'],
            'progress_percentage' => $validated['project']['current_progress_percentage'],
            'updater_user_id' => auth()->id()
        ]);

        return redirect()->route('projects.show', $project);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProjectUpdate $update)
    {
        $update->load(['project']);

        return Inertia::render('projects/updates/Edit', [
            'projectUpdate' => $update,
            'statuses' => Status::asArray(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProjectUpdateRequest $request, ProjectUpdate $update)
    {
        $validated = $request->validated();

        $update->update($validated);

        return redirect()->route('projects.show', $update->project_id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProjectUpdate $update)
    {
        $update->delete();

        return redirect()->route('projects.show', $update->project_id);
    }
}
