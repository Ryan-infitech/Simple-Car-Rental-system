@extends('layouts.app')

@section('title', 'Daftar - Rental Oto')

@push('styles')
<style>
    .auth-container {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
    }
    
    .auth-card {
        background-color: rgba(255, 255, 255, 0.95);
        border-radius: 1rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    
    .form-control:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
    }
    
    .btn-gradient {
        background: linear-gradient(to right, #4e73df, #6f42c1);
        border: none;
        transition: all 0.3s;
    }
    
    .btn-gradient:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    
    .strength-meter {
        height: 4px;
        border-radius: 2px;
        transition: all 0.3s ease;
    }
    
    .strength-weak { background-color: #ef4444; width: 33%; }
    .strength-medium { background-color: #f59e0b; width: 66%; }
    .strength-strong { background-color: #10b981; width: 100%; }
</style>
@endpush

@section('content')
<div class="auth-container d-flex align-items-center justify-content-center p-4">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-8 col-lg-10">
                <!-- Logo Section -->
                <div class="text-center mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center bg-white rounded-circle shadow mb-3" style="width: 80px; height: 80px;">
                    <img src="{{ asset('images/icon.png') }}" alt="Car Rental Logo" height="40">
                    </div>
                    <h1 class="h2 text-white mb-2">Daftar Akun Baru</h1>
                    <p class="text-white-50">Bergabung dengan Rental Oto</p>
                </div>

                <!-- Registration Form -->
                <div class="auth-card p-4 p-md-5">
                    <form method="POST" action="{{ route('register') }}" id="registerForm">
                        @csrf
                        
                        <div class="row">
                            <!-- Name Field -->
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label fw-medium">
                                    <i class="fas fa-user fa-sm me-2"></i>
                                    Nama Lengkap
                                </label>
                                <input id="name" 
                                       type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       name="name" 
                                       value="{{ old('name') }}" 
                                       required 
                                       autocomplete="name" 
                                       autofocus
                                       placeholder="Masukkan nama lengkap">
                                
                                @error('name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Email Field -->
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label fw-medium">
                                    <i class="fas fa-envelope fa-sm me-2"></i>
                                    Alamat Email
                                </label>
                                <input id="email" 
                                       type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       name="email" 
                                       value="{{ old('email') }}" 
                                       required 
                                       autocomplete="email"
                                       placeholder="nama@email.com">
                                
                                @error('email')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Phone Field -->
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label fw-medium">
                                    <i class="fas fa-phone fa-sm me-2"></i>
                                    Nomor Telepon
                                </label>
                                <input id="phone" 
                                       type="tel" 
                                       class="form-control @error('phone') is-invalid @enderror" 
                                       name="phone" 
                                       value="{{ old('phone') }}"
                                       required
                                       placeholder="08xxxxxxxxxx">
                                
                                @error('phone')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Identity Number Field -->
                            <div class="col-md-6 mb-3">
                                <label for="identity_number" class="form-label fw-medium">
                                    <i class="fas fa-id-card fa-sm me-2"></i>
                                    Nomor KTP/SIM
                                </label>
                                <input id="identity_number" 
                                       type="text" 
                                       class="form-control @error('identity_number') is-invalid @enderror" 
                                       name="identity_number" 
                                       value="{{ old('identity_number') }}"
                                       required
                                       placeholder="Nomor KTP atau SIM"
                                       maxlength="16">
                                
                                @error('identity_number')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <!-- Address Field -->
                        <div class="mb-3">
                            <label for="address" class="form-label fw-medium">
                                <i class="fas fa-map-marker-alt fa-sm me-2"></i>
                                Alamat
                            </label>
                            <textarea id="address" 
                                      class="form-control @error('address') is-invalid @enderror" 
                                      name="address" 
                                      rows="3"
                                      required
                                      placeholder="Masukkan alamat lengkap">{{ old('address') }}</textarea>
                            
                            @error('address')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="row">
                            <!-- Password Field -->
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label fw-medium">
                                    <i class="fas fa-lock fa-sm me-2"></i>
                                    Password
                                </label>
                                <div class="input-group">
                                    <input id="password" 
                                           type="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           name="password" 
                                           required 
                                           autocomplete="new-password"
                                           placeholder="Minimal 8 karakter">
                                    <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                                        <i class="fas fa-eye" id="toggleIcon"></i>
                                    </button>
                                    
                                    @error('password')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <!-- Password Strength Indicator -->
                                <div class="mt-2">
                                    <div class="bg-secondary bg-opacity-25 rounded-pill" style="height: 4px;">
                                        <div id="strengthBar" class="strength-meter rounded-pill"></div>
                                    </div>
                                    <p id="passwordStrengthText" class="form-text mt-1"></p>
                                </div>
                            </div>

                            <!-- Confirm Password Field -->
                            <div class="col-md-6 mb-3">
                                <label for="password-confirm" class="form-label fw-medium">
                                    <i class="fas fa-check-circle fa-sm me-2"></i>
                                    Konfirmasi Password
                                </label>
                                <div class="input-group">
                                    <input id="password-confirm" 
                                           type="password" 
                                           class="form-control" 
                                           name="password_confirmation" 
                                           required 
                                           autocomplete="new-password"
                                           placeholder="Ulangi password">
                                    <button type="button" class="btn btn-outline-secondary" id="toggleConfirmPassword">
                                        <i class="fas fa-eye" id="toggleConfirmIcon"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Terms and Conditions -->
                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input @error('terms') is-invalid @enderror" 
                                       type="checkbox" 
                                       name="terms" 
                                       id="terms" 
                                       required>
                                <label class="form-check-label" for="terms">
                                    Saya menyetujui 
                                    <a href="#" class="text-primary">Syarat dan Ketentuan</a> 
                                    serta 
                                    <a href="#" class="text-primary">Kebijakan Privasi</a>
                                </label>
                                @error('terms')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <!-- Register Button -->
                        <button type="submit" class="btn btn-gradient btn-primary w-100 py-2 mb-3">
                            <i class="fas fa-user-plus me-2"></i> Daftar Sekarang
                        </button>

                        <!-- Login Link -->
                        <div class="text-center">
                            <p class="mb-0">
                                Sudah punya akun? 
                                <a href="{{ route('login') }}" class="text-primary fw-medium text-decoration-none">
                                    Login disini
                                </a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Toggle password visibility
    document.getElementById('togglePassword').addEventListener('click', function() {
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('toggleIcon');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        }
    });
    
    document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
        const confirmPasswordInput = document.getElementById('password-confirm');
        const toggleConfirmIcon = document.getElementById('toggleConfirmIcon');
        
        if (confirmPasswordInput.type === 'password') {
            confirmPasswordInput.type = 'text';
            toggleConfirmIcon.classList.remove('fa-eye');
            toggleConfirmIcon.classList.add('fa-eye-slash');
        } else {
            confirmPasswordInput.type = 'password';
            toggleConfirmIcon.classList.remove('fa-eye-slash');
            toggleConfirmIcon.classList.add('fa-eye');
        }
    });

    // Check password strength
    document.getElementById('password').addEventListener('input', function(e) {
        const password = e.target.value;
        const strengthBar = document.getElementById('strengthBar');
        const strengthText = document.getElementById('passwordStrengthText');
        
        let strength = 0;
        
        // Check length
        if (password.length >= 8) strength++;
        
        // Check lowercase letters
        if (/[a-z]/.test(password)) strength++;
        
        // Check uppercase letters
        if (/[A-Z]/.test(password)) strength++;
        
        // Check numbers
        if (/\d/.test(password)) strength++;
        
        // Check special characters
        if (/[^A-Za-z0-9]/.test(password)) strength++;
        
        // Reset classes
        strengthBar.className = 'strength-meter rounded-pill';
        
        // Apply appropriate styling based on strength
        if (password.length === 0) {
            strengthBar.style.width = '0%';
            strengthText.textContent = '';
        } else if (strength < 3) {
            strengthBar.classList.add('strength-weak');
            strengthText.textContent = 'Password lemah';
            strengthText.className = 'form-text text-danger mt-1';
        } else if (strength < 5) {
            strengthBar.classList.add('strength-medium');
            strengthText.textContent = 'Password sedang';
            strengthText.className = 'form-text text-warning mt-1';
        } else {
            strengthBar.classList.add('strength-strong');
            strengthText.textContent = 'Password kuat';
            strengthText.className = 'form-text text-success mt-1';
        }
    });

    // Format phone number input
    document.getElementById('phone').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 15) value = value.slice(0, 15);
        e.target.value = value;
    });

    // Format identity number input
    document.getElementById('identity_number').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 16) value = value.slice(0, 16);
        e.target.value = value;
    });
</script>
@endpush