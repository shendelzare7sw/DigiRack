<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     * All users register as 'buyer'. To become seller, activate separately.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['required', 'string', 'max:20', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Auto-generate unique username
        $baseUsername = 'user_' . strtolower(Str::random(8));
        while (User::where('username', $baseUsername)->exists()) {
            $baseUsername = 'user_' . strtolower(Str::random(8));
        }

        $user = User::create([
            'name' => $request->name,
            'username' => $baseUsername,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'buyer', // Always buyer on registration
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect('/')->with('success', 'Selamat datang di DigiRack, ' . $user->name . '! Username Anda: ' . $user->username);
    }
}
