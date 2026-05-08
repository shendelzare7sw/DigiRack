<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        // Stats
        $totalUsers = User::count();
        $totalBuyers = User::where('role', 'buyer')->count();
        $totalSellers = User::where('role', 'seller')->count();
        $totalAdmins = User::where('role', 'admin')->count();
        $bannedUsers = User::where('is_active', false)->count();

        return view('admin.users.index', compact(
            'users', 'totalUsers', 'totalBuyers', 'totalSellers', 'totalAdmins', 'bannedUsers'
        ));
    }

    public function show($id)
    {
        $user = User::with(['store', 'orders', 'reviews'])->findOrFail($id);

        return view('admin.users.show', compact('user'));
    }

    public function toggleActive($id)
    {
        $user = User::findOrFail($id);

        // Prevent banning yourself
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa menonaktifkan akun Anda sendiri.');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan (banned)';
        return back()->with('success', "User {$user->name} berhasil {$status}.");
    }
}
