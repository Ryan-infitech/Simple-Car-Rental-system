<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Models\Booking;

class NotificationService
{
    /**
     * Create a new notification
     *
     * @param int $userId
     * @param string $title
     * @param string $message
     * @param string $type
     * @return Notification
     */
    public function createNotification($userId, $title, $message, $type = 'general')
    {
        return Notification::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'is_read' => false
        ]);
    }

    /**
     * Create notifications for multiple users
     *
     * @param array $userIds
     * @param string $title
     * @param string $message
     * @param string $type
     * @return int Number of notifications created
     */
    public function createBulkNotifications($userIds, $title, $message, $type = 'general')
    {
        $notifications = [];
        $timestamp = now();

        foreach ($userIds as $userId) {
            $notifications[] = [
                'user_id' => $userId,
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'is_read' => false,
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ];
        }

        Notification::insert($notifications);
        return count($notifications);
    }

    /**
     * Notify all admins about payment upload
     *
     * @param Booking $booking
     * @return int Number of notifications sent
     */
    public function notifyPaymentUploaded(Booking $booking)
    {
        $admins = User::where('role', 'admin')->pluck('id')->toArray();
        
        return $this->createBulkNotifications(
            $admins,
            'Bukti Pembayaran Baru',
            "Bukti pembayaran untuk booking #{$booking->id} dari {$booking->user->name} telah diupload dan menunggu verifikasi.",
            'payment'
        );
    }

    /**
     * Notify customer about payment verification
     *
     * @param Booking $booking
     * @return Notification
     */
    public function notifyPaymentVerified(Booking $booking)
    {
        return $this->createNotification(
            $booking->user_id,
            'Pembayaran Diverifikasi',
            "Pembayaran untuk booking #{$booking->id} telah diverifikasi. Mobil {$booking->car->brand} {$booking->car->model} siap digunakan pada {$booking->start_date->format('d M Y')}!",
            'payment'
        );
    }

    /**
     * Notify customer about payment rejection
     *
     * @param Booking $booking
     * @param string $reason
     * @return Notification
     */
    public function notifyPaymentRejected(Booking $booking, $reason)
    {
        return $this->createNotification(
            $booking->user_id,
            'Pembayaran Ditolak',
            "Pembayaran untuk booking #{$booking->id} ditolak. Alasan: {$reason}. Silakan upload ulang bukti pembayaran yang benar.",
            'payment'
        );
    }

    /**
     * Notify admins about new booking
     *
     * @param Booking $booking
     * @return int Number of notifications sent
     */
    public function notifyNewBooking(Booking $booking)
    {
        $admins = User::where('role', 'admin')->pluck('id')->toArray();
        
        return $this->createBulkNotifications(
            $admins,
            'Booking Baru',
            "Booking baru #{$booking->id} untuk {$booking->car->brand} {$booking->car->model} telah dibuat oleh {$booking->user->name}.",
            'booking'
        );
    }

    /**
     * Notify customer about booking status change
     *
     * @param Booking $booking
     * @param string $oldStatus
     * @param string $newStatus
     * @return Notification
     */
    public function notifyBookingStatusChanged(Booking $booking, $oldStatus, $newStatus)
    {
        $statusMessages = [
            'pending' => 'menunggu konfirmasi',
            'confirmed' => 'dikonfirmasi',
            'ongoing' => 'sedang berlangsung',
            'completed' => 'selesai',
            'cancelled' => 'dibatalkan'
        ];

        $message = "Status booking #{$booking->id} telah diubah dari {$statusMessages[$oldStatus]} menjadi {$statusMessages[$newStatus]}.";
        
        if ($newStatus === 'confirmed') {
            $message .= ' Silakan lakukan pembayaran untuk melanjutkan proses rental.';
        } elseif ($newStatus === 'ongoing') {
            $message .= ' Selamat menikmati perjalanan Anda!';
        } elseif ($newStatus === 'completed') {
            $message .= ' Terima kasih telah menggunakan layanan kami.';
        }

        return $this->createNotification(
            $booking->user_id,
            'Status Booking Diperbarui',
            $message,
            'booking'
        );
    }

    /**
     * Notify customer about booking cancellation
     *
     * @param Booking $booking
     * @param string $reason
     * @return Notification
     */
    public function notifyBookingCancelled(Booking $booking, $reason = null)
    {
        $message = "Booking #{$booking->id} telah dibatalkan.";
        if ($reason) {
            $message .= " Alasan: {$reason}";
        }

        return $this->createNotification(
            $booking->user_id,
            'Booking Dibatalkan',
            $message,
            'booking'
        );
    }

    /**
     * Send welcome notification to new user
     *
     * @param User $user
     * @return Notification
     */
    public function sendWelcomeNotification(User $user)
    {
        return $this->createNotification(
            $user->id,
            'Selamat Datang!',
            "Terima kasih telah bergabung dengan sistem rental mobil kami, {$user->name}. Sekarang Anda dapat mulai menyewa mobil dengan mudah dan nyaman.",
            'general'
        );
    }

    /**
     * Notify admins about new user registration
     *
     * @param User $user
     * @return int Number of notifications sent
     */
    public function notifyNewUserRegistration(User $user)
    {
        $admins = User::where('role', 'admin')->pluck('id')->toArray();
        
        return $this->createBulkNotifications(
            $admins,
            'Customer Baru Terdaftar',
            "Customer baru dengan nama {$user->name} ({$user->email}) telah mendaftar di sistem.",
            'general'
        );
    }

    /**
     * Mark notification as read
     *
     * @param int $notificationId
     * @param int $userId
     * @return bool
     */
    public function markAsRead($notificationId, $userId)
    {
        return Notification::where('id', $notificationId)
            ->where('user_id', $userId)
            ->update(['is_read' => true]);
    }

    /**
     * Mark all notifications as read for a user
     *
     * @param int $userId
     * @return int Number of notifications marked as read
     */
    public function markAllAsRead($userId)
    {
        return Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }

    /**
     * Get unread notification count for a user
     *
     * @param int $userId
     * @return int
     */
    public function getUnreadCount($userId)
    {
        return Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->count();
    }

    /**
     * Get recent notifications for a user
     *
     * @param int $userId
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRecentNotifications($userId, $limit = 10)
    {
        return Notification::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get();
    }

    /**
     * Delete old notifications
     *
     * @param int $days
     * @return int Number of deleted notifications
     */
    public function deleteOldNotifications($days = 30)
    {
        return Notification::where('created_at', '<', now()->subDays($days))
            ->where('is_read', true)
            ->delete();
    }
}
