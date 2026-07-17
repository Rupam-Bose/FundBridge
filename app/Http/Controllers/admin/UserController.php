<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /** List all users with search/filter */
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $users = $query->latest()->paginate(15);

        return view('admin.users', compact('users'));
    }

    /** Toggle user role between founder and investor */
    public function toggleRole(Request $request, int $id)
    {
        $request->validate([
            'role' => 'required|in:founder,investor,admin',
        ]);

        $user = User::findOrFail($id);
        $user->update(['role' => $request->role]);

        return back()->with('status', "User {$user->name}'s role updated to {$request->role}.");
    }

    /** Delete a user */
    public function destroy(int $id)
    {
        $user = User::findOrFail($id);

        // Prevent self-deletion
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'You cannot delete your own account.']);
        }

        $user->delete();

        return back()->with('status', "User {$user->name} has been removed.");
    }
}
