@extends('layouts.app')

@section('title', 'Lupa Password - Rental Oto')

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
</style>
@endpush

@section('content')
<div class="auth-container d-flex align-items-center justify-content-center p-4">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <!-- Logo Section -->
                <div class="text-center mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center bg-white rounded-circle shadow mb-3" style="width: 80px; height: 80px;">
                        <i class="fas fa-unlock-alt fa-2x text-primary"></i>
                    </div>
                    <h1 class="h2 text-white mb-2">Lupa Password?</h1>
                    <p class="text-white-50">Masukkan email Anda dan kami akan mengirimkan link reset password</p>
                </div>

                <!-- Form Card -->
                <div class="auth-card p-4 p-md-5">
                    <!-- Session Status -->
                    @if (session('status'))
                        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                            <i class="fas fa-check-circle me-2"></i> {{ session('status') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.email') }}" id="passwordResetForm">
                        @csrf

                        <!-- Email Address -->
                        <div class="mb-4">
                            <label for="email" class="form-label fw-medium">
                                <i class="fas fa-envelope fa-sm me-2"></i>
                                Alamat Email
                            </label>
                            <input 
                                id="email" 
                                name="email" 
                                type="email" 
                                autocomplete="email" 
                                required 
                                value="{{ old('email') }}"
                                class="form-control @error('email') is-invalid @enderror"
                                placeholder="masukkan email terdaftar Anda">
                                
                            @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" id="submitBtn" class="btn btn-gradient btn-primary w-100 py-2 mb-4">
                            <i class="fas fa-paper-plane me-2"></i> Kirim Link Reset Password
                        </button>

                        <!-- Back to Login -->
                        <div class="text-center">
                            <a href="{{ route('login') }}" class="text-decoration-none d-inline-flex align-items-center">
                                <i class="fas fa-chevron-left me-2"></i>
                                Kembali ke Login
                            </a>
                        </div>
                    </form>
                </div>
                
                <!-- Help Section -->
                <div class="mt-4 text-center">
                    <div class="bg-white bg-opacity-25 rounded p-3">
                        <h5 class="text-white mb-2">Butuh Bantuan?</h5>
                        <p class="text-white-50 small mb-2">
                            Jika Anda mengalami kesulitan, hubungi customer service kami
                        </p>
                        <div class="d-flex justify-content-center gap-3 small">
                            <a href="mailto:support@rentaloto.com" class="text-white">
                                <i class="fas fa-envelope me-1"></i> support@rentaloto.com
                            </a>
                            <span class="text-white-50">|</span>
                            <a href="tel:+628123456789" class="text-white">
                                <i class="fas fa-phone me-1"></i> 0812-3456-789
                            </a>
                        </div>
                    </div>
                    <p class="text-white-50 small mt-3">
                        <i class="fas fa-lock"></i> Link reset password berlaku selama 60 menit
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loadingOverlay" style="display: none; position: fixed; inset: 0; background-color: rgba(0,0,0,0.5); z-index: 9999; display: flex; align-items: center; justify-content: center;">
    <div class="bg-white p-4 rounded-3 d-flex align-items-center">
        <div class="spinner-border text-primary me-3" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <span>Mengirim email...</span>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Handle form submission and loading state
    document.getElementById('passwordResetForm').addEventListener('submit', function() {
        const loadingOverlay = document.getElementById('loadingOverlay');
        const submitBtn = document.getElementById('submitBtn');
        
        loadingOverlay.style.display = 'flex';
        submitBtn.disabled = true;
        submitBtn.innerHTML = `
            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
            Mengirim...
        `;
    });

    // Auto-hide success message after 5 seconds
    @if (session('status'))
        setTimeout(() => {
            const alert = document.querySelector('.alert-success');
            if (alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }
        }, 5000);
    @endif
    
    // Email validation
    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }
    
    // Real-time email validation
    document.getElementById('email').addEventListener('input', function() {
        const email = this.value;
        const submitBtn = document.getElementById('submitBtn');
        
        if (email && !validateEmail(email)) {
            this.classList.add('is-invalid');
            if (!this.nextElementSibling) {
                const feedback = document.createElement('div');
                feedback.classList.add('invalid-feedback');
                feedback.textContent = 'Mohon masukkan alamat email yang valid';
                this.after(feedback);
            }
            submitBtn.disabled = true;
        } else {
            this.classList.remove('is-invalid');
            submitBtn.disabled = false;
            if (this.nextElementSibling && this.nextElementSibling.classList.contains('invalid-feedback')) {
                this.nextElementSibling.remove();
            }
        }
    });
</script>
@endpush