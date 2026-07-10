<?php

namespace Database\Factories;

use App\Enums\Status;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProjectUpdate>
 */
class ProjectUpdateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'description' => fake()->text(),
            'status' => fake()->randomElement(Status::cases()),
            'progress_percentage' => fake()->numberBetween(0, 100),
            'project_id' => fn () => Project::inRandomOrder()->first()?->id ?? Project::factory(),
            'updater_user_id' => fn () => User::inRandomOrder()->first()?->id ?? User::factory(),
        ];
    }
}
