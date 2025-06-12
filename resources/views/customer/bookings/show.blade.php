@extends('layouts.customer')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('customer.home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('customer.bookings.index') }}">My Bookings</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Booking #{{ $booking->id }}</li>
                </ol>
            </nav>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('customer.bookings.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> Back to Bookings
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row">
        <div class="col-md-7">
            <!-- Booking Details -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Booking Details</h5>
                        <span class="badge 
                            @if($booking->status == 'pending') bg-warning text-dark
                            @elseif($booking->status == 'confirmed') bg-success
                            @elseif($booking->status == 'ongoing') bg-primary
                            @elseif($booking->status == 'completed') bg-info
                            @elseif($booking->status == 'cancelled') bg-danger
                            @endif">
                            {{ ucfirst($booking->status) }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1 text-muted">Booking ID</p>
                                <h6>#{{ $booking->id }}</h6>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1 text-muted">Booking Date</p>
                                <h6>{{ $booking->created_at->format('M d, Y H:i') }}</h6>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="text-muted">Car Details</h6>
                        <div class="row booking-car-details align-items-center">
                            <div class="col-md-4">
                                <img src="{{ asset($booking->car->image_path) }}" alt="{{ $booking->car->brand }} {{ $booking->car->model }}" class="img-fluid rounded">
                            </div>
                            <div class="col-md-8">
                                <h5>{{ $booking->car->brand }} {{ $booking->car->model }}</h5>
                                <p class="text-muted mb-2">{{ $booking->car->year }} • {{ ucfirst($booking->car->transmission) }} • {{ $booking->car->fuel_type }}</p>
                                <p class="mb-1">
                                    <span class="badge bg-light text-dark me-2"><i class="fas fa-users me-1"></i> {{ $booking->car->seats }} seats</span>
                                    <span class="badge bg-light text-dark me-2"><i class="fas fa-palette me-1"></i> {{ $booking->car->color }}</span>
                                    <span class="badge bg-light text-dark"><i class="fas fa-id-card me-1"></i> {{ $booking->car->license_plate }}</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="text-muted">Rental Period</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <p class="mb-1">Pickup Date:</p>
                                    <h6><i class="fas fa-calendar-alt me-2 text-primary"></i> {{ $booking->start_date->format('M d, Y') }}</h6>
                                </div>
                                <div>
                                    <p class="mb-1">Pickup Location:</p>
                                    <h6><i class="fas fa-map-marker-alt me-2 text-primary"></i> {{ ucfirst($booking->pickup_location) }}</h6>
                                    @if($booking->pickup_location == 'custom' && $booking->custom_pickup_address)
                                        <p class="small text-muted">{{ $booking->custom_pickup_address }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <p class="mb-1">Return Date:</p>
                                    <h6><i class="fas fa-calendar-check me-2 text-primary"></i> {{ $booking->end_date->format('M d, Y') }}</h6>
                                </div>
                                <div>
                                    <p class="mb-1">Return Location:</p>
                                    <h6><i class="fas fa-map-marker-alt me-2 text-primary"></i> {{ ucfirst($booking->return_location) }}</h6>
                                    @if($booking->return_location == 'custom' && $booking->custom_return_address)
                                        <p class="small text-muted">{{ $booking->custom_return_address }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="mt-2 p-2 bg-light rounded">
                            <p class="mb-1">Duration:</p>
                            <h5>{{ $booking->start_date->diffInDays($booking->end_date) }} days</h5>
                        </div>
                    </div>

                    @if($booking->notes)
                    <div class="mb-4">
                        <h6 class="text-muted">Additional Notes</h6>
                        <p>{{ $booking->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Driver Information -->
            @if($booking->driver_name || $booking->driver_phone || $booking->driver_license)
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Driver Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-1 text-muted">Driver's Name</p>
                            <h6>{{ $booking->driver_name ?? auth()->user()->name }}</h6>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1 text-muted">Driver's Phone</p>
                            <h6>{{ $booking->driver_phone ?? auth()->user()->phone ?? 'Not provided' }}</h6>
                        </div>
                    </div>
                    @if($booking->driver_license)
                    <div class="mt-3">
                        <p class="mb-1 text-muted">Driver's License Number</p>
                        <h6>{{ $booking->driver_license }}</h6>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <div class="col-md-5">
            <!-- Payment Information -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Payment Information</h5>
                        <span class="badge 
                            @if($booking->payment_status == 'pending') bg-warning text-dark
                            @elseif($booking->payment_status == 'paid') bg-success
                            @elseif($booking->payment_status == 'refunded') bg-secondary
                            @endif">
                            {{ ucfirst($booking->payment_status) }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Daily Rate:</span>
                            <span>${{ number_format($booking->car->price_per_day, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Number of Days:</span>
                            <span>{{ $booking->start_date->diffInDays($booking->end_date) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span>${{ number_format($booking->car->price_per_day * $booking->start_date->diffInDays($booking->end_date), 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tax (10%):</span>
                            <span>${{ number_format($booking->car->price_per_day * $booking->start_date->diffInDays($booking->end_date) * 0.1, 2) }}</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <strong>Total Amount:</strong>
                            <strong class="text-primary">${{ number_format($booking->total_price, 2) }}</strong>
                        </div>
                    </div>

                    @if($booking->payment && $booking->payment->payment_method)
                    <div class="mb-4">
                        <h6 class="text-muted">Payment Method</h6>
                        <div class="payment-method-info p-3 bg-light rounded">
                            @if($booking->payment->payment_method == 'credit_card')
                                <div><i class="fas fa-credit-card me-2 text-primary"></i> Credit Card</div>
                            @elseif($booking->payment->payment_method == 'bank_transfer')
                                <div><i class="fas fa-university me-2 text-primary"></i> Bank Transfer</div>
                            @elseif($booking->payment->payment_method == 'paypal')
                                <div><i class="fab fa-paypal me-2 text-primary"></i> PayPal</div>
                            @elseif(in_array($booking->payment->payment_method, ['bca', 'mandiri', 'bni', 'bri']))
                                <div><i class="fas fa-university me-2 text-primary"></i> Bank {{ strtoupper($booking->payment->payment_method) }}</div>
                            @else
                                <div><i class="fas fa-money-bill-wave me-2 text-primary"></i> {{ ucfirst($booking->payment->payment_method) }}</div>
                            @endif
                            
                            @if($booking->payment->payment_date)
                                <div class="small text-muted mt-1">Paid on: {{ $booking->payment->payment_date->format('M d, Y H:i') }}</div>
                            @endif
                        </div>
                    </div>
                    @endif

                    @if($booking->payment && $booking->payment->payment_proof && $booking->payment_status !== 'paid')
                    <div class="mb-4">
                        <h6 class="text-muted">Payment Proof</h6>
                        <img src="{{ asset('storage/' . $booking->payment->payment_proof) }}" alt="Payment Proof" class="img-fluid rounded mb-2">
                        <div class="small text-muted">
                            Uploaded on: {{ $booking->payment->updated_at->format('M d, Y H:i') }}
                        </div>
                        <div class="alert alert-info mt-2 small">
                            <i class="fas fa-info-circle"></i> Your payment is being verified by our team.
                        </div>
                    </div>
                    @endif

                    @if($booking->status != 'cancelled' && $booking->payment_status == 'pending')
                    <div class="d-grid gap-2">
                        <a href="{{ route('customer.payments.show', $booking->id) }}" class="btn btn-primary">
                            <i class="fas fa-credit-card me-2"></i> Make Payment
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Status and Actions -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Status and Actions</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <div class="status-timeline">
                            <div class="status-step {{ in_array($booking->status, ['pending', 'confirmed', 'ongoing', 'completed']) ? 'completed' : '' }}">
                                <div class="status-icon"><i class="fas fa-file-alt"></i></div>
                                <div class="status-label">Booked</div>
                                <div class="status-date">{{ $booking->created_at->format('M d') }}</div>
                            </div>
                            <div class="status-step {{ in_array($booking->status, ['confirmed', 'ongoing', 'completed']) ? 'completed' : ($booking->status == 'cancelled' ? 'cancelled' : '') }}">
                                <div class="status-icon"><i class="fas fa-check-circle"></i></div>
                                <div class="status-label">Confirmed</div>
                                @if(in_array($booking->status, ['confirmed', 'ongoing', 'completed']))
                                    <div class="status-date">
                                        {{ $booking->updated_at->format('M d') }}
                                    </div>
                                @endif
                            </div>
                            <div class="status-step {{ in_array($booking->status, ['ongoing', 'completed']) ? 'completed' : '' }}">
                                <div class="status-icon"><i class="fas fa-car"></i></div>
                                <div class="status-label">Ongoing</div>
                                @if(in_array($booking->status, ['ongoing', 'completed']))
                                    <div class="status-date">
                                        {{ $booking->start_date->format('M d') }}
                                    </div>
                                @endif
                            </div>
                            <div class="status-step {{ $booking->status == 'completed' ? 'completed' : '' }}">
                                <div class="status-icon"><i class="fas fa-flag-checkered"></i></div>
                                <div class="status-label">Completed</div>
                                @if($booking->status == 'completed')
                                    <div class="status-date">
                                        {{ $booking->end_date->format('M d') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="action-buttons">
                        @if($booking->status == 'pending' && $booking->payment_status == 'pending')
                            <div class="d-grid gap-2">
                                <a href="{{ route('customer.payments.show', $booking->id) }}" class="btn btn-primary">
                                    <i class="fas fa-credit-card me-2"></i> Make Payment
                                </a>
                                <form action="{{ route('customer.bookings.cancel', $booking->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-outline-danger w-100" onclick="return confirm('Are you sure you want to cancel this booking? This action cannot be undone.')">
                                        <i class="fas fa-times me-2"></i> Cancel Booking
                                    </button>
                                </form>
                            </div>
                        @elseif($booking->status == 'confirmed' && $booking->start_date->isFuture())
                            <form action="{{ route('customer.bookings.cancel', $booking->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-outline-danger w-100" onclick="return confirm('Are you sure you want to cancel this booking? This action cannot be undone.')">
                                    <i class="fas fa-times me-2"></i> Cancel Booking
                                </button>
                            </form>
                        @elseif($booking->status == 'completed')
                            <a href="{{ route('customer.bookings.review', $booking->id) }}" class="btn btn-outline-primary w-100 mb-2">
                                <i class="fas fa-star me-2"></i> Leave a Review
                            </a>
                            <a href="{{ route('customer.cars.show', $booking->car->id) }}" class="btn btn-outline-success w-100">
                                <i class="fas fa-redo me-2"></i> Book Again
                            </a>
                        @elseif($booking->status == 'cancelled')
                            <div class="alert alert-secondary">
                                <i class="fas fa-info-circle me-2"></i> This booking was cancelled on {{ $booking->updated_at->format('M d, Y') }}.
                            </div>
                            <a href="{{ route('customer.cars.show', $booking->car->id) }}" class="btn btn-outline-primary w-100">
                                <i class="fas fa-redo me-2"></i> Book Again
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Contact Support -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Need Assistance?</h5>
                </div>
                <div class="card-body">
                    <p>If you have any questions or need assistance with your booking, our support team is here to help.</p>
                    <div class="d-grid gap-2">
                        <a href="{{ route('customer.support.create', ['booking_id' => $booking->id]) }}" class="btn btn-outline-primary">
                            <i class="fas fa-headset me-2"></i> Contact Support
                        </a>
                        <a href="tel:+1234567890" class="btn btn-outline-secondary">
                            <i class="fas fa-phone-alt me-2"></i> Call Us: (123) 456-7890
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .booking-car-details img {
        max-height: 150px;
        object-fit: cover;
    }
    
    .status-timeline {
        display: flex;
        justify-content: space-between;
        margin-bottom: 20px;
        position: relative;
    }
    
    .status-timeline::before {
        content: '';
        position: absolute;
        top: 15px;
        left: 15px;
        right: 15px;
        height: 3px;
        background-color: #e9ecef;
        z-index: 1;
    }
    
    .status-step {
        display: flex;
        flex-direction: column;
        align-items: center;
        position: relative;
        z-index: 2;
        flex: 1;
    }
    
    .status-icon {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background-color: #f8f9fa;
        border: 3px solid #e9ecef;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        color: #6c757d;
        margin-bottom: 8px;
    }
    
    .status-label {
        font-size: 12px;
        font-weight: 500;
        color: #6c757d;
    }
    
    .status-date {
        font-size: 11px;
        color: #adb5bd;
    }
    
    .status-step.completed .status-icon {
        background-color: #198754;
        border-color: #198754;
        color: white;
    }
    
    .status-step.completed .status-label {
        color: #198754;
        font-weight: 600;
    }
    
    .status-step.cancelled .status-icon {
        background-color: #dc3545;
        border-color: #dc3545;
        color: white;
    }
    
    .status-step.cancelled .status-label {
        color: #dc3545;
        font-weight: 600;
    }
    
    .status-step.completed + .status-step.completed::before,
    .status-step.completed + .status-step.cancelled::before {
        background-color: #198754;
    }
    
    .payment-method-info {
        font-size: 1rem;
    }
</style>
@endsection
