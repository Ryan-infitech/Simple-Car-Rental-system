<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Car;
use Carbon\Carbon;

class BookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'car_id' => 'required|exists:cars,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'pickup_location' => 'required|string|max:255',
            'return_location' => 'required|string|max:255',
            'notes' => 'nullable|string|max:500',
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
            'car_id.required' => 'Mobil wajib dipilih.',
            'car_id.exists' => 'Mobil yang dipilih tidak valid.',
            'start_date.required' => 'Tanggal mulai wajib diisi.',
            'start_date.date' => 'Format tanggal mulai tidak valid.',
            'start_date.after_or_equal' => 'Tanggal mulai tidak boleh sebelum hari ini.',
            'end_date.required' => 'Tanggal selesai wajib diisi.',
            'end_date.date' => 'Format tanggal selesai tidak valid.',
            'end_date.after' => 'Tanggal selesai harus setelah tanggal mulai.',
            'pickup_location.required' => 'Lokasi pengambilan wajib diisi.',
            'pickup_location.string' => 'Format lokasi pengambilan tidak valid.',
            'pickup_location.max' => 'Lokasi pengambilan maksimal 255 karakter.',
            'return_location.required' => 'Lokasi pengembalian wajib diisi.',
            'return_location.string' => 'Format lokasi pengembalian tidak valid.',
            'return_location.max' => 'Lokasi pengembalian maksimal 255 karakter.',
            'notes.string' => 'Format catatan tidak valid.',
            'notes.max' => 'Catatan maksimal 500 karakter.',
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
            'car_id' => 'mobil',
            'start_date' => 'tanggal mulai',
            'end_date' => 'tanggal selesai',
            'pickup_location' => 'lokasi pengambilan',
            'return_location' => 'lokasi pengembalian',
            'notes' => 'catatan',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Check if car is available
            if ($this->has('car_id')) {
                $car = Car::find($this->car_id);
                
                if ($car && $car->status !== 'available') {
                    $validator->errors()->add('car_id', 'Mobil yang dipilih sedang tidak tersedia.');
                }
            }

            // Check date availability
            if ($this->has(['car_id', 'start_date', 'end_date'])) {
                $this->validateDateAvailability($validator);
            }

            // Check minimum rental period (optional: minimum 1 day)
            if ($this->has(['start_date', 'end_date'])) {
                $startDate = Carbon::parse($this->start_date);
                $endDate = Carbon::parse($this->end_date);
                $days = $startDate->diffInDays($endDate) + 1;

                if ($days < 1) {
                    $validator->errors()->add('end_date', 'Minimal rental adalah 1 hari.');
                }

                // Check maximum rental period (optional: maximum 30 days)
                if ($days > 30) {
                    $validator->errors()->add('end_date', 'Maksimal rental adalah 30 hari.');
                }
            }
        });
    }

    /**
     * Validate date availability for the selected car.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    protected function validateDateAvailability($validator): void
    {
        $car = Car::find($this->car_id);
        $startDate = $this->start_date;
        $endDate = $this->end_date;
        $bookingId = $this->route('booking') ? $this->route('booking')->id : null;

        if ($car) {
            $conflictingBookings = $car->bookings()
                ->when($bookingId, function ($query) use ($bookingId) {
                    return $query->where('id', '!=', $bookingId);
                })
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
                $validator->errors()->add('start_date', 'Mobil tidak tersedia pada tanggal yang dipilih.');
                $validator->errors()->add('end_date', 'Mobil tidak tersedia pada tanggal yang dipilih.');
            }
        }
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Format dates if needed
        if ($this->has('start_date')) {
            $this->merge([
                'start_date' => Carbon::parse($this->start_date)->format('Y-m-d'),
            ]);
        }

        if ($this->has('end_date')) {
            $this->merge([
                'end_date' => Carbon::parse($this->end_date)->format('Y-m-d'),
            ]);
        }
    }
}
