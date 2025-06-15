<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Car;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BookingService
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Create a new booking
     *
     * @param array $data
     * @param int $userId
     * @return Booking
     */
    public function createBooking(array $data, $userId)
    {
        // Calculate total days and price
        $startDate = Carbon::parse($data['start_date']);
        $endDate = Carbon::parse($data['end_date']);
        $totalDays = $startDate->diffInDays($endDate) + 1;
        
        $car = Car::findOrFail($data['car_id']);
        $totalPrice = $totalDays * $car->price_per_day;

        $booking = Booking::create([
            'user_id' => $userId,
            'car_id' => $data['car_id'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'total_days' => $totalDays,
            'total_price' => $totalPrice,
            'pickup_location' => $data['pickup_location'],
            'return_location' => $data['return_location'],
            'notes' => $data['notes'] ?? null,
            'status' => 'pending'
        ]);

        // Notify admins about new booking
        $this->notificationService->notifyNewBooking($booking);

        return $booking;
    }

    /**
     * Check if car is available for given dates
     *
     * @param int $carId
     * @param string $startDate
     * @param string $endDate
     * @param int|null $excludeBookingId
     * @return bool
     */
    public function isCarAvailable($carId, $startDate, $endDate, $excludeBookingId = null)
    {
        $car = Car::find($carId);
        
        if (!$car || $car->status !== 'available') {
            return false;
        }

        $conflictingBookings = Booking::where('car_id', $carId)
            ->when($excludeBookingId, function ($query) use ($excludeBookingId) {
                return $query->where('id', '!=', $excludeBookingId);
            })
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($q) use ($startDate, $endDate) {
                        $q->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                    });
            })
            ->whereIn('status', ['pending', 'confirmed', 'ongoing'])
            ->exists();

        return !$conflictingBookings;
    }

    /**
     * Update booking status
     *
     * @param Booking $booking
     * @param string $newStatus
     * @param string|null $reason
     * @return bool
     */
    public function updateBookingStatus(Booking $booking, $newStatus, $reason = null)
    {
        $oldStatus = $booking->status;
        
        $updated = $booking->update(['status' => $newStatus]);

        if ($updated) {
            // Update car status based on booking status
            $this->updateCarStatus($booking, $newStatus);

            // Send notification to customer
            if ($newStatus === 'cancelled' && $reason) {
                $this->notificationService->notifyBookingCancelled($booking, $reason);
            } else {
                $this->notificationService->notifyBookingStatusChanged($booking, $oldStatus, $newStatus);
            }
        }

        return $updated;
    }

    /**
     * Update car status based on booking status
     *
     * @param Booking $booking
     * @param string $bookingStatus
     * @return void
     */
    protected function updateCarStatus(Booking $booking, $bookingStatus)
    {
        switch ($bookingStatus) {
            case 'ongoing':
                $booking->car->update(['status' => 'rented']);
                break;
            case 'completed':
            case 'cancelled':
                // Check if there are other ongoing bookings for this car
                $hasOtherOngoingBookings = Booking::where('car_id', $booking->car_id)
                    ->where('id', '!=', $booking->id)
                    ->where('status', 'ongoing')
                    ->exists();
                
                if (!$hasOtherOngoingBookings) {
                    $booking->car->update(['status' => 'available']);
                }
                break;
        }
    }

    /**
     * Calculate booking price
     *
     * @param int $carId
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public function calculateBookingPrice($carId, $startDate, $endDate)
    {
        $car = Car::findOrFail($carId);
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);
        $totalDays = $startDate->diffInDays($endDate) + 1;
        $totalPrice = $totalDays * $car->price_per_day;

        return [
            'total_days' => $totalDays,
            'price_per_day' => $car->price_per_day,
            'total_price' => $totalPrice,
            'formatted_price_per_day' => 'Rp ' . number_format($car->price_per_day, 0, ',', '.'),
            'formatted_total_price' => 'Rp ' . number_format($totalPrice, 0, ',', '.')
        ];
    }

    /**
     * Get booking statistics
     *
     * @param array $filters
     * @return array
     */
    public function getBookingStatistics($filters = [])
    {
        $query = Booking::query();

        // Apply date filters
        if (isset($filters['start_date'])) {
            $query->whereDate('start_date', '>=', $filters['start_date']);
        }

        if (isset($filters['end_date'])) {
            $query->whereDate('end_date', '<=', $filters['end_date']);
        }

        // Apply user filter
        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        return [
            'total_bookings' => $query->count(),
            'pending_bookings' => $query->where('status', 'pending')->count(),
            'confirmed_bookings' => $query->where('status', 'confirmed')->count(),
            'ongoing_bookings' => $query->where('status', 'ongoing')->count(),
            'completed_bookings' => $query->where('status', 'completed')->count(),
            'cancelled_bookings' => $query->where('status', 'cancelled')->count(),
            'total_revenue' => $query->whereHas('payment', function ($q) {
                $q->where('payment_status', 'verified');
            })->sum('total_price'),
        ];
    }

    /**
     * Get available cars for given dates
     *
     * @param string $startDate
     * @param string $endDate
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAvailableCars($startDate, $endDate)
    {
        $bookedCarIds = Booking::where(function ($query) use ($startDate, $endDate) {
            $query->whereBetween('start_date', [$startDate, $endDate])
                ->orWhereBetween('end_date', [$startDate, $endDate])
                ->orWhere(function ($q) use ($startDate, $endDate) {
                    $q->where('start_date', '<=', $startDate)
                        ->where('end_date', '>=', $endDate);
                });
        })
        ->whereIn('status', ['pending', 'confirmed', 'ongoing'])
        ->pluck('car_id')
        ->toArray();

        return Car::where('status', 'available')
            ->whereNotIn('id', $bookedCarIds)
            ->with('images')
            ->get();
    }

    /**
     * Get upcoming bookings that need attention
     *
     * @param int $days
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUpcomingBookings($days = 7)
    {
        return Booking::with(['user', 'car'])
            ->where('status', 'confirmed')
            ->whereBetween('start_date', [
                now()->toDateString(),
                now()->addDays($days)->toDateString()
            ])
            ->orderBy('start_date')
            ->get();
    }

    /**
     * Get overdue bookings that should be marked as ongoing
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getOverdueBookings()
    {
        return Booking::with(['user', 'car'])
            ->where('status', 'confirmed')
            ->where('start_date', '<', now()->toDateString())
            ->get();
    }

    /**
     * Auto update booking statuses
     *
     * @return array
     */
    public function autoUpdateBookingStatuses()
    {
        $today = now()->toDateString();
        $updated = ['ongoing' => 0, 'completed' => 0];

        // Mark bookings as ongoing if start date is today or past
        $toOngoing = Booking::where('status', 'confirmed')
            ->where('start_date', '<=', $today)
            ->get();

        foreach ($toOngoing as $booking) {
            $this->updateBookingStatus($booking, 'ongoing');
            $updated['ongoing']++;
        }

        // Mark bookings as completed if end date is past
        $toCompleted = Booking::where('status', 'ongoing')
            ->where('end_date', '<', $today)
            ->get();

        foreach ($toCompleted as $booking) {
            $this->updateBookingStatus($booking, 'completed');
            $updated['completed']++;
        }

        return $updated;
    }

    /**
     * Cancel booking
     *
     * @param Booking $booking
     * @param string|null $reason
     * @return bool
     */
    public function cancelBooking(Booking $booking, $reason = null)
    {
        if (!in_array($booking->status, ['pending', 'confirmed'])) {
            return false;
        }

        return $this->updateBookingStatus($booking, 'cancelled', $reason);
    }

    /**
     * Get popular cars based on booking frequency
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPopularCars($limit = 10)
    {
        return Car::select('cars.*', DB::raw('COUNT(bookings.id) as booking_count'))
            ->leftJoin('bookings', 'cars.id', '=', 'bookings.car_id')
            ->groupBy('cars.id')
            ->orderBy('booking_count', 'desc')
            ->take($limit)
            ->get();
    }
}
