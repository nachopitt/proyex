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
            'status' => fake()->numberBetween(Status::PLANNED->value, Status::CANCELLED->value),
            'progress_percentage' => fake()->numberBetween(0, 100),
            'project_id' => Project::all()->random()->id,
            'updater_user_id' => User::all()->random()->id,
        ];
    }
}
