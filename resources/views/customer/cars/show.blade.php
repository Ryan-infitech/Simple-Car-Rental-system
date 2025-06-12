@extends('layouts.customer')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('customer.home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('customer.cars.index') }}">Cars</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $car->brand }} {{ $car->model }}</li>
                </ol>
            </nav>
        </div>
        <div class="col-md-4">
            <div class="d-flex justify-content-md-end">
                <a href="{{ route('customer.cars.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i> Back to Cars
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Car Images Carousel -->
            <div class="card shadow-sm mb-4">
                <div class="card-body p-0">
                    <div id="carImagesCarousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-indicators">
                            <button type="button" data-bs-target="#carImagesCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                            @if($car->images->count() > 0)
                                @foreach($car->images as $key => $image)
                                    <button type="button" data-bs-target="#carImagesCarousel" data-bs-slide-to="{{ $key + 1 }}" aria-label="Slide {{ $key + 2 }}"></button>
                                @endforeach
                            @endif
                        </div>
                        <div class="carousel-inner">
                            <div class="carousel-item active">
                                <img src="{{ asset($car->image_path) }}" class="d-block w-100 car-main-img" alt="{{ $car->brand }} {{ $car->model }}">
                            </div>
                            @if($car->images->count() > 0)
                                @foreach($car->images as $image)
                                    <div class="carousel-item">
                                        <img src="{{ asset($image->image_path) }}" class="d-block w-100 car-main-img" alt="{{ $car->brand }} {{ $car->model }}">
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#carImagesCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carImagesCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Car Details -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Car Details</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p class="mb-1 text-muted small">Brand & Model</p>
                            <h4>{{ $car->brand }} {{ $car->model }}</h4>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <p class="mb-1 text-muted small">Price per day</p>
                            <h4 class="text-primary">${{ number_format($car->price_per_day, 2) }}</h4>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="car-badges mb-3">
                                <span class="badge bg-light text-dark mb-2 me-2 p-2">
                                    <i class="fas fa-calendar me-1"></i> Year: {{ $car->year }}
                                </span>
                                <span class="badge bg-light text-dark mb-2 me-2 p-2">
                                    <i class="fas fa-gas-pump me-1"></i> Fuel: {{ $car->fuel_type }}
                                </span>
                                <span class="badge bg-light text-dark mb-2 me-2 p-2">
                                    <i class="fas fa-cog me-1"></i> {{ ucfirst($car->transmission) }}
                                </span>
                                <span class="badge bg-light text-dark mb-2 me-2 p-2">
                                    <i class="fas fa-users me-1"></i> {{ $car->seats }} Seats
                                </span>
                                <span class="badge bg-light text-dark mb-2 me-2 p-2">
                                    <i class="fas fa-palette me-1"></i> Color: {{ $car->color }}
                                </span>
                                <span class="badge bg-light text-dark mb-2 me-2 p-2">
                                    <i class="fas fa-id-card me-1"></i> Plate: {{ $car->license_plate }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <h5>Description</h5>
                            <p>{{ $car->description }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Car Features -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Features & Amenities</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-group list-group-flush feature-list">
                                <li class="list-group-item"><i class="fas fa-check-circle text-success me-2"></i> Air Conditioning</li>
                                <li class="list-group-item"><i class="fas fa-check-circle text-success me-2"></i> Power Steering</li>
                                <li class="list-group-item"><i class="fas fa-check-circle text-success me-2"></i> Power Windows</li>
                                <li class="list-group-item"><i class="fas fa-check-circle text-success me-2"></i> Anti-lock Braking System</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-group list-group-flush feature-list">
                                <li class="list-group-item"><i class="fas fa-check-circle text-success me-2"></i> Bluetooth Connectivity</li>
                                <li class="list-group-item"><i class="fas fa-check-circle text-success me-2"></i> USB Port</li>
                                <li class="list-group-item"><i class="fas fa-check-circle text-success me-2"></i> GPS Navigation</li>
                                <li class="list-group-item"><i class="fas fa-check-circle text-success me-2"></i> Airbags</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Booking Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Book This Car</h5>
                </div>
                <div class="card-body">
                    @if($car->status == 'available')
                        <p class="text-success mb-4">
                            <i class="fas fa-check-circle me-2"></i> Available for booking
                        </p>
                        <form action="{{ route('customer.bookings.create') }}" method="GET">
                            <input type="hidden" name="car_id" value="{{ $car->id }}">
                            
                            <div class="mb-3">
                                <label for="start_date" class="form-label">Pickup Date</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" min="{{ date('Y-m-d') }}" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="end_date" class="form-label">Return Date</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="pickup_location" class="form-label">Pickup Location</label>
                                <select class="form-select" id="pickup_location" name="pickup_location" required>
                                    <option value="">Select location...</option>
                                    <option value="office">Main Office</option>
                                    <option value="airport">Airport</option>
                                    <option value="hotel">Hotel Delivery</option>
                                </select>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-calendar-plus me-2"></i> Continue Booking
                                </button>
                            </div>
                        </form>
                    @elseif($car->status == 'rented')
                        <div class="text-center py-4">
                            <i class="fas fa-calendar-times fa-3x text-warning mb-3"></i>
                            <h5>Currently Rented</h5>
                            <p class="text-muted">This car is currently rented. Please check back later or explore other cars.</p>
                            <a href="{{ route('customer.cars.index') }}" class="btn btn-outline-primary">Browse Other Cars</a>
                        </div>
                    @elseif($car->status == 'maintenance')
                        <div class="text-center py-4">
                            <i class="fas fa-tools fa-3x text-danger mb-3"></i>
                            <h5>Under Maintenance</h5>
                            <p class="text-muted">This car is currently under maintenance. Please check back later or explore other cars.</p>
                            <a href="{{ route('customer.cars.index') }}" class="btn btn-outline-primary">Browse Other Cars</a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Car Status -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Current Status</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="status-indicator 
                            @if($car->status == 'available') bg-success 
                            @elseif($car->status == 'rented') bg-warning 
                            @else bg-danger @endif">
                        </div>
                        <span class="ms-2">
                            @if($car->status == 'available')
                                Available for Rental
                            @elseif($car->status == 'rented')
                                Currently Rented
                            @elseif($car->status == 'maintenance')
                                Under Maintenance
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            <!-- Price Calculation -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Price Calculator</h5>
                </div>
                <div class="card-body">
                    <div class="price-calculator">
                        <div class="mb-3">
                            <label for="calc_days" class="form-label">Number of Days</label>
                            <input type="number" class="form-control" id="calc_days" min="1" value="1">
                        </div>
                        
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span>Daily Rate:</span>
                                <span>${{ number_format($car->price_per_day, 2) }}</span>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <h5 class="mb-0">Estimated Total:</h5>
                            <h5 class="mb-0 text-primary" id="estimated_total">${{ number_format($car->price_per_day, 2) }}</h5>
                        </div>
                        
                        <div class="text-center mt-3">
                            <small class="text-muted">* Taxes and fees may apply</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Need Help Card -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5>Need Help?</h5>
                    <p class="text-muted">If you have any questions about this vehicle or the booking process, feel free to contact us.</p>
                    <div class="d-grid">
                        <a href="#" class="btn btn-outline-primary">
                            <i class="fas fa-headset me-2"></i> Contact Support
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
    .car-main-img {
        height: 400px;
        object-fit: cover;
    }
    
    .car-badges .badge {
        font-weight: normal;
    }
    
    .feature-list .list-group-item {
        border-left: none;
        border-right: none;
        padding: 0.75rem 0;
    }
    
    .feature-list .list-group-item:first-child {
        border-top: none;
    }
    
    .feature-list .list-group-item:last-child {
        border-bottom: none;
    }
    
    .status-indicator {
        width: 12px;
        height: 12px;
        border-radius: 50%;
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const calcDaysInput = document.getElementById('calc_days');
        const estimatedTotalEl = document.getElementById('estimated_total');
        const dailyRate = {{ $car->price_per_day }};
        
        calcDaysInput.addEventListener('input', function() {
            const days = parseInt(this.value) || 1;
            const total = (days * dailyRate).toFixed(2);
            estimatedTotalEl.textContent = '$' + total;
        });
        
        // Set min date for start_date to today
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');
        
        if (startDateInput && endDateInput) {
            startDateInput.addEventListener('change', function() {
                const startDate = new Date(this.value);
                const minEndDate = new Date(startDate);
                minEndDate.setDate(minEndDate.getDate() + 1);
                
                // Format minEndDate to YYYY-MM-DD
                const year = minEndDate.getFullYear();
                const month = String(minEndDate.getMonth() + 1).padStart(2, '0');
                const day = String(minEndDate.getDate()).padStart(2, '0');
                
                endDateInput.min = `${year}-${month}-${day}`;
                
                // If current end_date is before new min, update it
                if (new Date(endDateInput.value) < minEndDate) {
                    endDateInput.value = `${year}-${month}-${day}`;
                }
            });
        }
    });
</script>
@endsection
