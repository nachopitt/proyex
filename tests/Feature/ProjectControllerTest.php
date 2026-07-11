<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Tag;
use App\Models\User;
use App\Enums\Priority;
use App\Enums\Status;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a verified user for authentication
        $this->user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
    }

    /**
     * Test guest users are redirected to login.
     */
    public function test_guest_is_redirected(): void
    {
        $response = $this->get(route('projects.index'));
        $response->assertRedirect(route('login'));
    }

    /**
     * Test index page rendering.
     */
    public function test_index_page_can_be_rendered(): void
    {
        $project = Project::factory()->create();

        $response = $this->actingAs($this->user)
            ->get(route('projects.index'));

        $response->assertStatus(200);
        $response->assertSee($project->title);
    }

    /**
     * Test project creation form rendering.
     */
    public function test_create_page_can_be_rendered(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('projects.create'));

        $response->assertStatus(200);
    }

    /**
     * Test storing a project successfully.
     */
    public function test_project_can_be_stored(): void
    {
        $assignedUser = User::factory()->create();

        $payload = [
            'title' => 'New Awesome Project',
            'description' => 'Detailed description of the awesome project.',
            'start_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(10)->format('Y-m-d'),
            'current_status' => Status::PLANNED->value,
            'priority' => Priority::MEDIUM->value,
            'current_progress_percentage' => 0,
            'assigned_user_id' => $assignedUser->id,
            'reporter_user_id' => $this->user->id,
            'tags' => ['frontend', 'typescript'],
        ];

        $response = $this->actingAs($this->user)
            ->post(route('projects.store'), $payload);

        $this->assertDatabaseHas('projects', [
            'title' => 'New Awesome Project',
            'priority' => Priority::MEDIUM->value,
        ]);

        $project = Project::where('title', 'New Awesome Project')->firstOrFail();
        $response->assertRedirect(route('projects.show', $project));
        $response->assertSessionHas('success', 'Project created successfully.');

        // Verify tags are created and synchronized
        $this->assertDatabaseHas('tags', ['name' => 'frontend']);
        $this->assertDatabaseHas('tags', ['name' => 'typescript']);
        $this->assertCount(2, $project->tags);

        // Verify automatic project update log
        $this->assertDatabaseHas('project_updates', [
            'project_id' => $project->id,
            'description' => 'Project created.',
            'updater_user_id' => $this->user->id,
        ]);
    }

    /**
     * Test showing a project.
     */
    public function test_project_can_be_shown(): void
    {
        $project = Project::factory()->create();

        $response = $this->actingAs($this->user)
            ->get(route('projects.show', $project));

        $response->assertStatus(200);
    }

    /**
     * Test editing a project form.
     */
    public function test_edit_page_can_be_rendered(): void
    {
        $project = Project::factory()->create();

        $response = $this->actingAs($this->user)
            ->get(route('projects.edit', $project));

        $response->assertStatus(200);
    }

    /**
     * Test updating a project.
     */
    public function test_project_can_be_updated(): void
    {
        $project = Project::factory()->create([
            'title' => 'Old Title',
            'current_status' => Status::PLANNED->value,
        ]);

        $payload = [
            'title' => 'Updated Title',
            'description' => 'Updated description.',
            'start_date' => \Illuminate\Support\Carbon::parse($project->start_date)->format('Y-m-d'),
            'due_date' => \Illuminate\Support\Carbon::parse($project->due_date)->format('Y-m-d'),
            'current_status' => Status::IN_PROGRESS->value,
            'priority' => Priority::HIGH->value,
            'current_progress_percentage' => 30,
            'assigned_user_id' => $project->assigned_user_id,
            'reporter_user_id' => $project->reporter_user_id,
            'tags' => ['backend'],
        ];

        $response = $this->actingAs($this->user)
            ->put(route('projects.update', $project), $payload);

        $response->assertRedirect(route('projects.show', $project));
        $response->assertSessionHas('success', 'Project updated successfully.');

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'title' => 'Updated Title',
            'current_status' => Status::IN_PROGRESS->value,
        ]);

        $project->refresh();
        $this->assertCount(1, $project->tags);
        $this->assertEquals('backend', $project->tags->first()->name);
    }

    /**
     * Test deleting a project.
     */
    public function test_project_can_be_deleted(): void
    {
        $project = Project::factory()->create();

        $response = $this->actingAs($this->user)
            ->delete(route('projects.destroy', $project));

        $response->assertRedirect(route('projects.index'));
        $response->assertSessionHas('success', 'Project deleted successfully.');
        $this->assertSoftDeleted('projects', ['id' => $project->id]);
    }
}
