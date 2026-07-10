<?php

namespace Tests\Feature;

use App\Enums\Status;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeedValidationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the baseline seed outputs the expected structures, counts, and invariants.
     */
    public function test_baseline_seed_invariants()
    {
        $this->seed();

        // 1. Assert project counts from the baseline seed
        $topLevelProjectsCount = Project::whereNull('parent_id')->count();
        $subtasksCount = Project::whereNotNull('parent_id')->count();

        // Baseline has exactly 11 top-level projects and 8 subtasks
        $this->assertEquals(11, $topLevelProjectsCount, "Should have exactly 11 baseline top-level projects.");
        $this->assertEquals(8, $subtasksCount, "Should have exactly 8 baseline subtasks.");

        // 2. Assert upcoming deadlines on the dashboard
        $today = now()->toDateString();
        $inactiveStatuses = [Status::COMPLETED, Status::CANCELLED];

        $upcomingCount = Project::whereNull('parent_id')
            ->whereNotIn('current_status', $inactiveStatuses)
            ->whereNotNull('due_date')
            ->where('due_date', '>=', $today)
            ->count();

        $this->assertGreaterThanOrEqual(5, $upcomingCount, "Should yield at least 5 active top-level deadlines.");

        // 3. Assert all project records adhere strictly to temporal and status constraints
        $allProjects = Project::all();

        foreach ($allProjects as $project) {
            // start_date <= due_date
            if ($project->start_date && $project->due_date) {
                $this->assertTrue(
                    $project->start_date <= $project->due_date,
                    "Project ID {$project->id} ('{$project->title}') has start_date ({$project->start_date}) after due_date ({$project->due_date})."
                );
            }

            // Completed & Cancelled status logic
            if (in_array($project->current_status, [Status::COMPLETED, Status::CANCELLED])) {
                $this->assertNotNull(
                    $project->end_date,
                    "Project ID {$project->id} ('{$project->title}') is completed/cancelled but has null end_date."
                );

                if ($project->start_date) {
                    $this->assertTrue(
                        $project->start_date <= $project->end_date,
                        "Project ID {$project->id} ('{$project->title}') has start_date ({$project->start_date}) after end_date ({$project->end_date})."
                    );
                }
            } else {
                // Active projects MUST have null end_date
                $this->assertNull(
                    $project->end_date,
                    "Project ID {$project->id} ('{$project->title}') is active ({$project->current_status->value}) but has non-null end_date ({$project->end_date})."
                );
            }

            // Progress percentage logic
            if ($project->current_status === Status::PLANNED) {
                $this->assertEquals(0, $project->current_progress_percentage, "Planned project must have 0% progress.");
            } elseif ($project->current_status === Status::COMPLETED) {
                $this->assertEquals(100, $project->current_progress_percentage, "Completed project must have 100% progress.");
            }
        }

        // 4. Assert updates are chronologically and logically consistent
        foreach ($allProjects as $project) {
            $updates = $project->projectUpdates()->orderBy('created_at', 'asc')->get();
            $lastProgress = -1;
            $lastCreatedAt = null;

            foreach ($updates as $update) {
                // Progress values should be non-decreasing over time
                $this->assertGreaterThanOrEqual(
                    $lastProgress,
                    $update->progress_percentage,
                    "Update ID {$update->id} for project {$project->id} has a progress percentage that decreased."
                );

                // Timestamps should be chronological
                if ($lastCreatedAt !== null) {
                    $this->assertTrue(
                        $update->created_at >= $lastCreatedAt,
                        "Update ID {$update->id} for project {$project->id} is out of chronological order."
                    );
                }

                $lastProgress = $update->progress_percentage;
                $lastCreatedAt = $update->created_at;
            }
        }

        // 5. Assert all Dashboard KPI cards are non-zero
        $admin = User::where('email', env('INITIAL_ADMIN_EMAIL', 'admin@example.com'))->first();
        $this->assertNotNull($admin, "Admin user must be seeded.");

        $response = $this->actingAs($admin)->get(route('dashboard'));
        $response->assertStatus(200);

        $response->assertInertia(fn ($page) => $page
            ->component('Dashboard')
            ->where('stats.active_projects', fn ($val) => $val > 0)
            ->where('stats.completed_projects', fn ($val) => $val > 0)
            ->where('stats.overdue_projects', fn ($val) => $val > 0)
            ->where('stats.total_subtasks', fn ($val) => $val > 0)
            ->where('stats.completion_rate', fn ($val) => $val > 0)
        );
    }
}
