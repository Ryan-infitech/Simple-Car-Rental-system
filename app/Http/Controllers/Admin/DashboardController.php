<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Car;
use App\Models\Booking;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Basic statistics
        $stats = [
            'total_customers' => User::where('role', 'customer')->count(),
            'total_cars' => Car::count(),
            'available_cars' => Car::where('status', 'available')->count(),
            'rented_cars' => Car::where('status', 'rented')->count(),
            'total_bookings' => Booking::count(),
            'pending_bookings' => Booking::where('status', 'pending')->count(),
            'confirmed_bookings' => Booking::where('status', 'confirmed')->count(),
            'total_revenue' => 100000000, // Dummy data
            'monthly_revenue' => 25000000, // Dummy data
        ];
        
        // Recent activities
        $recentBookings = collect([]); // Empty collection for now
        $pendingPayments = collect([]); // Empty collection for now
        
        return view('admin.dashboard', compact('stats', 'recentBookings', 'pendingPayments'));
    }

    public function quickStats()
    {
        return response()->json([
            'pending_bookings' => Booking::where('status', 'pending')->count(),
            'available_cars' => Car::where('status', 'available')->count(),
            'today_bookings' => Booking::whereDate('created_at', today())->count(),
        ]);
    }
}
