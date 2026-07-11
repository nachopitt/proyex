<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\ProjectUpdate;
use App\Models\User;
use App\Enums\Status;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectUpdateControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
    }

    /**
     * Test storing a project update successfully.
     */
    public function test_project_update_can_be_stored(): void
    {
        $project = Project::factory()->create([
            'current_status' => Status::PLANNED->value,
            'current_progress_percentage' => 10,
        ]);

        $payload = [
            'description' => 'Working on the setup phase.',
            'project' => [
                'current_status' => Status::IN_PROGRESS->value,
                'current_progress_percentage' => 30,
            ],
        ];

        $response = $this->actingAs($this->user)
            ->post(route('projects.updates.store', $project), $payload);

        $response->assertRedirect(route('projects.show', $project));
        $response->assertSessionHas('success', 'Project update logged successfully.');

        $project->refresh();
        $this->assertEquals(Status::IN_PROGRESS, $project->current_status);
        $this->assertEquals(30, $project->current_progress_percentage);

        $this->assertDatabaseHas('project_updates', [
            'project_id' => $project->id,
            'description' => 'Working on the setup phase.',
            'status' => Status::IN_PROGRESS->value,
            'progress_percentage' => 30,
            'updater_user_id' => $this->user->id,
        ]);
    }

    /**
     * Test editing a project update page.
     */
    public function test_edit_page_can_be_rendered(): void
    {
        $project = Project::factory()->create();
        $update = ProjectUpdate::factory()->create([
            'project_id' => $project->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('updates.edit', $update));

        $response->assertStatus(200);
    }

    /**
     * Test updating a project update description.
     */
    public function test_project_update_can_be_updated(): void
    {
        $project = Project::factory()->create();
        $update = ProjectUpdate::factory()->create([
            'project_id' => $project->id,
            'description' => 'Old description',
        ]);

        $payload = [
            'description' => 'New updated description',
        ];

        $response = $this->actingAs($this->user)
            ->put(route('updates.update', $update), $payload);

        $response->assertRedirect(route('projects.show', $project));
        $response->assertSessionHas('success', 'Project update edited successfully.');

        $this->assertDatabaseHas('project_updates', [
            'id' => $update->id,
            'description' => 'New updated description',
        ]);
    }

    /**
     * Test deleting a project update.
     */
    public function test_project_update_can_be_deleted(): void
    {
        $project = Project::factory()->create();
        $update = ProjectUpdate::factory()->create([
            'project_id' => $project->id,
        ]);

        $response = $this->actingAs($this->user)
            ->delete(route('updates.destroy', $update));

        $response->assertRedirect(route('projects.show', $project));
        $response->assertSessionHas('success', 'Project update deleted successfully.');
        $this->assertSoftDeleted('project_updates', ['id' => $update->id]);
    }
}
