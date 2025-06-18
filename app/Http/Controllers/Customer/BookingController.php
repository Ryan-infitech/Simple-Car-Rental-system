<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\Booking;
use App\Services\NotificationService;
use App\Http\Requests\BookingRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BookingController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->middleware('auth');
        $this->middleware('customer');
        $this->notificationService = $notificationService;
    }

    public function index(Request $request)
    {
        $query = auth()->user()->bookings()->with(['car', 'payment']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('start_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('end_date', '<=', $request->date_to);
        }

        $bookings = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('customer.bookings.index', compact('bookings'));
    }

    public function create(Request $request)
    {
        $carId = $request->get('car_id');
        $car = null;

        if ($carId) {
            $car = Car::where('id', $carId)
                ->where('status', 'available')
                ->first();

            if (!$car) {
                return redirect()->route('customer.cars.index')
                    ->with('error', 'Mobil tidak ditemukan atau tidak tersedia.');
            }
        }

        return view('customer.bookings.create', compact('car'));
    }

    public function store(BookingRequest $request)
    {
        $car = Car::findOrFail($request->car_id);

        if ($car->status !== 'available') {
            return back()->with('error', 'Mobil tidak tersedia untuk disewa.');
        }

        // Check availability again
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        $conflictingBookings = $car->bookings()
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

        if ($conflictingBookings) {
            return back()->with('error', 'Mobil tidak tersedia pada tanggal yang dipilih.');
        }

        // Calculate total days and price
        $totalDays = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1;
        $totalPrice = $totalDays * $car->price_per_day;

        // Create booking
        $booking = Booking::create([
            'user_id' => auth()->id(),
            'car_id' => $car->id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total_days' => $totalDays,
            'total_price' => $totalPrice,
            'pickup_location' => $request->pickup_location,
            'return_location' => $request->return_location,
            'notes' => $request->notes,
            'status' => 'pending'
        ]);

        // Create notification for admins
        $this->notificationService->createNotification(
            1, // Assume admin user ID is 1, in real app get all admins
            'Booking Baru',
            "Booking baru #{$booking->id} telah dibuat oleh " . auth()->user()->name,
            'booking'
        );

        return redirect()->route('customer.bookings.show', $booking->id)
            ->with('success', 'Booking berhasil dibuat. Silakan tunggu konfirmasi dari admin.');
    }

    public function show(Booking $booking)
    {
        // Check if booking belongs to the authenticated user
        if ($booking->user_id !== auth()->id()) {
            return redirect()->route('customer.bookings.index')
                ->with('error', 'Booking tidak ditemukan.');
        }

        $booking->load(['car', 'payment']);

        return view('customer.bookings.show', compact('booking'));
    }

    public function edit(Booking $booking)
    {
        // Check if booking belongs to the authenticated user
        if ($booking->user_id !== auth()->id()) {
            return redirect()->route('customer.bookings.index')
                ->with('error', 'Booking tidak ditemukan.');
        }

        // Only allow editing if booking is still pending
        if ($booking->status !== 'pending') {
            return back()->with('error', 'Booking tidak dapat diubah karena sudah dikonfirmasi atau selesai.');
        }

        return view('customer.bookings.edit', compact('booking'));
    }

    public function update(BookingRequest $request, Booking $booking)
    {
        // Check if booking belongs to the authenticated user
        if ($booking->user_id !== auth()->id()) {
            return redirect()->route('customer.bookings.index')
                ->with('error', 'Booking tidak ditemukan.');
        }

        // Only allow updating if booking is still pending
        if ($booking->status !== 'pending') {
            return back()->with('error', 'Booking tidak dapat diubah karena sudah dikonfirmasi atau selesai.');
        }

        $car = $booking->car;
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        // Check availability for new dates (excluding current booking)
        $conflictingBookings = $car->bookings()
            ->where('id', '!=', $booking->id)
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

        if ($conflictingBookings) {
            return back()->with('error', 'Mobil tidak tersedia pada tanggal yang dipilih.');
        }

        // Recalculate total days and price
        $totalDays = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1;
        $totalPrice = $totalDays * $car->price_per_day;

        $booking->update([
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total_days' => $totalDays,
            'total_price' => $totalPrice,
            'pickup_location' => $request->pickup_location,
            'return_location' => $request->return_location,
            'notes' => $request->notes,
        ]);

        return redirect()->route('customer.bookings.show', $booking->id)
            ->with('success', 'Booking berhasil diperbarui.');
    }

    public function destroy(Booking $booking)
    {
        // Check if booking belongs to the authenticated user
        if ($booking->user_id !== auth()->id()) {
            return redirect()->route('customer.bookings.index')
                ->with('error', 'Booking tidak ditemukan.');
        }

        // Only allow cancellation if booking is pending or confirmed
        if (!in_array($booking->status, ['pending', 'confirmed'])) {
            return back()->with('error', 'Booking tidak dapat dibatalkan.');
        }

        $booking->update(['status' => 'cancelled']);

        // Create notification for admins
        $this->notificationService->createNotification(
            1, // Assume admin user ID is 1
            'Booking Dibatalkan',
            "Booking #{$booking->id} telah dibatalkan oleh customer",
            'booking'
        );

        return redirect()->route('customer.bookings.index')
            ->with('success', 'Booking berhasil dibatalkan.');
    }
}
