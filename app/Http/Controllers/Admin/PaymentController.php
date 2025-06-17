<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index(Request $request)
    {
        $query = Payment::with(['booking.user', 'booking.car']);

        // Filter by payment status
        if ($request->filled('status')) {
            $query->where('payment_status', $request->status);
        }

        // Filter by payment method
        if ($request->filled('method')) {
            $query->where('payment_method', $request->method);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('payment_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('payment_date', '<=', $request->date_to);
        }

        // Search by booking ID or customer name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhere('booking_id', 'like', "%{$search}%")
                    ->orWhereHas('booking.user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.payments.index', compact('payments'));
    }

    public function show(Payment $payment)
    {
        $payment->load(['booking.user', 'booking.car', 'verifiedBy']);
        return view('admin.payments.show', compact('payment'));
    }

    public function verify(Payment $payment)
    {
        if ($payment->payment_status !== 'pending') {
            return back()->with('error', 'Pembayaran ini tidak dapat diverifikasi');
        }

        $payment->update([
            'payment_status' => 'verified',
            'verified_at' => now(),
            'verified_by' => auth()->id()
        ]);

        // Update booking status
        $payment->booking->update(['status' => 'confirmed']);

        // Create notification for customer
        $this->notificationService->createNotification(
            $payment->booking->user_id,
            'Pembayaran Diverifikasi',
            "Pembayaran untuk booking #{$payment->booking->id} telah diverifikasi. Mobil siap digunakan!",
            'payment'
        );

        return back()->with('success', 'Pembayaran berhasil diverifikasi');
    }

    public function reject(Request $request, Payment $payment)
    {
        $request->validate([
            'notes' => 'required|string|max:500'
        ]);

        if ($payment->payment_status !== 'pending') {
            return back()->with('error', 'Pembayaran ini tidak dapat ditolak');
        }

        $payment->update([
            'payment_status' => 'rejected',
            'notes' => $request->notes
        ]);

        // Update booking status back to pending
        $payment->booking->update(['status' => 'pending']);

        // Create notification for customer
        $this->notificationService->createNotification(
            $payment->booking->user_id,
            'Pembayaran Ditolak',
            "Pembayaran untuk booking #{$payment->booking->id} ditolak. Alasan: {$request->notes}",
            'payment'
        );

        return back()->with('success', 'Pembayaran berhasil ditolak');
    }

    public function downloadProof(Payment $payment)
    {
        if (!$payment->payment_proof) {
            return back()->with('error', 'Bukti pembayaran tidak tersedia');
        }

        $filePath = storage_path('app/public/' . $payment->payment_proof);
        
        if (!file_exists($filePath)) {
            return back()->with('error', 'File bukti pembayaran tidak ditemukan');
        }

        return response()->download($filePath, "bukti_pembayaran_{$payment->id}.jpg");
    }

    public function bulkVerify(Request $request)
    {
        $request->validate([
            'payment_ids' => 'required|array',
            'payment_ids.*' => 'exists:payments,id'
        ]);

        $payments = Payment::whereIn('id', $request->payment_ids)
            ->where('payment_status', 'pending')
            ->get();

        $verifiedCount = 0;

        foreach ($payments as $payment) {
            $payment->update([
                'payment_status' => 'verified',
                'verified_at' => now(),
                'verified_by' => auth()->id()
            ]);

            // Update booking status
            $payment->booking->update(['status' => 'confirmed']);

            // Create notification for customer
            $this->notificationService->createNotification(
                $payment->booking->user_id,
                'Pembayaran Diverifikasi',
                "Pembayaran untuk booking #{$payment->booking->id} telah diverifikasi.",
                'payment'
            );

            $verifiedCount++;
        }

        return back()->with('success', "{$verifiedCount} pembayaran berhasil diverifikasi");
    }
}
