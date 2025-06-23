<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    // List all users
    public function index()
    {
        $users = User::with('department')->paginate(10);
        $roles = ['director', 'supervisor', 'team_leader', 'employee'];
        $departments = \App\Models\Department::all();
        $sortField = 'name';
        $sortDirection = 'asc';
        return view('users.index', compact('users', 'roles', 'departments', 'sortField', 'sortDirection'));
    }

    // Show form to create a new user
    public function create()
    {
        $roles = ['supervisor', 'team_leader', 'employee'];
        $departments = Department::all();
        return view('users.create', compact('roles', 'departments'));
    }

    // Store a new user
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:supervisor,team_leader,employee',
            'department_id' => 'required|exists:departments,id',
        ]);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'department_id' => $request->department_id,
        ]);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    // Show form to edit a user
    public function edit(User $user)
    {
        $roles = ['supervisor', 'team_leader', 'employee'];
        $departments = Department::all();
        if (request()->ajax() || request()->wantsJson()) {
            // For AJAX, return only the modal partial (no layout)
            return view('users.partials.edit-modal', compact('user', 'roles', 'departments'))->render();
        }
        return view('users.edit', compact('user', 'roles', 'departments'));
    }

    // Update user info
    public function update(Request $request, User $user)
    {
        $isAjax = $request->ajax() || $request->wantsJson();
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:supervisor,team_leader,employee',
            'department_id' => 'required|exists:departments,id',
        ]);

        $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'role' => $request->role,
            'department_id' => $request->department_id,
        ]);

        if ($isAjax) {
            // On AJAX, return the form with a success message
            $roles = ['supervisor', 'team_leader', 'employee'];
            $departments = Department::all();
            $success = true;
            return view('users.edit', compact('user', 'roles', 'departments', 'success'))->with('success', 'User updated successfully.');
        }
        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    // Change user role
    public function changeRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:supervisor,team_leader,employee',
        ]);
        $user->role = $request->role;
        $user->save();
        return redirect()->route('users.index')->with('success', 'User role updated successfully.');
    }

    // Change user team/department
    public function changeTeam(Request $request, User $user)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
        ]);
        $user->department_id = $request->department_id;
        $user->save();
        return redirect()->route('users.index')->with('success', 'User team updated successfully.');
    }

    // Delete user
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
