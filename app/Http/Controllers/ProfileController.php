<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use App\Notifications\ConfirmPhoneChange;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();
        $currentEmail = $user->email;
        $emailWasVerified = $user->hasVerifiedEmail();
        $phoneSubmitted = array_key_exists('phone', $validated);
        $requestedPhone = $phoneSubmitted ? $validated['phone'] : $user->phone;
        $phoneChanged = $phoneSubmitted && $requestedPhone !== $user->phone;

        unset($validated['phone']);

        $user->fill($validated);

        $emailChanged = $user->isDirty('email');

        if ($emailChanged) {
            $user->email_verified_at = null;
        }

        if ($phoneChanged) {
            if (blank($requestedPhone)) {
                throw ValidationException::withMessages([
                    'phone' => 'Nomor telepon wajib diisi.',
                ]);
            }

            if (! $emailWasVerified) {
                throw ValidationException::withMessages([
                    'phone' => 'Verifikasi email Anda terlebih dahulu sebelum mengganti nomor telepon.',
                ]);
            }

            $token = bin2hex(random_bytes(32));
            $user->pending_phone = $requestedPhone;
            $user->pending_phone_token = hash('sha256', $token);
            $user->pending_phone_expires_at = now()->addMinutes(config('auth.verification.expire', 60));
        }

        if ($request->hasFile('avatar')) {
            if ($user->avatar && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->avatar)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar);
            }
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $avatarPath;
        }

        $user->save();

        if ($phoneChanged) {
            Notification::route('mail', $currentEmail)
                ->notify(new ConfirmPhoneChange($user, $requestedPhone, $token));
        }

        if ($emailChanged && $user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail) {
            $user->sendEmailVerificationNotification();
        }

        $status = $phoneChanged
            ? 'phone-change-verification-sent'
            : 'profile-updated';

        return Redirect::route('profile.edit')->with('status', $status);
    }

    public function confirmPhoneChange(Request $request, User $user, string $token): RedirectResponse
    {
        $pendingPhone = $user->pending_phone;
        $expectedToken = $user->pending_phone_token;

        if (
            ! $pendingPhone ||
            ! $expectedToken ||
            ! hash_equals($expectedToken, hash('sha256', $token)) ||
            optional($user->pending_phone_expires_at)->isPast()
        ) {
            abort(403, 'Link konfirmasi nomor telepon tidak valid atau sudah kedaluwarsa.');
        }

        $phoneIsUsed = User::query()
            ->where('phone', $pendingPhone)
            ->whereKeyNot($user->getKey())
            ->exists();

        if ($phoneIsUsed) {
            return Redirect::route('profile.edit')
                ->withErrors(['phone' => 'Nomor telepon ini sudah digunakan akun lain.']);
        }

        $user->forceFill([
            'phone' => $pendingPhone,
            'pending_phone' => null,
            'pending_phone_token' => null,
            'pending_phone_expires_at' => null,
        ])->save();

        return Auth::id() === $user->getKey()
            ? Redirect::route('profile.edit')->with('status', 'phone-updated')
            : Redirect::route('login')->with('success', 'Nomor telepon berhasil diperbarui. Silakan login kembali.');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
