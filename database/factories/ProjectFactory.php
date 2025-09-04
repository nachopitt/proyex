<?php

namespace Database\Factories;

use App\Enums\Priority;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'description' => fake()->text(),
            'priority' => fake()->numberBetween(Priority::LOW->value, Priority::HIGH->value),
            'start_date' => fake()->date(),
            'due_date' => fake()->date(),
            'end_date' => fake()->date(),
            'parent_id' => null,
            'reporter_id' => User::all()->random()->id,
            'owner_id' => User::all()->random()->id,
        ];
    }
}
