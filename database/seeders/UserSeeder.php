<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Enums\Role;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\UserRole;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()
            ->has(UserProfile::factory()->count(1)->state([
                'first_name' => 'Ignacio',
                'last_name' => 'Zamora',
                'active' => 1,
            ]))
            ->has(UserRole::factory()->count(1)->state([
                'role' => Role::ADMIN->value,
            ]))
            ->create([
                'name' => 'nachopitt',
                'email' => 'nachopitt@gmail.com',
                'password' => Hash::make('coronado'),
            ]);
    }
}
