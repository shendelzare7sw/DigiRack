<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\RegistrationOtpNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    private const SESSION_KEY = 'registration_otp';
    private const EXPIRES_MINUTES = 10;
    private const MAX_ATTEMPTS = 5;
    private const MAX_RESENDS = 3;
    private const RESEND_COOLDOWN_SECONDS = 60;

    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Validate the registration form and send a 6-digit OTP to the email.
     * The account is NOT created until the OTP is verified.
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

        $baseUsername = 'user_' . strtolower(Str::random(8));
        while (User::where('username', $baseUsername)->exists()) {
            $baseUsername = 'user_' . strtolower(Str::random(8));
        }

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $request->session()->put(self::SESSION_KEY, [
            'name' => $request->name,
            'username' => $baseUsername,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'code' => Hash::make($code),
            'expires_at' => now()->addMinutes(self::EXPIRES_MINUTES)->timestamp,
            'sent_at' => now()->timestamp,
            'attempts' => 0,
            'resends' => 0,
        ]);

        $this->sendOtp($request->email, $request->name, $code);

        return redirect()->route('register.otp.notice')
            ->with('status', 'Kode OTP telah dikirim ke ' . $request->email . '. Cek inbox/spam Anda.');
    }

    /**
     * Show the OTP entry form.
     */
    public function showOtp(Request $request): RedirectResponse|View
    {
        $pending = $request->session()->get(self::SESSION_KEY);

        if (!$pending) {
            return redirect()->route('register')
                ->with('error', 'Sesi pendaftaran tidak ditemukan atau sudah berakhir. Silakan daftar ulang.');
        }

        return view('auth.verify-otp', [
            'email' => $pending['email'],
            'canResendIn' => max(0, self::RESEND_COOLDOWN_SECONDS - (now()->timestamp - $pending['sent_at'])),
        ]);
    }

    /**
     * Verify the OTP and create the account.
     *
     * @throws ValidationException
     */
    public function verifyOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'string', 'regex:/^\d{6}$/'],
        ], [
            'code.regex' => 'Kode OTP harus 6 digit angka.',
        ]);

        $pending = $request->session()->get(self::SESSION_KEY);

        if (!$pending) {
            return redirect()->route('register')
                ->with('error', 'Sesi pendaftaran tidak ditemukan atau sudah berakhir. Silakan daftar ulang.');
        }

        if (now()->timestamp > $pending['expires_at']) {
            $request->session()->forget(self::SESSION_KEY);
            return redirect()->route('register')
                ->with('error', 'Kode OTP sudah kedaluwarsa. Silakan daftar ulang.');
        }

        if ($pending['attempts'] >= self::MAX_ATTEMPTS) {
            $request->session()->forget(self::SESSION_KEY);
            return redirect()->route('register')
                ->with('error', 'Terlalu banyak percobaan kode OTP. Silakan daftar ulang.');
        }

        if (!Hash::check($request->code, $pending['code'])) {
            $pending['attempts']++;
            $request->session()->put(self::SESSION_KEY, $pending);
            $remaining = self::MAX_ATTEMPTS - $pending['attempts'];

            throw ValidationException::withMessages([
                'code' => 'Kode OTP salah.' . ($remaining > 0 ? ' Sisa percobaan: ' . $remaining . '.' : ''),
            ]);
        }

        // Guard against the email/phone being taken between request and verification.
        if (User::where('email', $pending['email'])->orWhere('phone', $pending['phone'])->exists()) {
            $request->session()->forget(self::SESSION_KEY);
            return redirect()->route('register')
                ->with('error', 'Email atau nomor telepon sudah terdaftar. Silakan gunakan data lain.');
        }

        $user = User::create([
            'name' => $pending['name'],
            'username' => $pending['username'],
            'email' => $pending['email'],
            'phone' => $pending['phone'],
            'password' => $pending['password'],
            'role' => 'buyer',
        ]);

        // OTP already proves email ownership — mark verified, skip the link flow.
        $user->forceFill(['email_verified_at' => now()])->save();

        $request->session()->forget(self::SESSION_KEY);

        event(new Registered($user));

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')
            ->with('success', 'Email terverifikasi & akun berhasil dibuat. Selamat datang di ' . config('app.name', 'DigiRack') . '!');
    }

    /**
     * Resend the OTP (rate limited).
     */
    public function resendOtp(Request $request): RedirectResponse
    {
        $pending = $request->session()->get(self::SESSION_KEY);

        if (!$pending) {
            return redirect()->route('register')
                ->with('error', 'Sesi pendaftaran tidak ditemukan. Silakan daftar ulang.');
        }

        if (now()->timestamp > $pending['expires_at']) {
            $request->session()->forget(self::SESSION_KEY);
            return redirect()->route('register')
                ->with('error', 'Kode OTP sudah kedaluwarsa. Silakan daftar ulang.');
        }

        if (($pending['resends'] ?? 0) >= self::MAX_RESENDS) {
            $request->session()->forget(self::SESSION_KEY);
            return redirect()->route('register')
                ->with('error', 'Batas kirim ulang kode OTP tercapai. Silakan daftar ulang.');
        }

        $elapsed = now()->timestamp - $pending['sent_at'];
        if ($elapsed < self::RESEND_COOLDOWN_SECONDS) {
            return back()->with('error', 'Tunggu ' . (self::RESEND_COOLDOWN_SECONDS - $elapsed) . ' detik sebelum meminta kode baru.');
        }

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $pending['code'] = Hash::make($code);
        $pending['expires_at'] = now()->addMinutes(self::EXPIRES_MINUTES)->timestamp;
        $pending['sent_at'] = now()->timestamp;
        $pending['attempts'] = 0;
        $pending['resends'] = ($pending['resends'] ?? 0) + 1;
        $request->session()->put(self::SESSION_KEY, $pending);

        $this->sendOtp($pending['email'], $pending['name'], $code);

        return back()->with('status', 'Kode OTP baru telah dikirim ke ' . $pending['email'] . '.');
    }

    private function sendOtp(string $email, string $name, string $code): void
    {
        Notification::route('mail', $email)
            ->notify(new RegistrationOtpNotification($code, $name, self::EXPIRES_MINUTES));
    }
}
