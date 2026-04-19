<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminRecoveryController extends Controller
{
    /**
     * Easter egg: Unlock access to admin recovery form via AJAX.
     */
    public function unlock(Request $request)
    {
        $request->session()->put('admin_recovery_unlocked', true);
        return response()->json(['success' => true]);
    }

    /**
     * Show the admin recovery form (only if session unlocked).
     */
    public function showForm(Request $request)
    {
        if (!$request->session()->get('admin_recovery_unlocked')) {
            return redirect()->route('login')->with('error', 'Akses ditolak.');
        }

        return view('auth.admin-recovery');
    }

    /**
     * Process admin password reset via security question + answer + PIN.
     */
    public function reset(Request $request)
    {
        if (!$request->session()->get('admin_recovery_unlocked')) {
            return redirect()->route('login')->with('error', 'Akses ditolak.');
        }

        $request->validate([
            'identifier' => 'required|string',
            'security_question' => 'required|string',
            'security_answer' => 'required|string',
            'security_pin' => 'required|string|digits:6',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Find admin user by email or username
        $user = User::where('username', $request->identifier)
                    ->orWhere('email', $request->identifier)
                    ->first();

        if (!$user || !$user->isAdmin()) {
            return back()->withInput()->withErrors(['identifier' => 'Akun administrator tidak ditemukan.']);
        }

        // Verify security question matches
        if (!$user->security_question || $user->security_question !== $request->security_question) {
            return back()->withInput()->withErrors(['security_question' => 'Pertanyaan keamanan tidak cocok.']);
        }

        // Verify security answer (hashed)
        if (!$user->security_answer || !Hash::check(strtolower(trim($request->security_answer)), $user->security_answer)) {
            return back()->withInput()->withErrors(['security_answer' => 'Jawaban keamanan salah.']);
        }

        // Verify security PIN (hashed)
        if (!$user->security_pin || !Hash::check($request->security_pin, $user->security_pin)) {
            return back()->withInput()->withErrors(['security_pin' => 'PIN Keamanan salah.']);
        }

        // All checks passed — reset password
        $user->password = Hash::make($request->password);
        $user->save();

        // Lock the easter egg session
        $request->session()->forget('admin_recovery_unlocked');

        return redirect()->route('login')->with('success', 'Password administrator berhasil direset. Silakan login kembali.');
    }
}
