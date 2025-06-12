@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h1>Car Details</h1>
        <div>
            <a href="{{ route('admin.cars.edit', $car->id) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('admin.cars.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">{{ $car->make }} {{ $car->model }} ({{ $car->year }})</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted">Basic Information</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Make:</th>
                                    <td>{{ $car->make }}</td>
                                </tr>
                                <tr>
                                    <th>Model:</th>
                                    <td>{{ $car->model }}</td>
                                </tr>
                                <tr>
                                    <th>Year:</th>
                                    <td>{{ $car->year }}</td>
                                </tr>
                                <tr>
                                    <th>Color:</th>
                                    <td>{{ $car->color }}</td>
                                </tr>
                                <tr>
                                    <th>License Plate:</th>
                                    <td>{{ $car->license_plate }}</td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        <span class="badge badge-{{ $car->status == 'available' ? 'success' : ($car->status == 'rented' ? 'warning' : 'danger') }}">
                                            {{ ucfirst($car->status) }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Technical Details</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Seats:</th>
                                    <td>{{ $car->seats }}</td>
                                </tr>
                                <tr>
                                    <th>Transmission:</th>
                                    <td>{{ ucfirst($car->transmission) }}</td>
                                </tr>
                                <tr>
                                    <th>Fuel Type:</th>
                                    <td>{{ ucfirst($car->fuel_type) }}</td>
                                </tr>
                                <tr>
                                    <th>Daily Rate:</th>
                                    <td>${{ number_format($car->daily_rate, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Added On:</th>
                                    <td>{{ $car->created_at->format('M d, Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Last Updated:</th>
                                    <td>{{ $car->updated_at->format('M d, Y') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($car->description)
                    <div class="mb-4">
                        <h6 class="text-muted">Description</h6>
                        <p class="card-text">{{ $car->description }}</p>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Rental History -->
            @if(isset($rentals) && count($rentals) > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Rental History</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Rental ID</th>
                                    <th>Customer</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Status</th>
                                    <th>Total Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rentals as $rental)
                                <tr>
                                    <td>{{ $rental->id }}</td>
                                    <td>{{ $rental->user->name }}</td>
                                    <td>{{ $rental->start_date->format('M d, Y') }}</td>
                                    <td>{{ $rental->end_date->format('M d, Y') }}</td>
                                    <td>
                                        <span class="badge badge-{{ $rental->status == 'active' ? 'success' : ($rental->status == 'pending' ? 'warning' : 'secondary') }}">
                                            {{ ucfirst($rental->status) }}
                                        </span>
                                    </td>
                                    <td>${{ number_format($rental->total_price, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
            
            <!-- Maintenance Records (if applicable) -->
            @if(isset($maintenances) && count($maintenances) > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Maintenance History</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Cost</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($maintenances as $maintenance)
                                <tr>
                                    <td>{{ $maintenance->date->format('M d, Y') }}</td>
                                    <td>{{ $maintenance->description }}</td>
                                    <td>${{ number_format($maintenance->cost, 2) }}</td>
                                    <td>
                                        <span class="badge badge-{{ $maintenance->status == 'completed' ? 'success' : 'warning' }}">
                                            {{ ucfirst($maintenance->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>
        
        <div class="col-md-4">
            <!-- Car Image -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Car Image</h5>
                </div>
                <div class="card-body text-center">
                    @if($car->image)
                        <img src="{{ asset('storage/' . $car->image) }}" alt="{{ $car->make }} {{ $car->model }}" class="img-fluid rounded">
                    @else
                        <img src="{{ asset('images/no-image.jpg') }}" alt="No Image Available" class="img-fluid rounded">
                        <p class="text-muted mt-2">No image available</p>
                    @endif
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <a href="{{ route('admin.cars.edit', $car->id) }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-edit mr-2"></i> Edit Car Details
                        </a>
                        @if($car->status != 'maintenance')
                        <a href="{{ route('admin.maintenance.create', ['car_id' => $car->id]) }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-tools mr-2"></i> Schedule Maintenance
                        </a>
                        @endif
                        @if($car->status == 'available')
                        <a href="{{ route('admin.rentals.create', ['car_id' => $car->id]) }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-calendar-plus mr-2"></i> Create New Rental
                        </a>
                        @endif
                        <button type="button" class="list-group-item list-group-item-action" data-toggle="modal" data-target="#deleteCarModal">
                            <i class="fas fa-trash mr-2 text-danger"></i> Delete Car
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Car Modal -->
<div class="modal fade" id="deleteCarModal" tabindex="-1" role="dialog" aria-labelledby="deleteCarModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteCarModalLabel">Confirm Delete</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this car ({{ $car->make }} {{ $car->model }})?
                This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <form action="{{ route('admin.cars.destroy', $car->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Car</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
