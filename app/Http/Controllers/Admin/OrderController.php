<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['buyer', 'store', 'items']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment status
        if ($request->filled('payment')) {
            $query->where('payment_status', $request->payment);
        }

        // Search by invoice
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('buyer', function ($sub) use ($search) {
                      $sub->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('store', function ($sub) use ($search) {
                      $sub->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $orders = $query->latest()->paginate(15)->withQueryString();

        // Stats
        $totalOrders = Order::count();
        $pendingPayment = Order::where('status', 'pending_payment')->count();
        $processing = Order::where('status', 'processing')->count();
        $shipped = Order::where('status', 'shipped')->count();
        $completed = Order::where('status', 'completed')->count();
        $cancelled = Order::where('status', 'cancelled')->count();

        return view('admin.orders.index', compact(
            'orders', 'totalOrders', 'pendingPayment', 'processing', 'shipped', 'completed', 'cancelled'
        ));
    }

    public function show($id)
    {
        $order = Order::with(['buyer', 'store', 'items.product.primaryImage'])->findOrFail($id);

        return view('admin.orders.show', compact('order'));
    }

    public function report(Request $request)
    {
        $query = Order::with(['buyer', 'store', 'items']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('payment')) {
            $query->where('payment_status', $request->payment);
        }
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('buyer', fn ($sub) => $sub->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('store', fn ($sub) => $sub->where('name', 'like', "%{$search}%"));
            });
        }

        $orders = $query->latest()->get();

        $summary = [
            'count' => $orders->count(),
            'gross' => $orders->sum('total_price'),
            'paidGross' => $orders->whereIn('status', ['processing', 'shipped', 'completed'])->sum('total_price'),
            'completed' => $orders->where('status', 'completed')->count(),
            'inProgress' => $orders->whereIn('status', ['processing', 'shipped'])->count(),
            'cancelled' => $orders->whereIn('status', ['cancelled', 'cancellation_requested'])->count(),
        ];

        return view('admin.orders.report', compact('orders', 'summary'));
    }
}
