<?php

namespace Tests\Feature\Admin;

use App\Enums\Role;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin user (UserProfile is created automatically via UserFactory afterCreating)
        $this->admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
        ]);
        UserRole::factory()->create([
            'user_id' => $this->admin->id,
            'role' => Role::ADMIN->value,
        ]);

        // Create regular user (UserProfile is created automatically via UserFactory afterCreating)
        $this->regularUser = User::factory()->create([
            'name' => 'Regular User',
            'email' => 'user@test.com',
        ]);
        UserRole::factory()->create([
            'user_id' => $this->regularUser->id,
            'role' => Role::USER->value,
        ]);
    }

    public function test_guest_cannot_access_admin_routes(): void
    {
        $response = $this->get(route('admin.users.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_access_admin_routes(): void
    {
        $response = $this->actingAs($this->regularUser)->get(route('admin.users.index'));
        
        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('warning', 'You do not have administrative access.');
    }

    public function test_admin_can_access_admin_routes(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.users.index'));
        $response->assertOk();
    }

    public function test_admin_can_update_user_role(): void
    {
        $response = $this->actingAs($this->admin)->put(route('admin.users.update', $this->regularUser), [
            'role' => Role::ADMIN->value,
        ]);

        $response->assertRedirect();
        $this->assertTrue($this->regularUser->fresh()->isAdmin());
    }

    public function test_admin_cannot_demote_themselves(): void
    {
        $response = $this->actingAs($this->admin)->put(route('admin.users.update', $this->admin), [
            'role' => Role::USER->value,
        ]);

        $response->assertSessionHasErrors(['error']);
        $this->assertTrue($this->admin->fresh()->isAdmin());
    }

    public function test_admin_can_toggle_user_active_status(): void
    {
        $response = $this->actingAs($this->admin)->put(route('admin.users.update', $this->regularUser), [
            'active' => false,
        ]);

        $response->assertRedirect();
        $this->assertFalse($this->regularUser->fresh()->userProfile->active);
    }

    public function test_admin_cannot_deactivate_themselves(): void
    {
        $response = $this->actingAs($this->admin)->put(route('admin.users.update', $this->admin), [
            'active' => false,
        ]);

        $response->assertSessionHasErrors(['error']);
        $this->assertTrue($this->admin->fresh()->userProfile->active);
    }

    public function test_deactivated_user_cannot_login(): void
    {
        $this->regularUser->userProfile->update(['active' => false]);

        $response = $this->post(route('login'), [
            'email' => $this->regularUser->email,
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertFalse(\Illuminate\Support\Facades\Auth::check());
    }

    public function test_deactivated_logged_in_user_is_logged_out_on_next_request(): void
    {
        // Log in the regular user
        $this->actingAs($this->regularUser);

        // Deactivate them
        $this->regularUser->userProfile->update(['active' => false]);

        // Attempt to access dashboard
        $response = $this->get(route('dashboard'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors(['email']);
        $this->assertFalse(\Illuminate\Support\Facades\Auth::check());
    }

    public function test_admin_can_search_users_by_name_and_email(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.users.index', ['search' => 'Regular']));
        $response->assertOk();
        
        $inertiaData = $response->original->getData()['page']['props']['users']['data'];
        $this->assertCount(1, $inertiaData);
        $this->assertEquals($this->regularUser->id, $inertiaData[0]['id']);
    }

    public function test_admin_can_filter_users_by_role_and_active_status(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.users.index', [
            'role' => Role::ADMIN->value,
            'active' => 'true'
        ]));
        $response->assertOk();

        $inertiaData = $response->original->getData()['page']['props']['users']['data'];
        $this->assertCount(1, $inertiaData);
        $this->assertEquals($this->admin->id, $inertiaData[0]['id']);
    }
}
