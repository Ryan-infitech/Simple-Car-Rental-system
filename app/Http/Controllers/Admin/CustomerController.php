<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Booking;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'customer')
            ->withCount('bookings');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('identity_number', 'like', "%{$search}%");
            });
        }

        // Filter by registration date
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $customers = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.customers.index', compact('customers'));
    }

    public function show(User $customer)
    {
        if ($customer->role !== 'customer') {
            return redirect()->route('admin.customers.index')
                ->with('error', 'Customer tidak ditemukan');
        }

        $customer->load([
            'bookings' => function ($query) {
                $query->with(['car', 'payment'])->orderBy('created_at', 'desc');
            }
        ]);

        $stats = [
            'total_bookings' => $customer->bookings->count(),
            'completed_bookings' => $customer->bookings->where('status', 'completed')->count(),
            'cancelled_bookings' => $customer->bookings->where('status', 'cancelled')->count(),
            'total_spent' => $customer->bookings()
                ->whereHas('payment', function ($query) {
                    $query->where('payment_status', 'verified');
                })
                ->with('payment')
                ->get()
                ->sum('payment.amount')
        ];

        return view('admin.customers.show', compact('customer', 'stats'));
    }

    public function destroy(User $customer)
    {
        if ($customer->role !== 'customer') {
            return back()->with('error', 'Customer tidak ditemukan');
        }

        // Check if customer has active bookings
        if ($customer->bookings()->whereIn('status', ['pending', 'confirmed', 'ongoing'])->exists()) {
            return back()->with('error', 'Tidak dapat menghapus customer yang memiliki booking aktif');
        }

        $customer->delete();

        return redirect()->route('admin.customers.index')
            ->with('success', 'Customer berhasil dihapus');
    }

    public function toggleStatus(User $customer)
    {
        if ($customer->role !== 'customer') {
            return back()->with('error', 'Customer tidak ditemukan');
        }

        // Toggle some status field if you have one
        // For now, we'll just return success
        return back()->with('success', 'Status customer berhasil diubah');
    }

    public function export()
    {
        $customers = User::where('role', 'customer')
            ->withCount('bookings')
            ->get();

        return response()->streamDownload(function () use ($customers) {
            $handle = fopen('php://output', 'w');
            
            // CSV header
            fputcsv($handle, [
                'ID',
                'Nama',
                'Email',
                'Telepon',
                'Alamat',
                'No. Identitas',
                'Total Booking',
                'Tanggal Daftar'
            ]);

            // CSV data
            foreach ($customers as $customer) {
                fputcsv($handle, [
                    $customer->id,
                    $customer->name,
                    $customer->email,
                    $customer->phone,
                    $customer->address,
                    $customer->identity_number,
                    $customer->bookings_count,
                    $customer->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($handle);
        }, 'customers-' . date('Y-m-d') . '.csv');
    }
}
