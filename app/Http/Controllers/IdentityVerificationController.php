<?php

namespace App\Http\Controllers;

use App\Models\IdentityVerification;
use App\Models\User;
use App\Notifications\IdentityVerificationNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class IdentityVerificationController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.identity', [
            'verification' => $request->user()->identityVerification()->first(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        abort_if($request->user()->isAdmin(), 403);
        $verification = $request->user()->identityVerification()->first();

        if ($verification?->status === IdentityVerification::STATUS_VERIFIED) {
            throw ValidationException::withMessages([
                'identity' => 'Identitas yang sudah terverifikasi tidak dapat diubah dari akun pembeli.',
            ]);
        }

        $validated = $request->validate([
            'legal_name' => ['required', 'string', 'max:255'],
            'nik' => ['required', 'string', 'regex:/^\d{16}$/'],
            'identity_document' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'consent' => ['accepted'],
        ], [
            'nik.regex' => 'NIK harus terdiri dari tepat 16 digit angka.',
            'identity_document.max' => 'Ukuran foto KTP maksimal 5 MB.',
            'consent.accepted' => 'Persetujuan pemrosesan data identitas wajib diberikan.',
        ]);

        $nik = trim($validated['nik']);
        $nikHash = hash_hmac('sha256', $nik, (string) config('app.key'));

        if (IdentityVerification::where('nik_hash', $nikHash)
            ->when($verification, fn ($query) => $query->whereKeyNot($verification->id))
            ->exists()) {
            throw ValidationException::withMessages(['nik' => 'NIK sudah digunakan untuk akun lain.']);
        }

        $file = $request->file('identity_document');
        $newPath = $file->store('identity-documents/'.$request->user()->id, 'local');
        $oldPath = $verification?->document_path;

        try {
            DB::transaction(fn () => IdentityVerification::updateOrCreate(
                ['user_id' => $request->user()->id],
                [
                    'legal_name' => trim($validated['legal_name']),
                    'nik' => $nik,
                    'nik_hash' => $nikHash,
                    'document_path' => $newPath,
                    'document_mime' => (string) $file->getMimeType(),
                    'status' => IdentityVerification::STATUS_PENDING,
                    'review_note' => null,
                    'submitted_at' => now(),
                    'reviewed_at' => null,
                    'reviewed_by' => null,
                ],
            ));
        } catch (Throwable $exception) {
            Storage::disk('local')->delete($newPath);
            throw $exception;
        }

        if ($oldPath && $oldPath !== $newPath) {
            Storage::disk('local')->delete($oldPath);
        }

        User::where('role', 'admin')->each(fn (User $admin) => $admin->notify(
            new IdentityVerificationNotification(
                'Verifikasi KTP baru',
                $request->user()->name.' mengirim dokumen KTP untuk ditinjau.',
                route('admin.users.show', $request->user()),
            ),
        ));

        return back()->with('success', 'KTP berhasil dikirim dan sedang menunggu verifikasi admin.');
    }
}
