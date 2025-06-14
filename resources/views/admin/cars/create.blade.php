@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h1 class="mt-4">Add New Car</h1>
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-plus mr-1"></i>
            Car Details
        </div>
        <div class="card-body">
            <form action="{{ route('admin.cars.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="make">Make <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('make') is-invalid @enderror" id="make" name="make" value="{{ old('make') }}" required>
                            @error('make')
                                <span class="invalid-feedback" role="alert">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="model">Model <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('model') is-invalid @enderror" id="model" name="model" value="{{ old('model') }}" required>
                            @error('model')
                                <span class="invalid-feedback" role="alert">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="year">Year <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('year') is-invalid @enderror" id="year" name="year" value="{{ old('year') }}" required min="1900" max="{{ date('Y') + 1 }}">
                            @error('year')
                                <span class="invalid-feedback" role="alert">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="license_plate">License Plate <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('license_plate') is-invalid @enderror" id="license_plate" name="license_plate" value="{{ old('license_plate') }}" required>
                            @error('license_plate')
                                <span class="invalid-feedback" role="alert">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="color">Color <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('color') is-invalid @enderror" id="color" name="color" value="{{ old('color') }}" required>
                            @error('color')
                                <span class="invalid-feedback" role="alert">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="daily_rate">Daily Rate (USD) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">$</span>
                                </div>
                                <input type="number" step="0.01" min="0" class="form-control @error('daily_rate') is-invalid @enderror" id="daily_rate" name="daily_rate" value="{{ old('daily_rate') }}" required>
                                @error('daily_rate')
                                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="status">Status <span class="text-danger">*</span></label>
                            <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>Available</option>
                                <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                <option value="rented" {{ old('status') == 'rented' ? 'selected' : '' }}>Rented</option>
                            </select>
                            @error('status')
                                <span class="invalid-feedback" role="alert">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="seats">Number of Seats <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('seats') is-invalid @enderror" id="seats" name="seats" value="{{ old('seats') }}" min="1" required>
                            @error('seats')
                                <span class="invalid-feedback" role="alert">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="transmission">Transmission <span class="text-danger">*</span></label>
                            <select class="form-control @error('transmission') is-invalid @enderror" id="transmission" name="transmission" required>
                                <option value="automatic" {{ old('transmission') == 'automatic' ? 'selected' : '' }}>Automatic</option>
                                <option value="manual" {{ old('transmission') == 'manual' ? 'selected' : '' }}>Manual</option>
                            </select>
                            @error('transmission')
                                <span class="invalid-feedback" role="alert">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="fuel_type">Fuel Type <span class="text-danger">*</span></label>
                    <select class="form-control @error('fuel_type') is-invalid @enderror" id="fuel_type" name="fuel_type" required>
                        <option value="gasoline" {{ old('fuel_type') == 'gasoline' ? 'selected' : '' }}>Gasoline</option>
                        <option value="diesel" {{ old('fuel_type') == 'diesel' ? 'selected' : '' }}>Diesel</option>
                        <option value="electric" {{ old('fuel_type') == 'electric' ? 'selected' : '' }}>Electric</option>
                        <option value="hybrid" {{ old('fuel_type') == 'hybrid' ? 'selected' : '' }}>Hybrid</option>
                    </select>
                    @error('fuel_type')
                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="image">Car Image</label>
                    <input type="file" class="form-control-file @error('image') is-invalid @enderror" id="image" name="image">
                    <small class="form-text text-muted">Upload an image of the car. Max size: 2MB. Supported formats: JPG, PNG.</small>
                    @error('image')
                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                    @error('description')
                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-success">Add Car</button>
                    <a href="{{ route('admin.cars.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
