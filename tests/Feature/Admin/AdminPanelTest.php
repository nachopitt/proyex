<?php

namespace Tests\Feature\Admin;

use App\Enums\Role;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin user
        $this->admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
        ]);
        UserRole::factory()->create([
            'user_id' => $this->admin->id,
            'role' => Role::ADMIN->value,
        ]);

        // Create regular user
        $this->regularUser = User::factory()->create([
            'name' => 'Regular User',
            'email' => 'user@test.com',
        ]);
        UserRole::factory()->create([
            'user_id' => $this->regularUser->id,
            'role' => Role::USER->value,
        ]);
    }

    public function test_guest_cannot_access_admin_dashboard(): void
    {
        $response = $this->get(route('admin.dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_access_admin_dashboard(): void
    {
        $response = $this->actingAs($this->regularUser)->get(route('admin.dashboard'));
        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('warning', 'You do not have administrative access.');
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page->component('admin/Dashboard'));
    }

    public function test_guest_cannot_access_audit_logs(): void
    {
        $response = $this->get(route('admin.logs.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_access_audit_logs(): void
    {
        $response = $this->actingAs($this->regularUser)->get(route('admin.logs.index'));
        $response->assertRedirect(route('dashboard'));
    }

    public function test_admin_can_access_audit_logs(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.logs.index'));
        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page->component('admin/Logs'));
    }

    public function test_non_admin_cannot_trigger_clear_cache(): void
    {
        $response = $this->actingAs($this->regularUser)->post(route('admin.actions.clear-cache'));
        $response->assertRedirect(route('dashboard'));
    }

    public function test_admin_can_trigger_clear_cache(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.actions.clear-cache'));
        $response->assertRedirect();
        $response->assertSessionHas('success', 'Application cache cleared successfully!');
    }
}
