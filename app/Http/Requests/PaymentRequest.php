<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
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
        $rules = [
            'payment_method' => 'required|in:bca,mandiri,bni,bri',
        ];

        // Rules for payment proof upload
        if ($this->hasFile('payment_proof') || $this->has('payment_proof')) {
            $rules['payment_proof'] = 'required|image|mimes:jpeg,png,jpg|max:2048';
        }

        // Rules for admin payment verification/rejection
        if (auth()->user()->isAdmin()) {
            if ($this->has('notes')) {
                $rules['notes'] = 'nullable|string|max:500';
            }
        }

        return $rules;
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'payment_method.required' => 'Metode pembayaran wajib dipilih.',
            'payment_method.in' => 'Metode pembayaran yang dipilih tidak valid.',
            'payment_proof.required' => 'Bukti pembayaran wajib diupload.',
            'payment_proof.image' => 'File bukti pembayaran harus berupa gambar.',
            'payment_proof.mimes' => 'Format file harus jpeg, png, atau jpg.',
            'payment_proof.max' => 'Ukuran file maksimal 2MB.',
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
            'payment_method' => 'metode pembayaran',
            'payment_proof' => 'bukti pembayaran',
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
            // Additional validation for payment proof file
            if ($this->hasFile('payment_proof')) {
                $file = $this->file('payment_proof');
                
                // Check if file is readable
                if (!$file->isValid()) {
                    $validator->errors()->add('payment_proof', 'File bukti pembayaran tidak valid atau rusak.');
                }

                // Check file dimensions (optional)
                $imageInfo = getimagesize($file->path());
                if ($imageInfo) {
                    $width = $imageInfo[0];
                    $height = $imageInfo[1];
                    
                    // Minimum dimensions check
                    if ($width < 200 || $height < 200) {
                        $validator->errors()->add('payment_proof', 'Resolusi gambar minimal 200x200 pixel.');
                    }
                }
            }
        });
    }
}
