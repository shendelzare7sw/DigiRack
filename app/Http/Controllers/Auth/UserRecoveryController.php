<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\RecoveryTicket;
use App\Models\User;
use App\Notifications\StoreStatusNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class UserRecoveryController extends Controller
{
    /**
     * Show the recovery form.
     */
    public function showForm()
    {
        return view('auth.user-recovery');
    }

    /**
     * Process recovery request: find user, create ticket, attempt email or fallback to admin.
     */
    public function store(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string',
        ]);

        $identifier = trim($request->identifier);

        // Find user flexibly
        $user = User::where('email', $identifier)
                    ->orWhere('username', $identifier)
                    ->orWhere('phone', $identifier)
                    ->first();

        if (!$user) {
            return back()->withInput()->withErrors([
                'identifier' => 'Akun dengan data tersebut tidak ditemukan di sistem kami.'
            ]);
        }

        // Block admin accounts from using public recovery
        if ($user->isAdmin()) {
            return back()->withInput()->withErrors([
                'identifier' => 'Akun administrator tidak dapat direset melalui jalur ini. Hubungi tim internal.'
            ]);
        }

        $token = Str::random(64);

        // Clean up old pending tickets for this user
        RecoveryTicket::where('user_id', $user->id)
                      ->whereIn('status', ['processing', 'pending_admin', 'sent'])
                      ->update(['status' => 'expired']);

        // Scenario A: User has email → send reset via Laravel Password Broker
        if ($user->email) {
            $ticket = RecoveryTicket::create([
                'user_id' => $user->id,
                'tipe_recovery' => 'lupa_password',
                'status' => 'processing',
                'token_reset' => $token,
                'expires_at' => Carbon::now()->addHours(24),
            ]);

            // Use Laravel's built-in password broker to send reset link
            $status = Password::sendResetLink(['email' => $user->email]);

            if ($status === Password::RESET_LINK_SENT) {
                $ticket->update(['status' => 'sent']);
                return redirect()->route('login')->with('success', 'Link pemulihan password telah dikirim ke email ' . Str::mask($user->email, '*', 3, -8) . '. Periksa inbox Anda.');
            } else {
                // Email failed → fallback to admin
                $ticket->update(['status' => 'pending_admin']);

                // Notify all admins about pending ticket
                $this->notifyAdmins($ticket);

                return redirect()->route('login')->with('warning', 'Sistem email sedang bermasalah. Tiket pemulihan #' . $ticket->id . ' telah diteruskan ke tim Customer Service. Harap tunggu.');
            }
        }

        // Scenario B: No email → direct fallback to admin
        $ticket = RecoveryTicket::create([
            'user_id' => $user->id,
            'tipe_recovery' => 'lupa_password',
            'status' => 'pending_admin',
            'token_reset' => $token,
            'expires_at' => Carbon::now()->addHours(24),
        ]);

        $this->notifyAdmins($ticket);

        return redirect()->route('login')->with('info', 'Tiket pemulihan #' . $ticket->id . ' telah dibuat. Tim Customer Service akan menghubungi Anda melalui nomor telepon terdaftar untuk verifikasi identitas.');
    }

    /**
     * Notify all admin users about a pending recovery ticket.
     */
    protected function notifyAdmins(RecoveryTicket $ticket)
    {
        try {
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                $admin->notify(new StoreStatusNotification(
                    'recovery_ticket',
                    '🎫 Tiket Pemulihan Akun Baru',
                    'User "' . $ticket->user->name . '" meminta reset password. Tiket #' . $ticket->id . ' menunggu verifikasi manual.',
                    route('admin.recovery-tickets.index'),
                    '🆘'
                ));
            }
        } catch (\Exception $e) {
            // Notification failure should not break recovery flow
        }
    }
}
