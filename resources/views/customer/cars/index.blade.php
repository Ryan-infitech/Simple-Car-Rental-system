@extends('layouts.customer')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Our Cars</h1>
            <p class="lead text-muted">Choose from our wide selection of quality vehicles</p>
        </div>
        <div class="col-md-4">
            <div class="d-flex justify-content-md-end">
                <a href="{{ route('customer.home') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('customer.cars.index') }}" method="GET">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="search" class="form-label">Search</label>
                        <input type="text" class="form-control" id="search" name="search" placeholder="Brand, model..." value="{{ request('search') }}">
                    </div>
                    
                    <div class="col-md-2">
                        <label for="transmission" class="form-label">Transmission</label>
                        <select class="form-select" id="transmission" name="transmission">
                            <option value="">All</option>
                            <option value="automatic" {{ request('transmission') == 'automatic' ? 'selected' : '' }}>Automatic</option>
                            <option value="manual" {{ request('transmission') == 'manual' ? 'selected' : '' }}>Manual</option>
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label for="seats" class="form-label">Seats</label>
                        <select class="form-select" id="seats" name="seats">
                            <option value="">All</option>
                            <option value="2" {{ request('seats') == '2' ? 'selected' : '' }}>2 seats</option>
                            <option value="4" {{ request('seats') == '4' ? 'selected' : '' }}>4 seats</option>
                            <option value="5" {{ request('seats') == '5' ? 'selected' : '' }}>5 seats</option>
                            <option value="7" {{ request('seats') == '7' ? 'selected' : '' }}>7+ seats</option>
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label for="sort" class="form-label">Sort By</label>
                        <select class="form-select" id="sort" name="sort">
                            <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                            <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                            <option value="brand" {{ request('sort') == 'brand' ? 'selected' : '' }}>Brand (A-Z)</option>
                        </select>
                    </div>
                    
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter me-2"></i> Apply Filters
                        </button>
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-md-8">
                        <div class="d-flex gap-2">
                            @if(request('transmission') || request('seats') || request('search') || request('sort'))
                                <a href="{{ route('customer.cars.index') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-times me-1"></i> Clear All
                                </a>
                            @endif
                            
                            @if(request('transmission'))
                                <div class="badge bg-light text-dark p-2">
                                    Transmission: {{ ucfirst(request('transmission')) }}
                                    <a href="{{ route('customer.cars.index', request()->except('transmission')) }}" class="text-dark ms-1">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </div>
                            @endif
                            
                            @if(request('seats'))
                                <div class="badge bg-light text-dark p-2">
                                    Seats: {{ request('seats') }}
                                    <a href="{{ route('customer.cars.index', request()->except('seats')) }}" class="text-dark ms-1">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </div>
                            @endif
                            
                            @if(request('search'))
                                <div class="badge bg-light text-dark p-2">
                                    Search: "{{ request('search') }}"
                                    <a href="{{ route('customer.cars.index', request()->except('search')) }}" class="text-dark ms-1">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 mb-4">
            <p>Showing {{ $cars->firstItem() ?? 0 }} - {{ $cars->lastItem() ?? 0 }} of {{ $cars->total() }} cars</p>
        </div>

        @forelse($cars as $car)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 car-card">
                    @if($car->status == 'rented')
                        <div class="rented-badge">
                            <span>Currently Rented</span>
                        </div>
                    @endif

                    @if($car->status == 'maintenance')
                        <div class="maintenance-badge">
                            <span>Under Maintenance</span>
                        </div>
                    @endif
                    
                    <img src="{{ asset($car->image_path) }}" class="card-img-top car-img" alt="{{ $car->brand }} {{ $car->model }}">
                    
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="card-title mb-0">{{ $car->brand }} {{ $car->model }}</h5>
                            <span class="badge bg-primary">${{ number_format($car->price_per_day, 2) }}/day</span>
                        </div>
                        
                        <p class="card-text text-muted small">{{ $car->year }} • {{ ucfirst($car->transmission) }} • {{ $car->fuel_type }}</p>
                        
                        <div class="car-features mb-3">
                            <span class="me-3"><i class="fas fa-users me-1"></i> {{ $car->seats }} seats</span>
                            <span><i class="fas fa-gas-pump me-1"></i> {{ $car->fuel_type }}</span>
                        </div>
                        
                        <div class="d-grid gap-2 mt-3">
                            <a href="{{ route('customer.cars.show', $car->id) }}" class="btn btn-outline-primary">View Details</a>
                            
                            @if($car->status == 'available')
                                <a href="{{ route('customer.bookings.create', ['car_id' => $car->id]) }}" class="btn btn-primary">
                                    <i class="fas fa-calendar-plus me-1"></i> Rent Now
                                </a>
                            @else
                                <button class="btn btn-secondary" disabled>
                                    <i class="fas fa-ban me-1"></i> Unavailable
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info text-center p-5">
                    <i class="fas fa-car-slash fa-4x mb-3"></i>
                    <h4>No cars found</h4>
                    <p>No cars match your current filter criteria. Please try different filters.</p>
                    <a href="{{ route('customer.cars.index') }}" class="btn btn-primary mt-2">Clear Filters</a>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-center">
                {{ $cars->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .car-img {
        height: 200px;
        object-fit: cover;
    }
    
    .car-features {
        font-size: 0.9rem;
    }
    
    .car-card {
        position: relative;
        transition: transform 0.3s, box-shadow 0.3s;
    }
    
    .car-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15);
    }
    
    .rented-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        background-color: rgba(255, 193, 7, 0.9);
        color: #212529;
        padding: 5px 10px;
        font-size: 0.8rem;
        font-weight: bold;
        border-radius: 4px;
        z-index: 2;
    }
    
    .maintenance-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        background-color: rgba(220, 53, 69, 0.9);
        color: white;
        padding: 5px 10px;
        font-size: 0.8rem;
        font-weight: bold;
        border-radius: 4px;
        z-index: 2;
    }
</style>
@endsection
