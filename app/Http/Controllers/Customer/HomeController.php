<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\Booking;
use App\Models\Notification;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('customer');
    }

    public function index()
    {
        // Get featured cars (available cars with good ratings or most popular)
        $featuredCars = Car::where('status', 'available')
            ->with('images')
            ->take(6)
            ->get();

        // Get user's booking statistics
        $user = auth()->user();
        $bookingStats = [
            'total_bookings' => $user->bookings()->count(),
            'pending_bookings' => $user->bookings()->where('status', 'pending')->count(),
            'confirmed_bookings' => $user->bookings()->where('status', 'confirmed')->count(),
            'ongoing_bookings' => $user->bookings()->where('status', 'ongoing')->count(),
            'completed_bookings' => $user->bookings()->where('status', 'completed')->count(),
        ];

        // Get recent bookings
        $recentBookings = $user->bookings()
            ->with(['car', 'payment'])
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        // Get unread notifications
        $unreadNotifications = $user->notifications()
            ->where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('customer.dashboard', compact(
            'featuredCars',
            'bookingStats',
            'recentBookings',
            'unreadNotifications'
        ));
    }

    public function dashboard()
    {
        return $this->index();
    }
}
