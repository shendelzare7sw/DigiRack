<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\IdentityVerification;
use App\Notifications\IdentityVerificationNotification;

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

        if ($request->filled('verification')) {
            $request->verification === 'not_submitted'
                ? $query->whereDoesntHave('identityVerification')
                : $query->whereHas('identityVerification', fn ($identity) => $identity
                    ->where('status', $request->verification));
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

        $users = $query->with('identityVerification')->latest()->paginate(15)->withQueryString();

        // Stats
        $totalUsers = User::count();
        $totalBuyers = User::where('role', 'buyer')->count();
        $totalAdmins = User::where('role', 'admin')->count();
        $bannedUsers = User::where('is_active', false)->count();
        $pendingIdentityCount = IdentityVerification::where('status', IdentityVerification::STATUS_PENDING)->count();

        return view('admin.users.index', compact(
            'users', 'totalUsers', 'totalBuyers', 'totalAdmins', 'bannedUsers', 'pendingIdentityCount'
        ));
    }

    public function show($id)
    {
        $user = User::with(['identityVerification.reviewer', 'addresses', 'orders', 'reviews'])->findOrFail($id);

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

    public function approveIdentity(Request $request, User $user)
    {
        abort_unless($user->isBuyer(), 404);
        $verification = $user->identityVerification;
        abort_unless($verification?->status === IdentityVerification::STATUS_PENDING, 422);

        $verification->update([
            'status' => IdentityVerification::STATUS_VERIFIED,
            'review_note' => null,
            'reviewed_at' => now(),
            'reviewed_by' => $request->user()->id,
        ]);

        $user->notify(new IdentityVerificationNotification(
            'Identitas berhasil diverifikasi',
            'KTP Anda disetujui. Checkout Digital Hook sekarang dapat digunakan.',
            route('profile.identity.edit'),
        ));

        return back()->with('success', 'Identitas pembeli berhasil diverifikasi.');
    }

    public function rejectIdentity(Request $request, User $user)
    {
        abort_unless($user->isBuyer(), 404);
        $validated = $request->validate([
            'review_note' => ['required', 'string', 'min:10', 'max:1000'],
        ]);
        $verification = $user->identityVerification;
        abort_unless($verification?->status === IdentityVerification::STATUS_PENDING, 422);

        $verification->update([
            'status' => IdentityVerification::STATUS_REJECTED,
            'review_note' => $validated['review_note'],
            'reviewed_at' => now(),
            'reviewed_by' => $request->user()->id,
        ]);

        $user->notify(new IdentityVerificationNotification(
            'Verifikasi KTP perlu diperbaiki',
            'Buka halaman verifikasi untuk melihat alasan penolakan dan kirim ulang dokumen.',
            route('profile.identity.edit'),
        ));

        return back()->with('success', 'KTP ditolak dan pembeli telah diberi notifikasi.');
    }
}
