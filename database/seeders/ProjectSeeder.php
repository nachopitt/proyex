<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use App\Enums\Priority;
use App\Enums\Status;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Seed reproducible baseline
        $this->createBaseline();

        // 2. Optional bulk random expansion if SEED_BULK env var is true
        if (env('SEED_BULK', false)) {
            $this->createBulkRandom();
        }
    }

    private function createBaseline(): void
    {
        fake()->seed(1234);

        if (User::count() <= 1) {
            User::factory(9)->create();
        }

        $users = User::all();
        $admin = User::where('email', env('INITIAL_ADMIN_EMAIL', 'admin@example.com'))->first() ?? $users->first();

        // 1. Website Redesign (In Progress, High Priority, due in 10 days)
        $p1 = Project::factory()
            ->inProgress(60)
            ->withDates(now()->subDays(15), now()->addDays(10))
            ->withCoherentUpdates(3)
            ->create([
                'title' => 'Website Redesign',
                'description' => 'Redesigning the company website for better UX and performance.',
                'priority' => Priority::HIGH,
                'reporter_user_id' => $admin->id,
                'assigned_user_id' => $users->random()->id,
            ]);

        Project::factory()
            ->completed()
            ->withDates(now()->subDays(15), now()->subDays(10), now()->subDays(12))
            ->withCoherentUpdates(2)
            ->create([
                'title' => 'Design Homepage Mockups',
                'description' => 'Create layout wireframes and visual designs for the main homepage.',
                'parent_id' => $p1->id,
                'priority' => Priority::HIGH,
                'reporter_user_id' => $admin->id,
                'assigned_user_id' => $users->random()->id,
            ]);

        Project::factory()
            ->inProgress(50)
            ->withDates(now()->subDays(11), now()->addDays(5))
            ->withCoherentUpdates(2)
            ->create([
                'title' => 'Develop Frontend Components',
                'description' => 'Implement the redesigned pages using Vue and Tailwind.',
                'parent_id' => $p1->id,
                'priority' => Priority::HIGH,
                'reporter_user_id' => $admin->id,
                'assigned_user_id' => $users->random()->id,
            ]);

        Project::factory()
            ->planned()
            ->withDates(now()->subDays(5), now()->addDays(12))
            ->withCoherentUpdates(0)
            ->create([
                'title' => 'Integrate CMS Backend',
                'description' => 'Connect frontend template with Laravel CMS admin panel.',
                'parent_id' => $p1->id,
                'priority' => Priority::MEDIUM,
                'reporter_user_id' => $admin->id,
                'assigned_user_id' => $users->random()->id,
            ]);

        // 2. Mobile App API Integration (In Progress, Medium Priority, due in 12 days)
        $p2 = Project::factory()
            ->inProgress(35)
            ->withDates(now()->subDays(8), now()->addDays(12))
            ->withCoherentUpdates(2)
            ->create([
                'title' => 'Mobile App API Integration',
                'description' => 'Set up RESTful endpoints for the mobile app authentication and resources.',
                'priority' => Priority::MEDIUM,
                'reporter_user_id' => $admin->id,
                'assigned_user_id' => $users->random()->id,
            ]);

        Project::factory()
            ->completed()
            ->withDates(now()->subDays(8), now()->subDays(5), now()->subDays(6))
            ->withCoherentUpdates(2)
            ->create([
                'title' => 'API Authentication Setup',
                'description' => 'Implement OAuth2 / Sanctum authentication flow.',
                'parent_id' => $p2->id,
                'priority' => Priority::MEDIUM,
                'reporter_user_id' => $admin->id,
                'assigned_user_id' => $users->random()->id,
            ]);

        Project::factory()
            ->inProgress(20)
            ->withDates(now()->subDays(5), now()->addDays(15))
            ->withCoherentUpdates(1)
            ->create([
                'title' => 'Push Notifications Payload',
                'description' => 'Design JSON payload structure for APNS and FCM.',
                'parent_id' => $p2->id,
                'priority' => Priority::LOW,
                'reporter_user_id' => $admin->id,
                'assigned_user_id' => $users->random()->id,
            ]);

        // 3. Q3 Marketing Campaign Planning (Planned, Medium Priority, due in 35 days)
        $p3 = Project::factory()
            ->planned()
            ->withDates(now()->addDays(5), now()->addDays(35))
            ->withCoherentUpdates(0)
            ->create([
                'title' => 'Q3 Marketing Campaign Planning',
                'description' => 'Outline deliverables, schedule, and assets for the Q3 release campaign.',
                'priority' => Priority::MEDIUM,
                'reporter_user_id' => $admin->id,
                'assigned_user_id' => $users->random()->id,
            ]);

        Project::factory()
            ->planned()
            ->withDates(now()->addDays(10), now()->addDays(20))
            ->withCoherentUpdates(0)
            ->create([
                'title' => 'Draft Campaign Copy',
                'description' => 'Write promotional emails, blog posts, and landing page copy.',
                'parent_id' => $p3->id,
                'priority' => Priority::MEDIUM,
                'reporter_user_id' => $admin->id,
                'assigned_user_id' => $users->random()->id,
            ]);

        // 4. Migrate Server to AWS (On Hold, High Priority, due in 5 days)
        $p4 = Project::factory()
            ->onHold(25)
            ->withDates(now()->subDays(30), now()->addDays(5))
            ->withCoherentUpdates(2)
            ->create([
                'title' => 'Migrate Server to AWS',
                'description' => 'Move on-premise staging databases and apps to AWS EC2 & RDS.',
                'priority' => Priority::HIGH,
                'reporter_user_id' => $admin->id,
                'assigned_user_id' => $users->random()->id,
            ]);

        Project::factory()
            ->completed()
            ->withDates(now()->subDays(30), now()->subDays(20), now()->subDays(25))
            ->withCoherentUpdates(2)
            ->create([
                'title' => 'Audit Current Infrastructure',
                'description' => 'Document active CPU, RAM, and storage usage of current hosting.',
                'parent_id' => $p4->id,
                'priority' => Priority::MEDIUM,
                'reporter_user_id' => $admin->id,
                'assigned_user_id' => $users->random()->id,
            ]);

        Project::factory()
            ->inProgress(10)
            ->withDates(now()->subDays(24), now()->addDays(10))
            ->withCoherentUpdates(1)
            ->create([
                'title' => 'Setup AWS VPC & RDS',
                'description' => 'Establish private networks and configure managed database instances.',
                'parent_id' => $p4->id,
                'priority' => Priority::HIGH,
                'reporter_user_id' => $admin->id,
                'assigned_user_id' => $users->random()->id,
            ]);

        // 5. Customer Feedback Portal (Completed, Low Priority, due 2 days ago, completed 3 days ago)
        Project::factory()
            ->completed()
            ->withDates(now()->subDays(25), now()->subDays(2), now()->subDays(3))
            ->withCoherentUpdates(4)
            ->create([
                'title' => 'Customer Feedback Portal',
                'description' => 'Create a public route for active users to request features and report issues.',
                'priority' => Priority::LOW,
                'reporter_user_id' => $admin->id,
                'assigned_user_id' => $users->random()->id,
            ]);

        // 6. Legacy Reports Cleanup (Cancelled, Low Priority, due 10 days ago, cancelled 30 days ago)
        Project::factory()
            ->cancelled(10)
            ->withDates(now()->subDays(40), now()->subDays(10), now()->subDays(30))
            ->withCoherentUpdates(2)
            ->create([
                'title' => 'Legacy Reports Cleanup',
                'description' => 'Prune obsolete PDF export tools that consume massive database memory.',
                'priority' => Priority::LOW,
                'reporter_user_id' => $admin->id,
                'assigned_user_id' => $users->random()->id,
            ]);

        // 7. HR Onboarding System (In Progress, High Priority, due in 3 days)
        Project::factory()
            ->inProgress(80)
            ->withDates(now()->subDays(20), now()->addDays(3))
            ->withCoherentUpdates(3)
            ->create([
                'title' => 'HR Onboarding System',
                'description' => 'Design new onboarding workflows, contract document uploads, and checklist systems.',
                'priority' => Priority::HIGH,
                'reporter_user_id' => $admin->id,
                'assigned_user_id' => $users->random()->id,
            ]);

        // 8. Data Warehouse Sync Setup (In Progress, Medium Priority, due today)
        Project::factory()
            ->inProgress(50)
            ->withDates(now()->subDays(10), now())
            ->withCoherentUpdates(2)
            ->create([
                'title' => 'Data Warehouse Sync Setup',
                'description' => 'Establish nightly ETL pipelines to sync transactional data with BigQuery.',
                'priority' => Priority::MEDIUM,
                'reporter_user_id' => $admin->id,
                'assigned_user_id' => $users->random()->id,
            ]);

        // 9. Security Audit Remediations (In Progress, High Priority, due in 14 days)
        Project::factory()
            ->inProgress(15)
            ->withDates(now()->subDays(2), now()->addDays(14))
            ->withCoherentUpdates(1)
            ->create([
                'title' => 'Security Audit Remediations',
                'description' => 'Patch dependency vulnerabilities and enforce strict CSP headers.',
                'priority' => Priority::HIGH,
                'reporter_user_id' => $admin->id,
                'assigned_user_id' => $users->random()->id,
            ]);

        // 10. Developer Documentation Refactoring (Planned, Low Priority, due in 20 days)
        Project::factory()
            ->planned()
            ->withDates(now()->subDays(1), now()->addDays(20))
            ->withCoherentUpdates(1)
            ->create([
                'title' => 'Developer Documentation Refactoring',
                'description' => 'Migrate technical onboarding setup guides from PDF to markdown.',
                'priority' => Priority::LOW,
                'reporter_user_id' => $admin->id,
                'assigned_user_id' => $users->random()->id,
            ]);

        // 11. Legacy Database Migration (In Progress, High Priority, due 5 days ago) [OVERDUE ACTIVE]
        Project::factory()
            ->inProgress(80)
            ->withDates(now()->subDays(30), now()->subDays(5))
            ->withCoherentUpdates(4)
            ->create([
                'title' => 'Legacy Database Migration',
                'description' => 'Migrate client historical records from outdated DB servers to new main host.',
                'priority' => Priority::HIGH,
                'reporter_user_id' => $admin->id,
                'assigned_user_id' => $users->random()->id,
            ]);
    }

    private function createBulkRandom(): void
    {
        Project::factory()
            ->count(50)
            ->withCoherentUpdates(fake()->numberBetween(1, 5))
            ->create();
    }
}
