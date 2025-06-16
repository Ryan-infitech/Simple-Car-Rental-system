<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Booking;
use Symfony\Component\HttpFoundation\Response;

class CheckBookingOwnership
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get booking ID from route parameter
        $bookingId = $request->route('booking');
        
        if ($bookingId) {
            // If it's a Booking model instance
            if ($bookingId instanceof Booking) {
                $booking = $bookingId;
            } else {
                // If it's just an ID, find the booking
                $booking = Booking::find($bookingId);
            }

            if ($booking) {
                // Check if user owns this booking or is admin
                if (auth()->user()->role !== 'admin' && $booking->user_id !== auth()->id()) {
                    return redirect()->route('customer.bookings.index')
                        ->with('error', 'Anda tidak memiliki akses ke booking ini.');
                }
            }
        }

        return $next($request);
    }
}
