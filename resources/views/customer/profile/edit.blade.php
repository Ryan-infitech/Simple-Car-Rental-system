@extends('layouts.customer')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>My Profile</h1>
            <p class="lead text-muted">Manage your personal information</p>
        </div>
        <div class="col-md-4">
            <div class="d-flex justify-content-md-end">
                <a href="{{ route('customer.home') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row">
        <div class="col-lg-4 mb-4">
            <!-- Profile Summary Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-body text-center">
                    <div class="profile-pic-wrapper mb-3">
                        @if(auth()->user()->avatar)
                            <img src="{{ asset('storage/'.auth()->user()->avatar) }}" alt="Profile Picture" class="rounded-circle img-thumbnail profile-pic">
                        @else
                            <div class="profile-initial rounded-circle">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    <h5>{{ auth()->user()->name }}</h5>
                    <p class="text-muted">{{ auth()->user()->email }}</p>
                    <p class="small text-muted">Member since {{ auth()->user()->created_at->format('M Y') }}</p>
                </div>
                <div class="card-footer bg-white">
                    <div class="d-grid">
                        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#uploadAvatarModal">
                            <i class="fas fa-camera me-2"></i> Change Profile Picture
                        </button>
                    </div>
                </div>
            </div>

            <!-- Activity Summary -->
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Account Summary</h5>
                </div>
                <div class="card-body">
                    <div class="activity-stat d-flex justify-content-between mb-3">
                        <span>Total Bookings</span>
                        <span class="badge bg-primary">{{ $totalBookings ?? 0 }}</span>
                    </div>
                    <div class="activity-stat d-flex justify-content-between mb-3">
                        <span>Active Bookings</span>
                        <span class="badge bg-success">{{ $activeBookings ?? 0 }}</span>
                    </div>
                    <div class="activity-stat d-flex justify-content-between">
                        <span>Completed Trips</span>
                        <span class="badge bg-info">{{ $completedBookings ?? 0 }}</span>
                    </div>
                    <hr>
                    <div class="d-grid">
                        <a href="{{ route('customer.bookings.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-history me-2"></i> View Booking History
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-8">
            <!-- Personal Information Form -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Personal Information</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('customer.profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', auth()->user()->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', auth()->user()->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', auth()->user()->phone) }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="identity_number" class="form-label">Identity Number (KTP/SIM)</label>
                                <input type="text" class="form-control @error('identity_number') is-invalid @enderror" id="identity_number" name="identity_number" value="{{ old('identity_number', auth()->user()->identity_number) }}">
                                @error('identity_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3">{{ old('address', auth()->user()->address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Change Password Form -->
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Change Password</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('customer.profile.password') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current Password</label>
                            <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" required>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">New Password</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-lock me-2"></i> Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Upload Avatar Modal -->
<div class="modal fade" id="uploadAvatarModal" tabindex="-1" aria-labelledby="uploadAvatarModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadAvatarModalLabel">Upload Profile Picture</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('customer.profile.avatar') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="avatar" class="form-label">Select Image</label>
                        <input class="form-control" type="file" id="avatar" name="avatar" accept="image/*" required>
                        <div class="form-text">Maximum file size: 2MB. Supported formats: JPG, PNG</div>
                    </div>
                    
                    <div class="text-center mt-3 d-none" id="preview-container">
                        <img id="avatar-preview" src="#" alt="Preview" class="img-thumbnail rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .profile-pic-wrapper {
        width: 100px;
        height: 100px;
        margin: 0 auto;
    }
    
    .profile-pic {
        width: 100px;
        height: 100px;
        object-fit: cover;
    }
    
    .profile-initial {
        width: 100px;
        height: 100px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #6c757d;
        color: white;
        font-size: 48px;
        font-weight: bold;
        margin: 0 auto;
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const avatarInput = document.getElementById('avatar');
        const previewContainer = document.getElementById('preview-container');
        const avatarPreview = document.getElementById('avatar-preview');
        
        avatarInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    avatarPreview.setAttribute('src', e.target.result);
                    previewContainer.classList.remove('d-none');
                }
                
                reader.readAsDataURL(this.files[0]);
            }
        });
    });
</script>
@endsection
