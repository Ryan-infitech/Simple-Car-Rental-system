@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h1 class="mt-4">Car Management</h1>
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-car mr-1"></i>
                All Cars
            </div>
            <a href="{{ route('admin.cars.create') }}" class="btn btn-primary">Add New Car</a>
        </div>
        <div class="card-body">
            <!-- Filters -->
            <div class="mb-3">
                <form action="{{ route('admin.cars.index') }}" method="GET" class="row">
                    <div class="col-md-3 mb-2">
                        <input type="text" name="search" class="form-control" placeholder="Search make or model" value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2 mb-2">
                        <select name="status" class="form-control">
                            <option value="">All Statuses</option>
                            <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
                            <option value="rented" {{ request('status') == 'rented' ? 'selected' : '' }}>Rented</option>
                            <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-2">
                        <select name="sort" class="form-control">
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                            <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                            <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price (Low to High)</option>
                            <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price (High to Low)</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-2">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                    <div class="col-md-2 mb-2">
                        <a href="{{ route('admin.cars.index') }}" class="btn btn-secondary w-100">Reset</a>
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered" id="carsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Make</th>
                            <th>Model</th>
                            <th>Year</th>
                            <th>License Plate</th>
                            <th>Daily Rate</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cars ?? [] as $car)
                        <tr>
                            <td>{{ $car->id }}</td>
                            <td>
                                @if($car->image)
                                    <img src="{{ asset('storage/' . $car->image) }}" alt="{{ $car->make }} {{ $car->model }}" height="50">
                                @else
                                    <img src="{{ asset('images/no-image.jpg') }}" alt="No Image" height="50">
                                @endif
                            </td>
                            <td>{{ $car->make }}</td>
                            <td>{{ $car->model }}</td>
                            <td>{{ $car->year }}</td>
                            <td>{{ $car->license_plate }}</td>
                            <td>${{ number_format($car->daily_rate, 2) }}</td>
                            <td>
                                <span class="badge badge-{{ $car->status == 'available' ? 'success' : ($car->status == 'rented' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($car->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.cars.show', $car->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.cars.edit', $car->id) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.cars.destroy', $car->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this car?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center">No cars found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $cars->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
