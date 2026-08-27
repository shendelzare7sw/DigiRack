<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Store;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\RecoveryTicket;
use App\Models\IdentityVerification;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Basic Counters
        $stats = [
            'totalUsers' => User::count(),
            'totalBuyers' => User::where('role', 'buyer')->count(),
            'pendingIdentityCount' => IdentityVerification::where('status', IdentityVerification::STATUS_PENDING)->count(),
            'totalProducts' => Product::count(),
            'activeProducts' => Product::where('status', 'active')->count(),
            'totalCategories' => Category::count(),
            'totalOrders' => Order::count(),
            'totalRevenue' => Order::where('payment_status', 'paid')->sum('total_price'),
            'pendingRecoveryTickets' => RecoveryTicket::where('status', 'pending_admin')->count(),
        ];

        // 2. Revenue Time-Series (Last 30 Days GMV)
        $dates = [];
        $revenues = [];
        
        $startDate = Carbon::today()->subDays(29);
        
        // Populate defaults (zeros)
        for ($i = 0; $i < 30; $i++) {
            $dateString = $startDate->copy()->addDays($i)->format('Y-m-d');
            $dates[$dateString] = Carbon::parse($dateString)->translatedFormat('d M');
            $revenues[$dateString] = 0;
        }

        // Query database for orders within the period
        $orders = Order::where('payment_status', 'paid')
            ->where('created_at', '>=', $startDate->startOfDay())
            ->get();

        foreach ($orders as $order) {
            $dateStr = $order->created_at->format('Y-m-d');
            if (isset($revenues[$dateStr])) {
                $revenues[$dateStr] += $order->total_price;
            }
        }

        $chartDates = array_values($dates);
        $chartRevenues = array_values($revenues);

        return view('admin.dashboard', compact('stats', 'chartDates', 'chartRevenues'));
    }
}
