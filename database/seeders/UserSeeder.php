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
        $adminName = env('INITIAL_ADMIN_NAME', 'admin');
        $adminEmail = env('INITIAL_ADMIN_EMAIL', 'admin@example.com');
        $adminPassword = env('INITIAL_ADMIN_PASSWORD', 'change-this-password');
        $firstName = env('INITIAL_ADMIN_FIRST_NAME', 'Admin');
        $lastName = env('INITIAL_ADMIN_LAST_NAME', 'User');

        $user = User::firstOrCreate(
            ['email' => $adminEmail],
            [
                'name' => $adminName,
                'password' => Hash::make($adminPassword),
            ]
        );

        UserProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'active' => 1,
            ]
        );

        UserRole::updateOrCreate(
            ['user_id' => $user->id],
            ['role' => Role::ADMIN->value]
        );
    }
}
