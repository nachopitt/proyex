<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\ProjectUpdate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Project::factory()
            ->has(ProjectUpdate::factory()->count(10))
            ->count(50)
            ->create();
    }
}
