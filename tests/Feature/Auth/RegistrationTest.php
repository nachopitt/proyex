<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_registration_screen()
    {
        $response = $this->get(route('register'));

        $response->assertRedirect(route('login', absolute: false));
    }

    public function test_authenticated_user_can_view_registration_screen()
    {
        $admin = User::factory()->create();

        $response = $this->actingAs($admin)->get(route('register'));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('auth/Register')
        );
    }

    public function test_authenticated_user_can_register_new_users()
    {
        $admin = User::factory()->create();

        $response = $this->actingAs($admin)->post(route('register.store'), [
            'name' => 'Test User',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticatedAs($admin);
        $response->assertRedirect(route('dashboard', absolute: false));

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'name' => 'Test User',
        ]);

        $user = User::where('email', 'test@example.com')->first();
        $this->assertNotNull($user->userProfile);
        $this->assertSame('John', $user->userProfile->first_name);
        $this->assertSame('Doe', $user->userProfile->last_name);
    }
}
