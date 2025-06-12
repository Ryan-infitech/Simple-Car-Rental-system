@extends('layouts.app')

@section('title', 'Login - Rental Oto')

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
    
    .car-icon {
        animation: float 6s ease-in-out infinite;
    }
    
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
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
</style>
@endpush

@section('content')
<div class="auth-container d-flex align-items-center justify-content-center p-4">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-8">
                <!-- Logo Section -->
                <div class="text-center mb-4">
                    <div class="car-icon d-inline-flex align-items-center justify-content-center bg-white rounded-circle shadow mb-3" style="width: 80px; height: 80px;">
                    <img src="{{ asset('images/icon.png') }}" alt="Car Rental Logo" height="40">
                    </div>
                    <h1 class="h2 text-white mb-2">Rental Oto</h1>
                    <p class="text-white-50">Masuk ke akun Anda</p>
                </div>

                <!-- Login Form -->
                <div class="auth-card p-4 p-md-5">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        
                        <!-- Email Field -->
                        <div class="mb-4">
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
                                   autofocus
                                   placeholder="masukkan email Anda">
                            
                            @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Password Field -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between">
                                <label for="password" class="form-label fw-medium">
                                    <i class="fas fa-lock fa-sm me-2"></i>
                                    Password
                                </label>
                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" class="text-decoration-none small">
                                        Lupa password?
                                    </a>
                                @endif
                            </div>
                            <div class="input-group">
                                <input id="password" 
                                       type="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       name="password" 
                                       required 
                                       autocomplete="current-password"
                                       placeholder="masukkan password Anda">
                                <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                                    <i class="fas fa-eye" id="toggleIcon"></i>
                                </button>
                                
                                @error('password')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <!-- Remember Me -->
                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label" for="remember">
                                    Ingat saya
                                </label>
                            </div>
                        </div>

                        <!-- Login Button -->
                        <button type="submit" class="btn btn-gradient btn-primary w-100 py-2 mb-4">
                            <i class="fas fa-sign-in-alt me-2"></i> Masuk
                        </button>

                        <!-- Register Link -->
                        <div class="text-center">
                            <p class="mb-0">
                                Belum punya akun? 
                                <a href="{{ route('register') }}" class="text-primary fw-medium text-decoration-none">
                                    Daftar sekarang
                                </a>
                            </p>
                        </div>

                        <!-- Demo Accounts -->
                        <div class="mt-4 p-3 bg-light rounded-3">
                            <h6 class="text-muted text-center mb-2">Demo Akun:</h6>
                            <div class="small text-center">
                                <div><strong>Admin:</strong> admin@rental.com / admin123</div>
                                <div><strong>Customer:</strong> customer@rental.com / customer123</div>
                            </div>
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

    // Auto-focus on email field if empty
    window.addEventListener('DOMContentLoaded', function() {
        const emailField = document.getElementById('email');
        if (emailField && !emailField.value) {
            emailField.focus();
        }
    });
</script>
@endpush