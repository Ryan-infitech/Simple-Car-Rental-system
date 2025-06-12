@props(['car'])

<div class="card h-100 car-card shadow-sm">
    @if($car->status != 'available')
        <div class="car-status-badge {{ $car->status == 'maintenance' ? 'badge-maintenance' : 'badge-rented' }}">
            {{ $car->status == 'maintenance' ? 'Maintenance' : 'Rented' }}
        </div>
    @endif
    
    <img src="{{ asset($car->image_path) }}" class="card-img-top car-img" alt="{{ $car->brand }} {{ $car->model }}">
    
    <div class="card-body d-flex flex-column">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 class="card-title mb-0">{{ $car->brand }} {{ $car->model }}</h5>
            <span class="badge bg-primary">${{ number_format($car->price_per_day, 2) }}/day</span>
        </div>
        
        <p class="card-text text-muted small">{{ $car->year }} • {{ ucfirst($car->transmission) }} • {{ $car->fuel_type }}</p>
        
        <div class="car-features mb-2">
            <span class="me-3"><i class="fas fa-users me-1"></i> {{ $car->seats }} seats</span>
            <span><i class="fas fa-gas-pump me-1"></i> {{ $car->fuel_type }}</span>
        </div>
        
        <div class="mt-auto">
            <div class="d-grid gap-2">
                <a href="{{ route('cars.show', $car->id) }}" class="btn btn-outline-primary">View Details</a>
                
                @auth
                    @if($car->status == 'available')
                        <a href="{{ route('customer.bookings.create', ['car_id' => $car->id]) }}" class="btn btn-primary">
                            <i class="fas fa-calendar-plus me-1"></i> Rent Now
                        </a>
                    @else
                        <button class="btn btn-secondary" disabled>
                            <i class="fas fa-ban me-1"></i> Unavailable
                        </button>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt me-1"></i> Login to Book
                    </a>
                @endauth
            </div>
        </div>
    </div>
</div>

<style>
    .car-img {
        height: 180px;
        object-fit: cover;
    }
    
    .car-card {
        position: relative;
        transition: transform 0.3s, box-shadow 0.3s;
    }
    
    .car-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
    }
    
    .car-status-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        padding: 5px 10px;
        font-size: 0.8rem;
        font-weight: bold;
        border-radius: 4px;
        z-index: 2;
    }
    
    .badge-maintenance {
        background-color: rgba(220, 53, 69, 0.9);
        color: white;
    }
    
    .badge-rented {
        background-color: rgba(255, 193, 7, 0.9);
        color: #212529;
    }
</style>
