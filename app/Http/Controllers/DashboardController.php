<?php

namespace App\Http\Controllers;

use App\Enums\Priority;
use App\Enums\Status;
use App\Models\Project;
use App\Models\ProjectUpdate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Display the dashboard page with projects stats and reports.
     */
    public function index(Request $request)
    {
        $today = Carbon::today()->toDateString();

        // 1. Projects statistics (Top-level projects only, i.e., parent_id IS NULL)
        $totalProjectsCount = Project::whereNull('parent_id')->count();
        
        $activeProjectsCount = Project::whereNull('parent_id')
            ->where('current_status', '!=', Status::COMPLETED)
            ->count();

        $completedProjectsCount = Project::whereNull('parent_id')
            ->where('current_status', Status::COMPLETED)
            ->count();

        $overdueProjectsCount = Project::where('current_status', '!=', Status::COMPLETED)
            ->whereNotNull('due_date')
            ->where('due_date', '<', $today)
            ->count();

        $totalSubtasksCount = Project::whereNotNull('parent_id')->count();

        // Safe completion rate calculation
        $completionRate = $totalProjectsCount > 0 
            ? round(($completedProjectsCount / $totalProjectsCount) * 100) 
            : 0;

        // 2. Recent activity feed (Project updates with project and updater details)
        $recentUpdates = ProjectUpdate::latest()
            ->with(['project:id,title', 'updaterUser:id,name'])
            ->take(5)
            ->get()
            ->map(function ($update) {
                return [
                    'id' => $update->id,
                    'description' => $update->description,
                    'status_label' => $update->status_label,
                    'progress_percentage' => $update->progress_percentage,
                    'project' => $update->project,
                    'updater_user' => $update->updaterUser,
                    'created_at_human' => $update->created_at->diffForHumans(),
                ];
            });

        // 3. Upcoming project deadlines (Next 5 upcoming deadlines)
        $upcomingProjects = Project::where('current_status', '!=', Status::COMPLETED)
            ->whereNotNull('due_date')
            ->where('due_date', '>=', $today)
            ->orderBy('due_date', 'asc')
            ->with('assignedUser:id,name')
            ->take(5)
            ->get();

        // 4. Breakdown by priority
        $priorities = Priority::cases();
        $priorityBreakdown = [];
        foreach ($priorities as $priority) {
            $priorityBreakdown[] = [
                'name' => $priority->getLabel(),
                'value' => $priority->value,
                'count' => Project::where('priority', $priority)->count(),
            ];
        }

        // 5. Breakdown by status
        $statuses = Status::cases();
        $statusBreakdown = [];
        foreach ($statuses as $status) {
            $statusBreakdown[] = [
                'name' => $status->getLabel(),
                'value' => $status->value,
                'count' => Project::where('current_status', $status)->count(),
            ];
        }

        return Inertia::render('Dashboard', [
            'stats' => [
                'active_projects' => $activeProjectsCount,
                'completed_projects' => $completedProjectsCount,
                'overdue_projects' => $overdueProjectsCount,
                'total_subtasks' => $totalSubtasksCount,
                'completion_rate' => $completionRate,
            ],
            'recent_updates' => $recentUpdates,
            'upcoming_projects' => $upcomingProjects,
            'priority_breakdown' => $priorityBreakdown,
            'status_breakdown' => $statusBreakdown,
        ]);
    }
}
