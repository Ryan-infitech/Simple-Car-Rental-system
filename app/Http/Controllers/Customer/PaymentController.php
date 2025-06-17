<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Services\PaymentService;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected $paymentService;
    protected $notificationService;

    public function __construct(PaymentService $paymentService, NotificationService $notificationService)
    {
        $this->middleware('auth');
        $this->middleware('customer');
        $this->paymentService = $paymentService;
        $this->notificationService = $notificationService;
    }

    public function checkout(Booking $booking)
    {
        // Check if booking belongs to the authenticated user
        if ($booking->user_id !== auth()->id()) {
            return redirect()->route('customer.bookings.index')
                ->with('error', 'Booking tidak ditemukan.');
        }

        // Check if booking is confirmed and doesn't have payment yet
        if ($booking->status !== 'confirmed') {
            return back()->with('error', 'Booking belum dikonfirmasi oleh admin.');
        }

        if ($booking->payment && $booking->payment->payment_status !== 'rejected') {
            return back()->with('error', 'Pembayaran untuk booking ini sudah diproses.');
        }

        $bankAccounts = $this->paymentService->getBankAccounts();

        return view('customer.payments.checkout', compact('booking', 'bankAccounts'));
    }

    public function process(Request $request, Booking $booking)
    {
        $request->validate([
            'payment_method' => 'required|in:bca,mandiri,bni,bri',
        ], [
            'payment_method.required' => 'Metode pembayaran wajib dipilih.',
            'payment_method.in' => 'Metode pembayaran tidak valid.',
        ]);

        // Check if booking belongs to the authenticated user
        if ($booking->user_id !== auth()->id()) {
            return redirect()->route('customer.bookings.index')
                ->with('error', 'Booking tidak ditemukan.');
        }

        // Create or update payment
        $payment = $booking->payment;
        
        if ($payment && $payment->payment_status === 'rejected') {
            // Update existing rejected payment
            $payment->update([
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending',
                'payment_date' => now(),
                'payment_proof' => null,
                'notes' => null,
            ]);
        } else {
            // Create new payment
            $payment = Payment::create([
                'booking_id' => $booking->id,
                'amount' => $booking->total_price,
                'payment_method' => $request->payment_method,
                'payment_date' => now(),
            ]);
        }

        $bankAccount = $this->paymentService->getPaymentInstructions($request->payment_method);

        return view('customer.payments.transfer-instructions', compact('payment', 'booking', 'bankAccount'));
    }

    public function uploadProof(Request $request, Booking $booking)
    {
        $request->validate([
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ], [
            'payment_proof.required' => 'Bukti pembayaran wajib diupload.',
            'payment_proof.image' => 'File harus berupa gambar.',
            'payment_proof.mimes' => 'Format file harus jpeg, png, atau jpg.',
            'payment_proof.max' => 'Ukuran file maksimal 2MB.',
        ]);

        // Check if booking belongs to the authenticated user
        if ($booking->user_id !== auth()->id()) {
            return redirect()->route('customer.bookings.index')
                ->with('error', 'Booking tidak ditemukan.');
        }

        $payment = $booking->payment;

        if (!$payment || $payment->payment_status === 'verified') {
            return back()->with('error', 'Pembayaran tidak ditemukan atau sudah diverifikasi.');
        }

        if ($request->hasFile('payment_proof')) {
            // Delete old proof if exists
            if ($payment->payment_proof) {
                \Storage::disk('public')->delete($payment->payment_proof);
            }

            $path = $request->file('payment_proof')->store('payment_proofs', 'public');
            $payment->update(['payment_proof' => $path]);
        }

        // Notify admins about payment proof upload
        $this->notificationService->notifyPaymentUploaded($booking);

        return redirect()->route('customer.bookings.show', $booking->id)
            ->with('success', 'Bukti pembayaran berhasil diupload. Admin akan memverifikasi dalam 1x24 jam.');
    }

    public function history()
    {
        $payments = auth()->user()->bookings()
            ->whereHas('payment')
            ->with(['payment', 'car'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('customer.payments.history', compact('payments'));
    }

    public function invoice(Booking $booking)
    {
        // Check if booking belongs to the authenticated user
        if ($booking->user_id !== auth()->id()) {
            return redirect()->route('customer.bookings.index')
                ->with('error', 'Booking tidak ditemukan.');
        }

        if (!$booking->payment || $booking->payment->payment_status !== 'verified') {
            return back()->with('error', 'Invoice hanya tersedia untuk pembayaran yang sudah diverifikasi.');
        }

        $booking->load(['car', 'payment']);

        return view('customer.payments.invoice', compact('booking'));
    }

    public function downloadInvoice(Booking $booking)
    {
        // Check if booking belongs to the authenticated user
        if ($booking->user_id !== auth()->id()) {
            return redirect()->route('customer.bookings.index')
                ->with('error', 'Booking tidak ditemukan.');
        }

        if (!$booking->payment || $booking->payment->payment_status !== 'verified') {
            return back()->with('error', 'Invoice hanya tersedia untuk pembayaran yang sudah diverifikasi.');
        }

        $booking->load(['car', 'payment']);

        // Generate PDF (you'll need to install dompdf or similar)
        // For now, return the view
        return view('customer.payments.invoice-pdf', compact('booking'));
    }
}
