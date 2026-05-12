<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RecoveryTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class RecoveryTicketController extends Controller
{
    public function index(Request $request)
    {
        $query = RecoveryTicket::with('user')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('username', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%');
            });
        }

        $tickets = $query->paginate(15)->withQueryString();
        $counts = [
            'pending_admin' => RecoveryTicket::where('status', 'pending_admin')->count(),
            'sent' => RecoveryTicket::where('status', 'sent')->count(),
            'resolved' => RecoveryTicket::where('status', 'resolved')->count(),
            'expired' => RecoveryTicket::where('status', 'expired')->count(),
        ];

        return view('admin.recovery-tickets.index', compact('tickets', 'counts'));
    }

    public function resendResetLink(Request $request, RecoveryTicket $ticket)
    {
        $ticket->load('user');

        if (!$ticket->user || !$ticket->user->email) {
            return back()->with('error', 'User pada tiket ini tidak memiliki email aktif.');
        }

        $status = Password::sendResetLink(['email' => $ticket->user->email]);

        if ($status === Password::RESET_LINK_SENT) {
            $ticket->update([
                'status' => 'sent',
                'admin_notes' => trim(($ticket->admin_notes ? $ticket->admin_notes . "\n" : '') . 'Admin mengirim ulang link reset password pada ' . now()->translatedFormat('d M Y H:i') . '.'),
            ]);

            return back()->with('success', 'Link reset password berhasil dikirim ulang ke email user.');
        }

        return back()->with('error', 'Email reset password gagal dikirim. Periksa konfigurasi SMTP atau coba lagi nanti.');
    }

    public function resolve(Request $request, RecoveryTicket $ticket)
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $ticket->update([
            'status' => 'resolved',
            'admin_notes' => $request->admin_notes ?: $ticket->admin_notes,
        ]);

        return back()->with('success', 'Tiket pemulihan ditandai selesai.');
    }

    public function expire(Request $request, RecoveryTicket $ticket)
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $ticket->update([
            'status' => 'expired',
            'admin_notes' => $request->admin_notes ?: $ticket->admin_notes,
        ]);

        return back()->with('success', 'Tiket pemulihan ditutup.');
    }
}
