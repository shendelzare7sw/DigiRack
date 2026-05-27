<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = $request->user();

        // Track login activity
        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        $request->session()->put('active_role', $user->isAdmin() ? 'admin' : 'buyer');

        // Intended URL check (avoid API/AJAX endpoints)
        $intendedUrl = redirect()->intended()->getTargetUrl();
        if (str_contains($intendedUrl, 'api/') || str_contains($intendedUrl, 'check-')) {
            $intendedUrl = null;
        }

        if ($user->isAdmin() && $intendedUrl && $intendedUrl !== route('dashboard') && $intendedUrl !== url('/')) {
            return redirect($intendedUrl);
        }

        return $user->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect('/');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
