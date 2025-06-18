<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Car;
use Illuminate\Http\Request;

class CarController extends Controller
{
    public function index(Request $request)
    {
        $query = Car::where('status', 'available')->with('images');

        // Search by brand, model, or description
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('brand', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by brand
        if ($request->filled('brand')) {
            $query->where('brand', $request->brand);
        }

        // Filter by transmission
        if ($request->filled('transmission')) {
            $query->where('transmission', $request->transmission);
        }

        // Filter by fuel type
        if ($request->filled('fuel_type')) {
            $query->where('fuel_type', $request->fuel_type);
        }

        // Filter by seats
        if ($request->filled('seats')) {
            $query->where('seats', $request->seats);
        }

        // Filter by price range
        if ($request->filled('min_price')) {
            $query->where('price_per_day', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price_per_day', '<=', $request->max_price);
        }

        // Sort options
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        switch ($sortBy) {
            case 'price_low':
                $query->orderBy('price_per_day', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price_per_day', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'brand':
                $query->orderBy('brand', 'asc');
                break;
            default:
                $query->orderBy($sortBy, $sortOrder);
        }

        $cars = $query->paginate(12);

        // Get filter options for the sidebar
        $brands = Car::where('status', 'available')
            ->distinct()
            ->pluck('brand')
            ->sort();

        $transmissions = Car::where('status', 'available')
            ->distinct()
            ->pluck('transmission')
            ->sort();

        $fuelTypes = Car::where('status', 'available')
            ->distinct()
            ->pluck('fuel_type')
            ->sort();

        $seatOptions = Car::where('status', 'available')
            ->distinct()
            ->pluck('seats')
            ->sort();

        $priceRange = [
            'min' => Car::where('status', 'available')->min('price_per_day'),
            'max' => Car::where('status', 'available')->max('price_per_day'),
        ];

        return view('customer.cars.index', compact(
            'cars',
            'brands',
            'transmissions',
            'fuelTypes',
            'seatOptions',
            'priceRange'
        ));
    }

    public function show(Car $car)
    {
        if ($car->status !== 'available') {
            return redirect()->route('customer.cars.index')
                ->with('error', 'Mobil tidak tersedia untuk disewa.');
        }

        $car->load('images');

        // Get similar cars (same brand or similar price range)
        $similarCars = Car::where('status', 'available')
            ->where('id', '!=', $car->id)
            ->where(function ($query) use ($car) {
                $query->where('brand', $car->brand)
                    ->orWhereBetween('price_per_day', [
                        $car->price_per_day * 0.8,
                        $car->price_per_day * 1.2
                    ]);
            })
            ->with('images')
            ->take(4)
            ->get();

        return view('customer.cars.show', compact('car', 'similarCars'));
    }

    public function checkAvailability(Request $request, Car $car)
    {
        $request->validate([
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
        ]);

        $startDate = $request->start_date;
        $endDate = $request->end_date;

        // Check if car is available for the requested dates
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
            return response()->json([
                'available' => false,
                'message' => 'Mobil tidak tersedia pada tanggal yang dipilih.'
            ]);
        }

        // Calculate total days and price
        $totalDays = \Carbon\Carbon::parse($startDate)->diffInDays(\Carbon\Carbon::parse($endDate)) + 1;
        $totalPrice = $totalDays * $car->price_per_day;

        return response()->json([
            'available' => true,
            'total_days' => $totalDays,
            'price_per_day' => $car->price_per_day,
            'total_price' => $totalPrice,
            'message' => 'Mobil tersedia untuk tanggal yang dipilih.'
        ]);
    }
}
