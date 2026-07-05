<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Audit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    /**
     * Requirement 13 & 14: Advanced Data Controls & User Management
     */
    public function index(Request $request)
    {
        $query = User::with(['role', 'loans']);

        // Searchable columns for the trait
        $searchable = ['name', 'email', 'status', 'role.name'];

        // Apply filters, sorting, and search
        $users = $query->where(function($q) use ($request) {
            if ($request->filled('search')) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            }
        });

        if ($request->filled('status')) {
            $users->where('status', $request->status);
        }

        if ($request->filled('role_id')) {
            $users->where('role_id', $request->role_id);
        }

        // Sorting
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');
        $users->orderBy($sort, $direction);

        // Bulk Actions (Requirement 13.5)
        if ($request->has('bulk_action') && $request->has('selected_users')) {
            $this->handleBulkAction($request->bulk_action, $request->selected_users);
            return back()->with('success', 'Bulk operation completed.');
        }

        $users = $users->paginate($request->get('per_page', 10))->withQueryString();
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|in:active,inactive,suspended',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        User::create($validated);

        return redirect()->route('admin.users.index')->with('success', 'Account generated.');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        // Login History (Requirement 14.4)
        $loginHistory = Audit::where('user_id', $user->id)
            ->whereIn('event', ['login', 'login_failed'])
            ->latest()
            ->limit(10)
            ->get();

        // Activity Analytics (Requirement 14.7)
        $lastActive = Audit::where('user_id', $user->id)->latest()->first()?->created_at;
        $mostUsedFeature = Audit::where('user_id', $user->id)
            ->select('auditable_type', DB::raw('count(*) as total'))
            ->groupBy('auditable_type')
            ->orderBy('total', 'desc')
            ->first();

        return view('admin.users.edit', compact('user', 'roles', 'loginHistory', 'lastActive', 'mostUsedFeature'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|in:active,inactive,suspended',
        ]);

        $user->update($validated);
        return redirect()->route('admin.users.index')->with('success', 'User refactored.');
    }

    public function destroy(Request $request, User $user)
    {
        $request->validate([
            'password' => 'required|current_password'
        ]);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete yourself.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Account deconstructed.');
    }

    /**
     * User Impersonation (Requirement 14.3)
     */
    public function impersonate(User $user)
    {
        if ($user->id === auth()->id()) return back();

        session(['impersonator_id' => auth()->id()]);
        Auth::login($user);

        return redirect()->route('home')->with('success', "Now impersonating {$user->name}");
    }

    public function stopImpersonating()
    {
        $id = session('impersonator_id');
        session()->forget('impersonator_id');
        Auth::loginUsingId($id);

        return redirect()->route('admin.users.index');
    }

    /**
     * Force Logout (Requirement 14.5)
     */
    public function forceLogout(User $user)
    {
        DB::table('sessions')->where('user_id', $user->id)->delete();
        return back()->with('success', "All sessions for {$user->name} have been purged.");
    }

    protected function handleBulkAction($action, $ids)
    {
        $users = User::whereIn('id', $ids)->where('id', '!=', auth()->id());

        match($action) {
            'delete' => $users->delete(),
            'activate' => $users->update(['status' => 'active']),
            'suspend' => $users->update(['status' => 'suspended']),
            'export' => null // Export handled separately usually
        };
    }
}
