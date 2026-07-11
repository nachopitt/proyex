<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Role;
use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Tag;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Display the Admin Dashboard / Overview page.
     */
    public function index()
    {
        $today = Carbon::today()->toDateString();
        $inactiveStatuses = [Status::COMPLETED->value, Status::CANCELLED->value];

        // 1. User Statistics
        $totalUsers = User::count();
        $activeUsers = User::whereHas('userProfile', function ($query) {
            $query->where('active', true);
        })->count();
        $inactiveUsers = $totalUsers - $activeUsers;

        $adminUsers = User::whereHas('userRoles', function ($query) {
            $query->where('role', Role::ADMIN->value);
        })->count();
        $regularUsers = $totalUsers - $adminUsers;

        // 2. Project Statistics
        $totalProjects = Project::count();
        $activeProjects = Project::whereNotIn('current_status', $inactiveStatuses)->count();
        $completedProjects = Project::where('current_status', Status::COMPLETED->value)->count();
        $overdueProjects = Project::whereNotIn('current_status', $inactiveStatuses)
            ->whereNotNull('due_date')
            ->where('due_date', '<', $today)
            ->count();

        // 3. System Information
        $dbDriver = DB::connection()->getDriverName();
        $dbVersion = 'Unknown';
        try {
            $results = DB::select('select version() as version');
            if (!empty($results)) {
                $dbVersion = $results[0]->version;
            }
        } catch (\Exception $e) {
            // Ignore database connection version exceptions
        }

        $systemInfo = [
            'laravel_version' => app()->version(),
            'php_version' => PHP_VERSION,
            'db_driver' => $dbDriver,
            'db_version' => $dbVersion,
            'app_env' => config('app.env'),
            'debug_mode' => config('app.debug'),
            'timezone' => config('app.timezone'),
        ];

        return Inertia::render('admin/Dashboard', [
            'metrics' => [
                'users' => [
                    'total' => $totalUsers,
                    'active' => $activeUsers,
                    'inactive' => $inactiveUsers,
                    'admins' => $adminUsers,
                    'regular' => $regularUsers,
                ],
                'projects' => [
                    'total' => $totalProjects,
                    'active' => $activeProjects,
                    'completed' => $completedProjects,
                    'overdue' => $overdueProjects,
                ],
                'tags_count' => Tag::count(),
            ],
            'system_info' => $systemInfo,
        ]);
    }

    /**
     * Clear application cache using artisan commands.
     */
    public function clearCache(Request $request)
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');

            return redirect()->back()->with('success', 'Application cache cleared successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to clear cache: ' . $e->getMessage());
        }
    }
}
