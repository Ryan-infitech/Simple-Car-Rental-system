<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('customer');
    }

    public function edit()
    {
        $user = auth()->user();
        return view('customer.profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'identity_number' => 'required|string|max:20|unique:users,identity_number,' . $user->id,
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.string' => 'Format nama tidak valid.',
            'name.max' => 'Nama maksimal 255 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.string' => 'Format email tidak valid.',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email maksimal 255 karakter.',
            'email.unique' => 'Email sudah digunakan oleh user lain.',
            'phone.required' => 'Nomor telepon wajib diisi.',
            'phone.string' => 'Format nomor telepon tidak valid.',
            'phone.max' => 'Nomor telepon maksimal 20 karakter.',
            'address.required' => 'Alamat wajib diisi.',
            'address.string' => 'Format alamat tidak valid.',
            'address.max' => 'Alamat maksimal 500 karakter.',
            'identity_number.required' => 'Nomor KTP/SIM wajib diisi.',
            'identity_number.string' => 'Format nomor identitas tidak valid.',
            'identity_number.max' => 'Nomor identitas maksimal 20 karakter.',
            'identity_number.unique' => 'Nomor identitas sudah digunakan oleh user lain.',
        ]);

        $user->update($request->only([
            'name',
            'email',
            'phone',
            'address',
            'identity_number'
        ]));

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'current_password.current_password' => 'Password saat ini tidak sesuai.',
            'password.required' => 'Password baru wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak sesuai.',
            'password.min' => 'Password minimal 8 karakter.',
        ]);

        auth()->user()->update([
            'password' => Hash::make($request->password)
        ]);

        return back()->with('success', 'Password berhasil diperbarui.');
    }

    public function show()
    {
        $user = auth()->user();
        
        // Get user statistics
        $stats = [
            'total_bookings' => $user->bookings()->count(),
            'completed_bookings' => $user->bookings()->where('status', 'completed')->count(),
            'cancelled_bookings' => $user->bookings()->where('status', 'cancelled')->count(),
            'total_spent' => $user->bookings()
                ->whereHas('payment', function ($query) {
                    $query->where('payment_status', 'verified');
                })
                ->with('payment')
                ->get()
                ->sum('payment.amount'),
            'member_since' => $user->created_at->format('F Y'),
        ];

        // Get recent bookings
        $recentBookings = $user->bookings()
            ->with(['car', 'payment'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('customer.profile.show', compact('user', 'stats', 'recentBookings'));
    }

    public function deleteAccount(Request $request)
    {
        $request->validate([
            'password' => 'required|current_password',
        ], [
            'password.required' => 'Password wajib diisi untuk menghapus akun.',
            'password.current_password' => 'Password tidak sesuai.',
        ]);

        $user = auth()->user();

        // Check if user has active bookings
        if ($user->bookings()->whereIn('status', ['pending', 'confirmed', 'ongoing'])->exists()) {
            return back()->with('error', 'Tidak dapat menghapus akun karena masih memiliki booking aktif.');
        }

        // Logout and delete user
        auth()->logout();
        $user->delete();

        return redirect()->route('home')
            ->with('success', 'Akun berhasil dihapus.');
    }
}
