<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\CarImage;
use App\Http\Requests\CarRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class CarController extends Controller
{
    public function index()
    {
        $cars = Car::with('images')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.cars.index', compact('cars'));
    }

    public function create()
    {
        return view('admin.cars.create');
    }

    public function store(CarRequest $request)
    {
        $car = Car::create($request->validated());

        // Handle main image
        if ($request->hasFile('image')) {
            $imagePath = $this->uploadImage($request->file('image'));
            $car->update(['image_path' => $imagePath]);
        }

        // Handle multiple images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePath = $this->uploadImage($image);
                CarImage::create([
                    'car_id' => $car->id,
                    'image_path' => $imagePath,
                    'is_primary' => false
                ]);
            }
        }

        return redirect()->route('admin.cars.index')
            ->with('success', 'Mobil berhasil ditambahkan');
    }

    public function show(Car $car)
    {
        $car->load(['images', 'bookings.user']);
        return view('admin.cars.show', compact('car'));
    }

    public function edit(Car $car)
    {
        $car->load('images');
        return view('admin.cars.edit', compact('car'));
    }

    public function update(CarRequest $request, Car $car)
    {
        $car->update($request->validated());

        // Handle main image update
        if ($request->hasFile('image')) {
            // Delete old image
            if ($car->image_path) {
                Storage::disk('public')->delete($car->image_path);
            }

            $imagePath = $this->uploadImage($request->file('image'));
            $car->update(['image_path' => $imagePath]);
        }

        // Handle additional images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePath = $this->uploadImage($image);
                CarImage::create([
                    'car_id' => $car->id,
                    'image_path' => $imagePath,
                    'is_primary' => false
                ]);
            }
        }

        return redirect()->route('admin.cars.index')
            ->with('success', 'Mobil berhasil diperbarui');
    }

    public function destroy(Car $car)
    {
        // Check if car has active bookings
        if ($car->bookings()->whereIn('status', ['pending', 'confirmed', 'ongoing'])->exists()) {
            return back()->with('error', 'Tidak dapat menghapus mobil yang memiliki booking aktif');
        }

        // Delete images
        if ($car->image_path) {
            Storage::disk('public')->delete($car->image_path);
        }

        foreach ($car->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }

        $car->delete();

        return redirect()->route('admin.cars.index')
            ->with('success', 'Mobil berhasil dihapus');
    }

    public function deleteImage(Request $request, Car $car)
    {
        $imageId = $request->input('image_id');
        $image = CarImage::where('car_id', $car->id)->where('id', $imageId)->first();

        if ($image) {
            Storage::disk('public')->delete($image->image_path);
            $image->delete();
        }

        return back()->with('success', 'Gambar berhasil dihapus');
    }

    private function uploadImage($file)
    {
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $path = 'cars/' . $filename;

        // Resize and optimize image
        $image = Image::make($file)
            ->resize(800, 600, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })
            ->encode('jpg', 85);

        Storage::disk('public')->put($path, $image->stream());

        return $path;
    }
}
