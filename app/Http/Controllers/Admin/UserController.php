<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index(Request $request)
    {
        $query = User::query()->with(['userProfile', 'userRoles']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('userProfile', function($qp) use ($search) {
                      $qp->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('active')) {
            $active = $request->input('active');
            $query->whereHas('userProfile', function($qp) use ($active) {
                $qp->where('active', filter_var($active, FILTER_VALIDATE_BOOLEAN));
            });
        }

        if ($request->filled('role')) {
            $role = $request->input('role');
            $query->whereHas('userRoles', function($qr) use ($role) {
                $qr->where('role', $role);
            });
        }

        $users = $query->paginate(15)->withQueryString();

        return Inertia::render('admin/Users', [
            'users' => $users,
            'filters' => $request->only(['search', 'active', 'role']),
        ]);
    }

    /**
     * Update the specified user's role and active status.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'active' => 'sometimes|required|boolean',
            'role' => 'sometimes|required|string|in:admin,user',
        ]);

        if ($user->id === $request->user()->id) {
            if ($request->has('active') && ! $request->input('active')) {
                return back()->withErrors(['error' => 'You cannot deactivate your own account.']);
            }
            if ($request->has('role') && $request->input('role') !== \App\Enums\Role::ADMIN->value) {
                return back()->withErrors(['error' => 'You cannot demote yourself.']);
            }
        }

        if ($request->has('active')) {
            $user->userProfile()->updateOrCreate(
                ['user_id' => $user->id],
                ['active' => $request->input('active')]
            );
        }

        if ($request->has('role')) {
            $user->userRoles()->updateOrCreate(
                ['user_id' => $user->id],
                ['role' => $request->input('role')]
            );
        }

        return redirect()->back();
    }
}
