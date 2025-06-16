<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CarRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $carId = $this->route('car') ? $this->route('car')->id : null;

        return [
            'brand' => 'required|string|max:100',
            'model' => 'required|string|max:100',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'license_plate' => [
                'required',
                'string',
                'max:20',
                Rule::unique('cars', 'license_plate')->ignore($carId)
            ],
            'color' => 'required|string|max:50',
            'transmission' => 'required|in:manual,automatic',
            'fuel_type' => 'required|string|max:50',
            'seats' => 'required|integer|min:2|max:50',
            'price_per_day' => 'required|numeric|min:0|max:999999999.99',
            'status' => 'required|in:available,rented,maintenance',
            'description' => 'nullable|string|max:1000',
            'image' => $this->isMethod('POST') ? 'nullable|image|mimes:jpeg,png,jpg|max:2048' : 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:2048',
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'brand.required' => 'Merek mobil wajib diisi.',
            'brand.string' => 'Format merek mobil tidak valid.',
            'brand.max' => 'Merek mobil maksimal 100 karakter.',
            'model.required' => 'Model mobil wajib diisi.',
            'model.string' => 'Format model mobil tidak valid.',
            'model.max' => 'Model mobil maksimal 100 karakter.',
            'year.required' => 'Tahun produksi wajib diisi.',
            'year.integer' => 'Tahun produksi harus berupa angka.',
            'year.min' => 'Tahun produksi minimal 1900.',
            'year.max' => 'Tahun produksi maksimal ' . (date('Y') + 1) . '.',
            'license_plate.required' => 'Nomor plat wajib diisi.',
            'license_plate.string' => 'Format nomor plat tidak valid.',
            'license_plate.max' => 'Nomor plat maksimal 20 karakter.',
            'license_plate.unique' => 'Nomor plat sudah terdaftar.',
            'color.required' => 'Warna mobil wajib diisi.',
            'color.string' => 'Format warna mobil tidak valid.',
            'color.max' => 'Warna mobil maksimal 50 karakter.',
            'transmission.required' => 'Jenis transmisi wajib dipilih.',
            'transmission.in' => 'Jenis transmisi harus manual atau automatic.',
            'fuel_type.required' => 'Jenis bahan bakar wajib diisi.',
            'fuel_type.string' => 'Format jenis bahan bakar tidak valid.',
            'fuel_type.max' => 'Jenis bahan bakar maksimal 50 karakter.',
            'seats.required' => 'Jumlah kursi wajib diisi.',
            'seats.integer' => 'Jumlah kursi harus berupa angka.',
            'seats.min' => 'Jumlah kursi minimal 2.',
            'seats.max' => 'Jumlah kursi maksimal 50.',
            'price_per_day.required' => 'Harga per hari wajib diisi.',
            'price_per_day.numeric' => 'Harga per hari harus berupa angka.',
            'price_per_day.min' => 'Harga per hari minimal 0.',
            'price_per_day.max' => 'Harga per hari terlalu besar.',
            'status.required' => 'Status mobil wajib dipilih.',
            'status.in' => 'Status mobil harus available, rented, atau maintenance.',
            'description.string' => 'Format deskripsi tidak valid.',
            'description.max' => 'Deskripsi maksimal 1000 karakter.',
            'image.image' => 'File harus berupa gambar.',
            'image.mimes' => 'Format gambar harus jpeg, png, atau jpg.',
            'image.max' => 'Ukuran gambar maksimal 2MB.',
            'images.array' => 'Format gambar tambahan tidak valid.',
            'images.max' => 'Maksimal 5 gambar tambahan.',
            'images.*.image' => 'Setiap file harus berupa gambar.',
            'images.*.mimes' => 'Format gambar harus jpeg, png, atau jpg.',
            'images.*.max' => 'Ukuran setiap gambar maksimal 2MB.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'brand' => 'merek',
            'model' => 'model',
            'year' => 'tahun',
            'license_plate' => 'nomor plat',
            'color' => 'warna',
            'transmission' => 'transmisi',
            'fuel_type' => 'bahan bakar',
            'seats' => 'jumlah kursi',
            'price_per_day' => 'harga per hari',
            'status' => 'status',
            'description' => 'deskripsi',
            'image' => 'gambar utama',
            'images' => 'gambar tambahan',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('price_per_day')) {
            $this->merge([
                'price_per_day' => str_replace(['Rp', '.', ',', ' '], '', $this->price_per_day),
            ]);
        }
    }
}
