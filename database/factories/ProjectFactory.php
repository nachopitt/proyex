<?php

namespace Database\Factories;

use App\Enums\Priority;
use App\Enums\Status;
use App\Models\User;
use App\Models\Project;
use App\Models\ProjectUpdate;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

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
        $status = fake()->randomElement(Status::cases());
        $startDate = fake()->dateTimeBetween('-60 days', '+30 days');
        $dueDate = (clone $startDate)->modify('+' . fake()->numberBetween(7, 90) . ' days');
        
        $endDate = null;
        if (in_array($status, [Status::COMPLETED, Status::CANCELLED])) {
            $endDate = fake()->dateTimeBetween($startDate, $dueDate);
        }

        $progress = match ($status) {
            Status::PLANNED => 0,
            Status::COMPLETED => 100,
            Status::IN_PROGRESS => fake()->numberBetween(10, 90),
            Status::ON_HOLD => fake()->numberBetween(5, 80),
            Status::CANCELLED => fake()->numberBetween(0, 90),
        };

        return [
            'title' => fake()->sentence(),
            'description' => fake()->text(),
            'priority' => fake()->randomElement(Priority::cases()),
            'current_status' => $status,
            'current_progress_percentage' => $progress,
            'start_date' => $startDate->format('Y-m-d'),
            'due_date' => $dueDate->format('Y-m-d'),
            'end_date' => $endDate ? $endDate->format('Y-m-d') : null,
            'parent_id' => null,
            'reporter_user_id' => fn () => User::inRandomOrder()->first()?->id ?? User::factory(),
            'assigned_user_id' => fn () => User::inRandomOrder()->first()?->id ?? User::factory(),
        ];
    }

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterMaking(function (Project $project) {
            // Enforce status and progress consistency
            if ($project->current_status === Status::PLANNED) {
                $project->current_progress_percentage = 0;
                $project->end_date = null;
            } elseif ($project->current_status === Status::COMPLETED) {
                $project->current_progress_percentage = 100;
                if (!$project->end_date) {
                    $project->end_date = $project->due_date ?? $project->start_date ?? now()->toDateString();
                }
            } elseif ($project->current_status === Status::CANCELLED) {
                if (!$project->end_date) {
                    $project->end_date = $project->due_date ?? $project->start_date ?? now()->toDateString();
                }
            } else {
                // Active status (in-progress, on-hold)
                $project->end_date = null;
                // If progress percentage was set to 100 or 0 incorrectly, adjust it
                if ($project->current_progress_percentage === 100) {
                    $project->current_progress_percentage = 90;
                }
            }

            // Ensure start_date <= due_date
            if ($project->start_date && $project->due_date) {
                $start = Carbon::parse($project->start_date);
                $due = Carbon::parse($project->due_date);
                if ($start->gt($due)) {
                    $project->due_date = $start->copy()->addDays(30)->toDateString();
                }
            }

            // Ensure end_date >= start_date
            if ($project->start_date && $project->end_date) {
                $start = Carbon::parse($project->start_date);
                $end = Carbon::parse($project->end_date);
                if ($start->gt($end)) {
                    $project->end_date = $start->copy()->addDays(5)->toDateString();
                }
            }
        });
    }

    public function planned(): static
    {
        return $this->state(fn (array $attributes) => [
            'current_status' => Status::PLANNED,
            'current_progress_percentage' => 0,
            'end_date' => null,
        ]);
    }

    public function inProgress(?int $progress = null): static
    {
        return $this->state(fn (array $attributes) => [
            'current_status' => Status::IN_PROGRESS,
            'current_progress_percentage' => $progress ?? fake()->numberBetween(10, 90),
            'end_date' => null,
        ]);
    }

    public function onHold(?int $progress = null): static
    {
        return $this->state(fn (array $attributes) => [
            'current_status' => Status::ON_HOLD,
            'current_progress_percentage' => $progress ?? fake()->numberBetween(5, 80),
            'end_date' => null,
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'current_status' => Status::COMPLETED,
            'current_progress_percentage' => 100,
        ]);
    }

    public function cancelled(?int $progress = null): static
    {
        return $this->state(fn (array $attributes) => [
            'current_status' => Status::CANCELLED,
            'current_progress_percentage' => $progress ?? fake()->numberBetween(0, 90),
        ]);
    }

    public function withDates(mixed $start, mixed $due, mixed $end = null): static
    {
        return $this->state(fn (array $attributes) => [
            'start_date' => Carbon::parse($start)->toDateString(),
            'due_date' => Carbon::parse($due)->toDateString(),
            'end_date' => $end ? Carbon::parse($end)->toDateString() : null,
        ]);
    }

    public function withCoherentUpdates(int $count = 3): static
    {
        return $this->afterCreating(function (Project $project) use ($count) {
            // Delete default updates if any
            $project->projectUpdates()->delete();

            $updaterIds = User::pluck('id')->toArray();
            if (empty($updaterIds)) {
                $updaterIds = [User::factory()->create()->id];
            }

            $startDate = Carbon::parse($project->start_date ?? now()->subDays(30));
            $endDate = null;
            if ($project->end_date) {
                $endDate = Carbon::parse($project->end_date);
            } elseif ($project->due_date) {
                $dueDate = Carbon::parse($project->due_date);
                $endDate = $dueDate->lt(now()) ? $dueDate : now();
            } else {
                $endDate = now();
            }

            if ($endDate->lt($startDate)) {
                $endDate = $startDate->copy()->addDays(5);
            }

            $diffInSeconds = $endDate->timestamp - $startDate->timestamp;
            $stepSeconds = $count > 0 ? (int)($diffInSeconds / ($count + 1)) : 0;

            $maxProgress = $project->current_progress_percentage;

            for ($i = 1; $i <= $count; $i++) {
                $progress = (int) (($maxProgress * $i) / $count);
                
                if ($i === $count) {
                    $status = $project->current_status;
                } else {
                    if ($progress === 0) {
                        $status = Status::PLANNED;
                    } elseif ($progress === 100) {
                        $status = Status::COMPLETED;
                    } else {
                        $status = Status::IN_PROGRESS;
                    }
                }

                $createdAt = $startDate->copy()->addSeconds($stepSeconds * $i);

                ProjectUpdate::create([
                    'project_id' => $project->id,
                    'status' => $status,
                    'progress_percentage' => $progress,
                    'updater_user_id' => fake()->randomElement($updaterIds),
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                    'description' => sprintf(
                        "Update %d for project '%s'. Status is now %s with %d%% progress.",
                        $i,
                        $project->title,
                        $status->getLabel(),
                        $progress
                    ),
                ]);
            }
        });
    }
}
