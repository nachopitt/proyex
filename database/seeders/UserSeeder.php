<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Enums\Role;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'nachopitt',
                'email' => 'nachopitt@gmail.com',
                'password' => Hash::make('coronado'),
                'userProfile' => [
                    'first_name' => 'Ignacio',
                    'last_name' => 'Zamora',
                    'active' => 1,
                ],
                'userRoles' => [
                    [
                        'role' => Role::ADMIN
                    ]
                ]
            ],
        ];

        foreach ($users as $user) {
            DB::transaction(function () use ($user) {
                $newUser = User::create(Arr::except($user, ['userProfile', 'userRoles']));

                $newUser->userProfile()->create($user['userProfile']);

                foreach ($user['userRoles'] as $userRole) {
                    $newUser->userRoles()->create($userRole);
                }
            });
        }
    }
}
