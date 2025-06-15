<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserRequest extends FormRequest
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
        $userId = $this->route('user') ? $this->route('user')->id : auth()->id();
        $isCreating = $this->isMethod('POST');
        $isAdmin = auth()->user()->isAdmin();

        $rules = [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId)
            ],
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'identity_number' => [
                'required',
                'string',
                'max:20',
                Rule::unique('users', 'identity_number')->ignore($userId)
            ],
        ];

        // Password rules (only for creation or when updating password)
        if ($isCreating || $this->has('password')) {
            $rules['password'] = [
                $isCreating ? 'required' : 'nullable',
                'string',
                Password::min(8)->mixedCase()->numbers(),
                'confirmed'
            ];
        }

        // Role rules (only for admins)
        if ($isAdmin && $this->has('role')) {
            $rules['role'] = 'required|in:customer,admin';
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
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.string' => 'Format nama tidak valid.',
            'name.max' => 'Nama maksimal 255 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.string' => 'Format email tidak valid.',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email maksimal 255 karakter.',
            'email.unique' => 'Email sudah digunakan oleh pengguna lain.',
            'phone.required' => 'Nomor telepon wajib diisi.',
            'phone.string' => 'Format nomor telepon tidak valid.',
            'phone.max' => 'Nomor telepon maksimal 20 karakter.',
            'address.required' => 'Alamat wajib diisi.',
            'address.string' => 'Format alamat tidak valid.',
            'address.max' => 'Alamat maksimal 500 karakter.',
            'identity_number.required' => 'Nomor KTP/SIM wajib diisi.',
            'identity_number.string' => 'Format nomor identitas tidak valid.',
            'identity_number.max' => 'Nomor identitas maksimal 20 karakter.',
            'identity_number.unique' => 'Nomor identitas sudah digunakan oleh pengguna lain.',
            'password.required' => 'Password wajib diisi.',
            'password.string' => 'Format password tidak valid.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.mixed' => 'Password harus mengandung huruf besar dan kecil.',
            'password.numbers' => 'Password harus mengandung angka.',
            'password.confirmed' => 'Konfirmasi password tidak sesuai.',
            'role.required' => 'Role pengguna wajib dipilih.',
            'role.in' => 'Role pengguna harus customer atau admin.',
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
            'name' => 'nama lengkap',
            'email' => 'email',
            'phone' => 'nomor telepon',
            'address' => 'alamat',
            'identity_number' => 'nomor identitas',
            'password' => 'password',
            'role' => 'role',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Clean phone number
        if ($this->has('phone')) {
            $phone = preg_replace('/[^0-9+]/', '', $this->phone);
            $this->merge(['phone' => $phone]);
        }

        // Clean identity number
        if ($this->has('identity_number')) {
            $identityNumber = preg_replace('/[^0-9]/', '', $this->identity_number);
            $this->merge(['identity_number' => $identityNumber]);
        }

        // Ensure role is not changed by non-admin users
        if (!auth()->user()->isAdmin()) {
            $this->request->remove('role');
        }
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
            // Validate Indonesian phone number format
            if ($this->has('phone')) {
                $phone = $this->phone;
                if (!preg_match('/^(\+62|62|0)[0-9]{8,12}$/', $phone)) {
                    $validator->errors()->add('phone', 'Format nomor telepon tidak valid. Gunakan format Indonesia.');
                }
            }

            // Validate Indonesian ID number (KTP) format
            if ($this->has('identity_number')) {
                $identityNumber = $this->identity_number;
                if (strlen($identityNumber) !== 16 || !is_numeric($identityNumber)) {
                    $validator->errors()->add('identity_number', 'Nomor KTP harus 16 digit angka.');
                }
            }

            // Prevent admin from changing their own role to customer
            if ($this->has('role') && auth()->user()->isAdmin()) {
                $userId = $this->route('user') ? $this->route('user')->id : auth()->id();
                if ($userId == auth()->id() && $this->role === 'customer') {
                    $validator->errors()->add('role', 'Anda tidak dapat mengubah role Anda sendiri menjadi customer.');
                }
            }
        });
    }
}
