<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Car;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_customers' => User::where('role', 'customer')->count(),
            'total_cars' => Car::count(),
            'available_cars' => Car::where('status', 'available')->count(),
            'rented_cars' => Car::where('status', 'rented')->count(),
            'total_bookings' => Booking::count(),
            'pending_bookings' => Booking::where('status', 'pending')->count(),
            'confirmed_bookings' => Booking::where('status', 'confirmed')->count(),
            'pending_payments' => Payment::where('payment_status', 'pending')->count(),
            'total_revenue' => Payment::where('payment_status', 'verified')->sum('amount'),
            'monthly_revenue' => Payment::where('payment_status', 'verified')
                ->whereMonth('verified_at', Carbon::now()->month)
                ->sum('amount')
        ];

        $recent_bookings = Booking::with(['user', 'car'])
            ->latest()
            ->take(5)
            ->get();

        $recent_payments = Payment::with(['booking.user', 'booking.car'])
            ->where('payment_status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        $monthly_bookings = Booking::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as count')
        )
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'recent_bookings',
            'recent_payments',
            'monthly_bookings'
        ));
    }

    public function reports()
    {
        $monthly_revenue = Payment::select(
            DB::raw('MONTH(verified_at) as month'),
            DB::raw('YEAR(verified_at) as year'),
            DB::raw('SUM(amount) as total')
        )
            ->where('payment_status', 'verified')
            ->whereYear('verified_at', Carbon::now()->year)
            ->groupBy('month', 'year')
            ->orderBy('month')
            ->get();

        $popular_cars = Car::select('cars.*', DB::raw('COUNT(bookings.id) as booking_count'))
            ->leftJoin('bookings', 'cars.id', '=', 'bookings.car_id')
            ->groupBy('cars.id')
            ->orderBy('booking_count', 'desc')
            ->take(10)
            ->get();

        $customer_stats = User::select('users.*', DB::raw('COUNT(bookings.id) as booking_count'))
            ->leftJoin('bookings', 'users.id', '=', 'bookings.user_id')
            ->where('users.role', 'customer')
            ->groupBy('users.id')
            ->orderBy('booking_count', 'desc')
            ->take(10)
            ->get();

        return view('admin.reports', compact(
            'monthly_revenue',
            'popular_cars',
            'customer_stats'
        ));
    }
}
