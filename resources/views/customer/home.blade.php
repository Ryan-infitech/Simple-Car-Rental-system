@extends('layouts.customer')

@section('content')
<div class="hero-section position-relative">
    <div class="hero-image">
        <img src="{{ asset('images/hero-car-rental.jpg') }}" alt="Car Rental Hero" class="w-100">
        <div class="overlay"></div>
    </div>
    <div class="container hero-content">
        <div class="row">
            <div class="col-lg-8 col-md-10">
                <h1 class="text-white display-4 fw-bold">Find Your Perfect Ride</h1>
                <p class="text-white lead">Choose from our wide selection of quality vehicles for any occasion.</p>
                <a href="{{ route('customer.cars.index') }}" class="btn btn-primary btn-lg mt-3">Browse Cars</a>
            </div>
        </div>
    </div>
</div>

<div class="container py-5">
    <!-- Welcome Section -->
    <div class="row mb-5">
        <div class="col-md-12 text-center">
            <h2>Welcome Back, {{ auth()->user()->name }}</h2>
            <p class="lead text-muted">Your trusted car rental service</p>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 col-sm-6 mb-3 mb-md-0">
                            <a href="{{ route('customer.cars.index') }}" class="text-decoration-none">
                                <div class="card h-100 quick-action-card">
                                    <div class="card-body text-center">
                                        <i class="fas fa-car fa-3x text-primary mb-3"></i>
                                        <h5>Browse Cars</h5>
                                        <p class="small text-muted">Explore our fleet</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3 mb-md-0">
                            <a href="{{ route('customer.bookings.index') }}" class="text-decoration-none">
                                <div class="card h-100 quick-action-card">
                                    <div class="card-body text-center">
                                        <i class="fas fa-calendar-check fa-3x text-success mb-3"></i>
                                        <h5>My Bookings</h5>
                                        <p class="small text-muted">Manage your reservations</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3 mb-md-0">
                            <a href="{{ route('customer.bookings.create') }}" class="text-decoration-none">
                                <div class="card h-100 quick-action-card">
                                    <div class="card-body text-center">
                                        <i class="fas fa-plus-circle fa-3x text-warning mb-3"></i>
                                        <h5>New Booking</h5>
                                        <p class="small text-muted">Rent a car now</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <a href="{{ route('customer.profile.edit') }}" class="text-decoration-none">
                                <div class="card h-100 quick-action-card">
                                    <div class="card-body text-center">
                                        <i class="fas fa-user-edit fa-3x text-info mb-3"></i>
                                        <h5>My Profile</h5>
                                        <p class="small text-muted">Update your information</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Bookings -->
    <div class="row mb-5">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Active Bookings</h5>
                    <a href="{{ route('customer.bookings.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    @if($activeBookings->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Car</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($activeBookings as $booking)
                                        <tr>
                                            <td>#{{ $booking->id }}</td>
                                            <td>{{ $booking->car->brand }} {{ $booking->car->model }}</td>
                                            <td>{{ $booking->start_date->format('M d, Y') }}</td>
                                            <td>{{ $booking->end_date->format('M d, Y') }}</td>
                                            <td>
                                                @if($booking->status == 'pending')
                                                    <span class="badge bg-warning text-dark">Pending</span>
                                                @elseif($booking->status == 'confirmed')
                                                    <span class="badge bg-success">Confirmed</span>
                                                @elseif($booking->status == 'ongoing')
                                                    <span class="badge bg-primary">Ongoing</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('customer.bookings.show', $booking->id) }}" class="btn btn-sm btn-info">View</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-calendar-day fa-3x text-muted mb-3"></i>
                            <p class="mb-0">You don't have any active bookings</p>
                            <a href="{{ route('customer.cars.index') }}" class="btn btn-primary mt-3">Browse Cars</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Featured Cars -->
    <div class="row">
        <div class="col-12 mb-4">
            <h3>Featured Cars</h3>
            <p class="text-muted">Explore our most popular vehicles</p>
        </div>
        
        @foreach($featuredCars as $car)
            <div class="col-lg-4 col-md-6 mb-4">
                @include('components.car-card', ['car' => $car])
            </div>
        @endforeach
    </div>
</div>

<div class="container-fluid bg-light py-5">
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-center mb-4">
                <h2>Why Choose Us?</h2>
                <p class="lead text-muted">Experience the best car rental service</p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 mb-4 mb-md-0">
                <div class="text-center">
                    <i class="fas fa-money-bill-wave fa-4x text-primary mb-3"></i>
                    <h4>Best Price Guarantee</h4>
                    <p class="text-muted">We offer the best rates and flexible rental options</p>
                </div>
            </div>
            <div class="col-md-4 mb-4 mb-md-0">
                <div class="text-center">
                    <i class="fas fa-car fa-4x text-primary mb-3"></i>
                    <h4>Quality Vehicles</h4>
                    <p class="text-muted">All our cars are well-maintained and regularly serviced</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-center">
                    <i class="fas fa-headset fa-4x text-primary mb-3"></i>
                    <h4>24/7 Customer Support</h4>
                    <p class="text-muted">Our team is always ready to assist you anytime</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .hero-section {
        height: 500px;
        overflow: hidden;
        margin-bottom: 40px;
    }
    
    .hero-image {
        height: 100%;
        position: relative;
    }
    
    .hero-image img {
        object-fit: cover;
        height: 100%;
    }
    
    .overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
    }
    
    .hero-content {
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        transform: translateY(-50%);
        z-index: 10;
    }
    
    .quick-action-card:hover {
        transform: translateY(-5px);
        transition: all 0.3s;
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15);
    }
    
    .car-img {
        height: 200px;
        object-fit: cover;
    }
    
    .car-card {
        transition: all 0.3s;
    }
    
    .car-card:hover {
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15);
        transform: translateY(-5px);
    }
</style>
@endsection
