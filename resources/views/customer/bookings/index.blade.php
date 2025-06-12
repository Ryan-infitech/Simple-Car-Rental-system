@extends('layouts.customer')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>My Bookings</h1>
            <p class="text-muted">Manage your car rentals</p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('customer.home') }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-home me-2"></i> Dashboard
            </a>
            <a href="{{ route('customer.cars.index') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i> New Booking
            </a>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <ul class="nav nav-tabs card-header-tabs" id="bookingTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all-bookings" type="button" role="tab" aria-controls="all-bookings" aria-selected="true">
                        All ({{ $bookings->count() }})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="upcoming-tab" data-bs-toggle="tab" data-bs-target="#upcoming-bookings" type="button" role="tab" aria-controls="upcoming-bookings" aria-selected="false">
                        Upcoming ({{ $upcomingBookings->count() }})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="active-tab" data-bs-toggle="tab" data-bs-target="#active-bookings" type="button" role="tab" aria-controls="active-bookings" aria-selected="false">
                        Active ({{ $activeBookings->count() }})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="past-tab" data-bs-toggle="tab" data-bs-target="#past-bookings" type="button" role="tab" aria-controls="past-bookings" aria-selected="false">
                        Past ({{ $pastBookings->count() }})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="cancelled-tab" data-bs-toggle="tab" data-bs-target="#cancelled-bookings" type="button" role="tab" aria-controls="cancelled-bookings" aria-selected="false">
                        Cancelled ({{ $cancelledBookings->count() }})
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="bookingTabsContent">
                <!-- All Bookings Tab -->
                <div class="tab-pane fade show active" id="all-bookings" role="tabpanel" aria-labelledby="all-tab">
                    @if($bookings->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Booking #</th>
                                        <th>Car</th>
                                        <th>Dates</th>
                                        <th>Status</th>
                                        <th>Payment</th>
                                        <th>Amount</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($bookings as $booking)
                                        <tr>
                                            <td>#{{ $booking->id }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="car-thumbnail me-2">
                                                        <img src="{{ asset($booking->car->image_path) }}" alt="{{ $booking->car->brand }}" width="60" height="40" class="rounded">
                                                    </div>
                                                    <div>
                                                        <div class="fw-medium">{{ $booking->car->brand }} {{ $booking->car->model }}</div>
                                                        <small class="text-muted">{{ $booking->car->year }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div>{{ $booking->start_date->format('M d, Y') }}</div>
                                                <small class="text-muted">to {{ $booking->end_date->format('M d, Y') }}</small>
                                            </td>
                                            <td>
                                                @if($booking->status == 'pending')
                                                    <span class="badge bg-warning text-dark">Pending</span>
                                                @elseif($booking->status == 'confirmed')
                                                    <span class="badge bg-success">Confirmed</span>
                                                @elseif($booking->status == 'ongoing')
                                                    <span class="badge bg-primary">Ongoing</span>
                                                @elseif($booking->status == 'completed')
                                                    <span class="badge bg-info">Completed</span>
                                                @elseif($booking->status == 'cancelled')
                                                    <span class="badge bg-danger">Cancelled</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($booking->payment_status == 'pending')
                                                    <span class="badge bg-warning text-dark">Pending</span>
                                                @elseif($booking->payment_status == 'paid')
                                                    <span class="badge bg-success">Paid</span>
                                                @elseif($booking->payment_status == 'refunded')
                                                    <span class="badge bg-secondary">Refunded</span>
                                                @endif
                                            </td>
                                            <td>${{ number_format($booking->total_price, 2) }}</td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('customer.bookings.show', $booking->id) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i> View
                                                    </a>
                                                    
                                                    @if($booking->status == 'pending' && $booking->payment_status == 'pending')
                                                        <a href="{{ route('customer.payments.show', $booking->id) }}" class="btn btn-sm btn-success">
                                                            <i class="fas fa-credit-card"></i> Pay
                                                        </a>
                                                    @endif
                                                    
                                                    @if(in_array($booking->status, ['pending', 'confirmed']) && $booking->start_date->isFuture())
                                                        <form action="{{ route('customer.bookings.cancel', $booking->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to cancel this booking?');">
                                                                <i class="fas fa-times"></i> Cancel
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center p-5">
                            <i class="fas fa-calendar-alt fa-4x text-muted mb-3"></i>
                            <h4>No Bookings Yet</h4>
                            <p>You haven't made any car bookings yet.</p>
                            <a href="{{ route('customer.cars.index') }}" class="btn btn-primary mt-3">
                                <i class="fas fa-car me-2"></i> Rent a Car
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Upcoming Bookings Tab -->
                <div class="tab-pane fade" id="upcoming-bookings" role="tabpanel" aria-labelledby="upcoming-tab">
                    @if($upcomingBookings->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Booking #</th>
                                        <th>Car</th>
                                        <th>Dates</th>
                                        <th>Status</th>
                                        <th>Payment</th>
                                        <th>Amount</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($upcomingBookings as $booking)
                                        <tr>
                                            <td>#{{ $booking->id }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="car-thumbnail me-2">
                                                        <img src="{{ asset($booking->car->image_path) }}" alt="{{ $booking->car->brand }}" width="60" height="40" class="rounded">
                                                    </div>
                                                    <div>
                                                        <div class="fw-medium">{{ $booking->car->brand }} {{ $booking->car->model }}</div>
                                                        <small class="text-muted">{{ $booking->car->year }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div>{{ $booking->start_date->format('M d, Y') }}</div>
                                                <small class="text-muted">to {{ $booking->end_date->format('M d, Y') }}</small>
                                            </td>
                                            <td>
                                                @if($booking->status == 'pending')
                                                    <span class="badge bg-warning text-dark">Pending</span>
                                                @elseif($booking->status == 'confirmed')
                                                    <span class="badge bg-success">Confirmed</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($booking->payment_status == 'pending')
                                                    <span class="badge bg-warning text-dark">Pending</span>
                                                @elseif($booking->payment_status == 'paid')
                                                    <span class="badge bg-success">Paid</span>
                                                @endif
                                            </td>
                                            <td>${{ number_format($booking->total_price, 2) }}</td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('customer.bookings.show', $booking->id) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i> View
                                                    </a>
                                                    
                                                    @if($booking->status == 'pending' && $booking->payment_status == 'pending')
                                                        <a href="{{ route('customer.payments.show', $booking->id) }}" class="btn btn-sm btn-success">
                                                            <i class="fas fa-credit-card"></i> Pay
                                                        </a>
                                                    @endif
                                                    
                                                    <form action="{{ route('customer.bookings.cancel', $booking->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to cancel this booking?');">
                                                            <i class="fas fa-times"></i> Cancel
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center p-5">
                            <i class="fas fa-hourglass-start fa-4x text-muted mb-3"></i>
                            <h4>No Upcoming Bookings</h4>
                            <p>You don't have any upcoming reservations.</p>
                            <a href="{{ route('customer.cars.index') }}" class="btn btn-primary mt-3">
                                <i class="fas fa-car me-2"></i> Book a Car Now
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Active Bookings Tab -->
                <div class="tab-pane fade" id="active-bookings" role="tabpanel" aria-labelledby="active-tab">
                    @if($activeBookings->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Booking #</th>
                                        <th>Car</th>
                                        <th>Pick-up Date</th>
                                        <th>Return Date</th>
                                        <th>Days Left</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($activeBookings as $booking)
                                        <tr>
                                            <td>#{{ $booking->id }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="car-thumbnail me-2">
                                                        <img src="{{ asset($booking->car->image_path) }}" alt="{{ $booking->car->brand }}" width="60" height="40" class="rounded">
                                                    </div>
                                                    <div>
                                                        <div class="fw-medium">{{ $booking->car->brand }} {{ $booking->car->model }}</div>
                                                        <small class="text-muted">{{ $booking->car->year }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $booking->start_date->format('M d, Y') }}</td>
                                            <td>{{ $booking->end_date->format('M d, Y') }}</td>
                                            <td>
                                                <span class="badge bg-primary">
                                                    {{ now()->diffInDays($booking->end_date) }} days left
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('customer.bookings.show', $booking->id) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i> View Details
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center p-5">
                            <i class="fas fa-car fa-4x text-muted mb-3"></i>
                            <h4>No Active Rentals</h4>
                            <p>You don't have any active car rentals.</p>
                        </div>
                    @endif
                </div>

                <!-- Past Bookings Tab -->
                <div class="tab-pane fade" id="past-bookings" role="tabpanel" aria-labelledby="past-tab">
                    @if($pastBookings->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Booking #</th>
                                        <th>Car</th>
                                        <th>Dates</th>
                                        <th>Duration</th>
                                        <th>Total</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pastBookings as $booking)
                                        <tr>
                                            <td>#{{ $booking->id }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="car-thumbnail me-2">
                                                        <img src="{{ asset($booking->car->image_path) }}" alt="{{ $booking->car->brand }}" width="60" height="40" class="rounded">
                                                    </div>
                                                    <div>
                                                        <div class="fw-medium">{{ $booking->car->brand }} {{ $booking->car->model }}</div>
                                                        <small class="text-muted">{{ $booking->car->year }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div>{{ $booking->start_date->format('M d, Y') }}</div>
                                                <small class="text-muted">to {{ $booking->end_date->format('M d, Y') }}</small>
                                            </td>
                                            <td>{{ $booking->start_date->diffInDays($booking->end_date) }} days</td>
                                            <td>${{ number_format($booking->total_price, 2) }}</td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('customer.bookings.show', $booking->id) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i> View
                                                    </a>
                                                    <a href="{{ route('customer.cars.show', $booking->car->id) }}" class="btn btn-sm btn-outline-success">
                                                        <i class="fas fa-redo"></i> Book Again
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center p-5">
                            <i class="fas fa-history fa-4x text-muted mb-3"></i>
                            <h4>No Past Bookings</h4>
                            <p>You don't have any past booking history.</p>
                        </div>
                    @endif
                </div>

                <!-- Cancelled Bookings Tab -->
                <div class="tab-pane fade" id="cancelled-bookings" role="tabpanel" aria-labelledby="cancelled-tab">
                    @if($cancelledBookings->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Booking #</th>
                                        <th>Car</th>
                                        <th>Dates</th>
                                        <th>Cancellation Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cancelledBookings as $booking)
                                        <tr>
                                            <td>#{{ $booking->id }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="car-thumbnail me-2">
                                                        <img src="{{ asset($booking->car->image_path) }}" alt="{{ $booking->car->brand }}" width="60" height="40" class="rounded">
                                                    </div>
                                                    <div>
                                                        <div class="fw-medium">{{ $booking->car->brand }} {{ $booking->car->model }}</div>
                                                        <small class="text-muted">{{ $booking->car->year }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div>{{ $booking->start_date->format('M d, Y') }}</div>
                                                <small class="text-muted">to {{ $booking->end_date->format('M d, Y') }}</small>
                                            </td>
                                            <td>{{ $booking->updated_at->format('M d, Y') }}</td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('customer.bookings.show', $booking->id) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i> View
                                                    </a>
                                                    <a href="{{ route('customer.cars.show', $booking->car->id) }}" class="btn btn-sm btn-outline-success">
                                                        <i class="fas fa-redo"></i> Book Again
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center p-5">
                            <i class="fas fa-ban fa-4x text-muted mb-3"></i>
                            <h4>No Cancelled Bookings</h4>
                            <p>You don't have any cancelled bookings.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
