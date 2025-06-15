<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Booking;
use App\Models\User;
use Carbon\Carbon;

class PaymentService
{
    /**
     * Get bank account information for all supported payment methods
     *
     * @return array
     */
    public function getBankAccounts()
    {
        return [
            'bca' => [
                'name' => 'Bank BCA',
                'account_number' => '1234567890',
                'account_name' => 'CV Rental Mobil Sejahtera',
                'code' => 'BCA',
                'logo' => 'images/banks/bca.png'
            ],
            'mandiri' => [
                'name' => 'Bank Mandiri',
                'account_number' => '0987654321',
                'account_name' => 'CV Rental Mobil Sejahtera',
                'code' => 'MANDIRI',
                'logo' => 'images/banks/mandiri.png'
            ],
            'bni' => [
                'name' => 'Bank BNI',
                'account_number' => '1122334455',
                'account_name' => 'CV Rental Mobil Sejahtera',
                'code' => 'BNI',
                'logo' => 'images/banks/bni.png'
            ],
            'bri' => [
                'name' => 'Bank BRI',
                'account_number' => '5566778899',
                'account_name' => 'CV Rental Mobil Sejahtera',
                'code' => 'BRI',
                'logo' => 'images/banks/bri.png'
            ]
        ];
    }

    /**
     * Get payment instructions for a specific method
     *
     * @param string $method
     * @return array|null
     */
    public function getPaymentInstructions($method)
    {
        $accounts = $this->getBankAccounts();
        return $accounts[$method] ?? null;
    }

    /**
     * Create a new payment record
     *
     * @param Booking $booking
     * @param string $paymentMethod
     * @return Payment
     */
    public function createPayment(Booking $booking, $paymentMethod)
    {
        return Payment::create([
            'booking_id' => $booking->id,
            'amount' => $booking->total_price,
            'payment_method' => $paymentMethod,
            'payment_date' => now(),
            'payment_status' => 'pending'
        ]);
    }

    /**
     * Process payment proof upload
     *
     * @param Payment $payment
     * @param string $proofPath
     * @return bool
     */
    public function uploadPaymentProof(Payment $payment, $proofPath)
    {
        return $payment->update([
            'payment_proof' => $proofPath,
            'payment_status' => 'pending'
        ]);
    }

    /**
     * Verify payment by admin
     *
     * @param Payment $payment
     * @param int $adminId
     * @return bool
     */
    public function verifyPayment(Payment $payment, $adminId)
    {
        $verified = $payment->update([
            'payment_status' => 'verified',
            'verified_at' => now(),
            'verified_by' => $adminId
        ]);

        if ($verified) {
            // Update booking status
            $payment->booking->update(['status' => 'confirmed']);
        }

        return $verified;
    }

    /**
     * Reject payment by admin
     *
     * @param Payment $payment
     * @param string $reason
     * @return bool
     */
    public function rejectPayment(Payment $payment, $reason)
    {
        return $payment->update([
            'payment_status' => 'rejected',
            'notes' => $reason
        ]);
    }

    /**
     * Get payment statistics
     *
     * @param array $filters
     * @return array
     */
    public function getPaymentStatistics($filters = [])
    {
        $query = Payment::query();

        // Apply date filters
        if (isset($filters['start_date'])) {
            $query->whereDate('payment_date', '>=', $filters['start_date']);
        }

        if (isset($filters['end_date'])) {
            $query->whereDate('payment_date', '<=', $filters['end_date']);
        }

        return [
            'total_payments' => $query->count(),
            'pending_payments' => $query->where('payment_status', 'pending')->count(),
            'verified_payments' => $query->where('payment_status', 'verified')->count(),
            'rejected_payments' => $query->where('payment_status', 'rejected')->count(),
            'total_amount' => $query->where('payment_status', 'verified')->sum('amount'),
            'pending_amount' => $query->where('payment_status', 'pending')->sum('amount'),
        ];
    }

    /**
     * Generate payment reference number
     *
     * @param Booking $booking
     * @return string
     */
    public function generatePaymentReference(Booking $booking)
    {
        return 'PAY-' . $booking->id . '-' . Carbon::now()->format('YmdHis');
    }

    /**
     * Check if payment is overdue
     *
     * @param Payment $payment
     * @param int $hours
     * @return bool
     */
    public function isPaymentOverdue(Payment $payment, $hours = 24)
    {
        if ($payment->payment_status !== 'pending') {
            return false;
        }

        return $payment->created_at->addHours($hours)->isPast();
    }

    /**
     * Get payment methods with their display names
     *
     * @return array
     */
    public function getPaymentMethods()
    {
        return [
            'bca' => 'Bank BCA',
            'mandiri' => 'Bank Mandiri',
            'bni' => 'Bank BNI',
            'bri' => 'Bank BRI'
        ];
    }
}
