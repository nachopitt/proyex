<?php

namespace Tests\Feature;

use App\Enums\Status;
use App\Models\Project;
use App\Models\ProjectUpdate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_login_page()
    {
        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_users_can_visit_the_dashboard()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('dashboard'));
        $response->assertStatus(200);
        
        $response->assertInertia(fn ($page) => $page
            ->component('Dashboard')
            ->has('stats')
            ->has('recent_updates')
            ->has('upcoming_projects')
            ->has('priority_breakdown')
            ->has('status_breakdown')
        );
    }

    public function test_dashboard_metrics_exclude_cancelled_projects_from_active_overdue_and_upcoming()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $today = now()->startOfDay();

        $plannedFuture = Project::factory()->create([
            'current_status' => Status::PLANNED,
            'parent_id' => null,
            'due_date' => $today->copy()->addDay()->toDateString(),
        ]);

        Project::factory()->create([
            'current_status' => Status::IN_PROGRESS,
            'parent_id' => null,
            'due_date' => $today->copy()->addDays(2)->toDateString(),
        ]);

        Project::factory()->create([
            'current_status' => Status::COMPLETED,
            'parent_id' => null,
            'due_date' => $today->copy()->addDays(3)->toDateString(),
        ]);

        Project::factory()->create([
            'current_status' => Status::CANCELLED,
            'parent_id' => null,
            'due_date' => $today->copy()->addDays(4)->toDateString(),
        ]);

        Project::factory()->create([
            'current_status' => Status::PLANNED,
            'parent_id' => null,
            'due_date' => $today->copy()->subDay()->toDateString(),
        ]);

        Project::factory()->create([
            'current_status' => Status::CANCELLED,
            'parent_id' => null,
            'due_date' => $today->copy()->subDays(2)->toDateString(),
        ]);

        Project::factory()->create([
            'current_status' => Status::PLANNED,
            'parent_id' => $plannedFuture->id,
            'due_date' => $today->copy()->addDays(5)->toDateString(),
        ]);

        ProjectUpdate::factory()->count(6)->create();

        $response = $this->get(route('dashboard'));

        $response->assertStatus(200);

        $response->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard')
            ->where('stats.active_projects', 3)
            ->where('stats.completed_projects', 1)
            ->where('stats.overdue_projects', 1)
            ->where('stats.total_subtasks', 1)
            ->where('stats.completion_rate', 17)
            ->has('upcoming_projects', 2)
            ->has('recent_updates', 5)
            ->has('recent_updates.0.description')
            ->has('recent_updates.0.updater_user.name')
            ->has('recent_updates.0.project.title')
        );
    }
}
