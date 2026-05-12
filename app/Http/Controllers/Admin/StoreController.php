<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Notifications\StoreStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StoreController extends Controller
{
    public function index(Request $request)
    {
        $query = Store::with('user')->withCount('products');

        if ($request->filled('status')) {
            if ($request->status === 'verified') {
                $query->where(function ($q) {
                    $q->where('verification_status', 'approved')
                        ->orWhere('is_verified', true);
                });
            } elseif ($request->status === 'pending') {
                $query->where('verification_status', 'pending')->where('is_verified', false);
            } elseif ($request->status === 'rejected') {
                $query->where('verification_status', 'rejected');
            }
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhereHas('user', function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%');
                });
        }

        $stores = $query->orderBy('created_at', 'desc')->paginate(15)->appends($request->query());
        $pendingCount = Store::where('verification_status', 'pending')->where('is_verified', false)->count();

        return view('admin.stores.index', compact('stores', 'pendingCount'));
    }

    public function show($id)
    {
        $store = Store::with('user')->withCount('products')->findOrFail($id);

        return view('admin.stores.show', compact('store'));
    }

    public function toggleVerification(Request $request, $id)
    {
        $store = Store::with('user')->findOrFail($id);

        DB::transaction(function () use ($store) {
            $store->is_verified = ! $store->is_verified;

            if ($store->is_verified) {
                $store->is_active = true;
                $store->verification_status = 'approved';
                $store->verification_notes = null;
                $store->verified_at = now();

                if ($store->user && $store->user->role !== 'admin') {
                    $store->user->role = 'seller';
                    $store->user->save();
                }
            } else {
                $store->is_active = false;
                $store->verification_status = 'pending';
                $store->verified_at = null;

                if ($store->user && $store->user->role !== 'admin') {
                    $store->user->role = 'buyer';
                    $store->user->save();
                }
            }

            $store->save();
        });

        try {
            $sellerUser = $store->user;
            if ($sellerUser) {
                if ($store->is_verified) {
                    $sellerUser->notify(new StoreStatusNotification(
                        'store_verified',
                        'Toko Anda telah terverifikasi',
                        'Selamat! Identitas bisnis toko "' . $store->name . '" telah lolos kurasi Admin. Anda kini dapat berjualan dan menerima pencairan dana.',
                        route('seller.dashboard'),
                        'OK'
                    ));
                } else {
                    $sellerUser->notify(new StoreStatusNotification(
                        'store_unverified',
                        'Status verifikasi toko dicabut',
                        'Status verifikasi toko "' . $store->name . '" telah dicabut oleh Admin. Silakan hubungi support untuk informasi lebih lanjut.',
                        route('seller.dashboard'),
                        '!'
                    ));
                }
            }
        } catch (\Exception $e) {
            report($e);
        }

        $status = $store->is_verified ? 'diverifikasi' : 'dicabut verifikasinya';

        return back()->with('success', "Toko {$store->name} berhasil {$status}.");
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'verification_notes' => 'required|string|max:1000',
        ]);

        $store = Store::with('user')->findOrFail($id);

        DB::transaction(function () use ($store, $request) {
            $store->is_verified = false;
            $store->is_active = false;
            $store->verification_status = 'rejected';
            $store->verification_notes = $request->verification_notes;
            $store->verified_at = null;
            $store->save();

            if ($store->user && $store->user->role !== 'admin') {
                $store->user->role = 'buyer';
                $store->user->save();
            }
        });

        try {
            $sellerUser = $store->user;
            if ($sellerUser) {
                $sellerUser->notify(new StoreStatusNotification(
                    'store_rejected',
                    'Pengajuan toko ditolak',
                    'Pengajuan toko "' . $store->name . '" ditolak. Catatan admin: ' . $request->verification_notes,
                    route('seller.dashboard'),
                    '!'
                ));
            }
        } catch (\Exception $e) {
            report($e);
        }

        return back()->with('success', "Pengajuan toko {$store->name} berhasil ditolak.");
    }

    public function toggleActive(Request $request, $id)
    {
        $store = Store::with('user')->findOrFail($id);

        if (! $store->is_verified) {
            return back()->with('success', 'Toko belum lolos verifikasi, jadi status aktif belum bisa diubah.');
        }

        $store->is_active = ! $store->is_active;
        $store->save();

        try {
            $sellerUser = $store->user;
            if ($sellerUser) {
                if ($store->is_active) {
                    $sellerUser->notify(new StoreStatusNotification(
                        'store_restored',
                        'Toko Anda telah dipulihkan',
                        'Toko "' . $store->name . '" telah diaktifkan kembali oleh Admin. Selamat berjualan!',
                        route('seller.dashboard'),
                        'OK'
                    ));
                } else {
                    $sellerUser->notify(new StoreStatusNotification(
                        'store_banned',
                        'Toko Anda dinonaktifkan',
                        'Toko "' . $store->name . '" telah dinonaktifkan oleh Admin. Seluruh produk Anda tidak akan tampil di marketplace.',
                        route('seller.dashboard'),
                        '!'
                    ));
                }
            }
        } catch (\Exception $e) {
            report($e);
        }

        $status = $store->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Toko {$store->name} berhasil {$status}.");
    }
}
