<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index(Request $request)
    {
        $query = Booking::with(['user', 'car', 'payment']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('start_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('end_date', '<=', $request->end_date);
        }

        // Search by customer name or car
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('car', function ($carQuery) use ($search) {
                    $carQuery->where('brand', 'like', "%{$search}%")
                        ->orWhere('model', 'like', "%{$search}%")
                        ->orWhere('license_plate', 'like', "%{$search}%");
                });
            });
        }

        $bookings = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.bookings.index', compact('bookings'));
    }

    public function show(Booking $booking)
    {
        $booking->load(['user', 'car', 'payment']);
        return view('admin.bookings.show', compact('booking'));
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,ongoing,completed,cancelled',
            'notes' => 'nullable|string|max:500'
        ]);

        $oldStatus = $booking->status;
        $newStatus = $request->status;

        $booking->update([
            'status' => $newStatus
        ]);

        // Update car status based on booking status
        if ($newStatus === 'ongoing') {
            $booking->car->update(['status' => 'rented']);
        } elseif ($newStatus === 'completed' || $newStatus === 'cancelled') {
            $booking->car->update(['status' => 'available']);
        }

        // Create notification for customer
        $this->notificationService->createNotification(
            $booking->user_id,
            'Status Booking Diperbarui',
            "Status booking #{$booking->id} telah diubah dari {$oldStatus} menjadi {$newStatus}",
            'booking'
        );

        return back()->with('success', 'Status booking berhasil diperbarui');
    }

    public function approve(Booking $booking)
    {
        if ($booking->status !== 'pending') {
            return back()->with('error', 'Booking ini tidak dapat disetujui');
        }

        $booking->update(['status' => 'confirmed']);

        $this->notificationService->createNotification(
            $booking->user_id,
            'Booking Disetujui',
            "Booking #{$booking->id} telah disetujui. Silakan lakukan pembayaran.",
            'booking'
        );

        return back()->with('success', 'Booking berhasil disetujui');
    }

    public function reject(Request $request, Booking $booking)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        if ($booking->status !== 'pending') {
            return back()->with('error', 'Booking ini tidak dapat ditolak');
        }

        $booking->update(['status' => 'cancelled']);
        $booking->car->update(['status' => 'available']);

        $this->notificationService->createNotification(
            $booking->user_id,
            'Booking Ditolak',
            "Booking #{$booking->id} ditolak. Alasan: {$request->reason}",
            'booking'
        );

        return back()->with('success', 'Booking berhasil ditolak');
    }

    public function destroy(Booking $booking)
    {
        if ($booking->status === 'ongoing') {
            return back()->with('error', 'Tidak dapat menghapus booking yang sedang berlangsung');
        }

        // Delete related payment if exists
        if ($booking->payment) {
            $booking->payment->delete();
        }

        $booking->delete();

        return redirect()->route('admin.bookings.index')
            ->with('success', 'Booking berhasil dihapus');
    }
}
