<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProjectUpdate;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AuditLogController extends Controller
{
    /**
     * Display a listing of system audit logs / project updates.
     */
    public function index(Request $request)
    {
        $query = ProjectUpdate::query()
            ->with(['project:id,title', 'updaterUser:id,name'])
            ->latest();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhereHas('project', function ($qp) use ($search) {
                      $qp->where('title', 'like', "%{$search}%");
                  })
                  ->orWhereHas('updaterUser', function ($qu) use ($search) {
                      $qu->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $status = $request->input('status');
            $query->where('status', $status);
        }

        $logs = $query->paginate(15)->withQueryString();

        // Map the results to format dates and labels
        $logs->getCollection()->transform(function ($update) {
            return [
                'id' => $update->id,
                'description' => $update->description,
                'status' => $update->status?->value,
                'status_label' => $update->status_label,
                'progress_percentage' => $update->progress_percentage,
                'project' => $update->project,
                'updater_user' => $update->updaterUser,
                'created_at' => $update->created_at->toIso8601String(),
                'created_at_human' => $update->created_at->diffForHumans(),
            ];
        });

        return Inertia::render('admin/Logs', [
            'logs' => $logs,
            'filters' => $request->only(['search', 'status']),
        ]);
    }
}
