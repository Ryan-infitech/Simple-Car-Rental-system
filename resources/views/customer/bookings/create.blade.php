@extends('layouts.customer')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Create a New Booking</h1>
            <p class="text-muted">Please fill out the details below to book a car</p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('customer.cars.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> Back to Cars
            </a>
        </div>
    </div>

    <form action="{{ route('customer.bookings.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Booking Details</h5>
                    </div>
                    <div class="card-body">
                        @if (!isset($car))
                            <div class="mb-4">
                                <label for="car_id" class="form-label">Select Car</label>
                                <select name="car_id" id="car_id" class="form-select @error('car_id') is-invalid @enderror" required>
                                    <option value="">-- Select a car --</option>
                                    @foreach($cars as $carOption)
                                        <option value="{{ $carOption->id }}" {{ old('car_id') == $carOption->id || (isset($selectedCarId) && $selectedCarId == $carOption->id) ? 'selected' : '' }}>
                                            {{ $carOption->brand }} {{ $carOption->model }} ({{ $carOption->year }}) - ${{ number_format($carOption->price_per_day, 2) }}/day
                                        </option>
                                    @endforeach
                                </select>
                                @error('car_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @else
                            <input type="hidden" name="car_id" value="{{ $car->id }}">
                            <div class="mb-4">
                                <div class="selected-car">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <img src="{{ asset($car->image_path) }}" alt="{{ $car->brand }} {{ $car->model }}" class="img-fluid rounded">
                                        </div>
                                        <div class="col-md-8">
                                            <h5>{{ $car->brand }} {{ $car->model }} ({{ $car->year }})</h5>
                                            <p class="text-muted mb-2">
                                                <i class="fas fa-gas-pump me-1"></i> {{ $car->fuel_type }} | 
                                                <i class="fas fa-cog me-1"></i> {{ ucfirst($car->transmission) }} | 
                                                <i class="fas fa-users me-1"></i> {{ $car->seats }} seats
                                            </p>
                                            <h6 class="text-primary">${{ number_format($car->price_per_day, 2) }} / day</h6>
                                            <a href="{{ route('customer.cars.index') }}" class="btn btn-sm btn-outline-secondary mt-2">
                                                Change Car
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="start_date" class="form-label">Pickup Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('start_date') is-invalid @enderror" id="start_date" name="start_date" 
                                       value="{{ old('start_date', isset($start_date) ? $start_date : '') }}"
                                       min="{{ date('Y-m-d') }}" required>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="end_date" class="form-label">Return Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('end_date') is-invalid @enderror" id="end_date" name="end_date" 
                                       value="{{ old('end_date', isset($end_date) ? $end_date : '') }}"
                                       min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="pickup_location" class="form-label">Pickup Location <span class="text-danger">*</span></label>
                                <select class="form-select @error('pickup_location') is-invalid @enderror" id="pickup_location" name="pickup_location" required>
                                    <option value="office" {{ old('pickup_location') == 'office' ? 'selected' : '' }}>Main Office</option>
                                    <option value="airport" {{ old('pickup_location') == 'airport' ? 'selected' : '' }}>Airport</option>
                                    <option value="hotel" {{ old('pickup_location') == 'hotel' ? 'selected' : '' }}>Hotel</option>
                                    <option value="custom" {{ old('pickup_location') == 'custom' ? 'selected' : '' }}>Custom Address</option>
                                </select>
                                @error('pickup_location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="return_location" class="form-label">Return Location <span class="text-danger">*</span></label>
                                <select class="form-select @error('return_location') is-invalid @enderror" id="return_location" name="return_location" required>
                                    <option value="office" {{ old('return_location') == 'office' ? 'selected' : '' }}>Main Office</option>
                                    <option value="airport" {{ old('return_location') == 'airport' ? 'selected' : '' }}>Airport</option>
                                    <option value="hotel" {{ old('return_location') == 'hotel' ? 'selected' : '' }}>Hotel</option>
                                    <option value="custom" {{ old('return_location') == 'custom' ? 'selected' : '' }}>Custom Address</option>
                                </select>
                                @error('return_location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div id="custom_pickup_container" class="mb-3 {{ old('pickup_location') == 'custom' ? '' : 'd-none' }}">
                            <label for="custom_pickup_address" class="form-label">Custom Pickup Address</label>
                            <textarea class="form-control" id="custom_pickup_address" name="custom_pickup_address" rows="2">{{ old('custom_pickup_address') }}</textarea>
                        </div>

                        <div id="custom_return_container" class="mb-3 {{ old('return_location') == 'custom' ? '' : 'd-none' }}">
                            <label for="custom_return_address" class="form-label">Custom Return Address</label>
                            <textarea class="form-control" id="custom_return_address" name="custom_return_address" rows="2">{{ old('custom_return_address') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Additional Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                            <div class="form-text">Optional: Add any special requests or information here</div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Driver Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="driver_is_renter" name="driver_is_renter" value="1" {{ old('driver_is_renter', '1') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label" for="driver_is_renter">
                                    I will be the driver
                                </label>
                            </div>
                        </div>

                        <div id="additional_driver_container" class="{{ old('driver_is_renter', '1') == '1' ? 'd-none' : '' }}">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="driver_name" class="form-label">Driver's Name</label>
                                    <input type="text" class="form-control" id="driver_name" name="driver_name" value="{{ old('driver_name') }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="driver_phone" class="form-label">Driver's Phone</label>
                                    <input type="text" class="form-control" id="driver_phone" name="driver_phone" value="{{ old('driver_phone') }}">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="driver_license" class="form-label">Driver's License Number</label>
                                <input type="text" class="form-control" id="driver_license" name="driver_license" value="{{ old('driver_license') }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Terms and Conditions</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="terms-box p-3 bg-light mb-3" style="max-height: 200px; overflow-y: auto;">
                                <h6>Rental Agreement Terms</h6>
                                <p>1. The renter must present a valid driver's license and credit card at the time of pickup.</p>
                                <p>2. The renter must be at least 21 years of age.</p>
                                <p>3. The vehicle must be returned with the same amount of fuel as when it was picked up.</p>
                                <p>4. No smoking is allowed in the vehicle.</p>
                                <p>5. The renter is responsible for any damages to the vehicle during the rental period.</p>
                                <p>6. Early returns do not qualify for refunds.</p>
                                <p>7. Late returns will incur additional charges.</p>
                                <p>8. Full payment is required at the time of booking.</p>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input @error('agree_terms') is-invalid @enderror" type="checkbox" id="agree_terms" name="agree_terms" value="1" required {{ old('agree_terms') ? 'checked' : '' }}>
                                <label class="form-check-label" for="agree_terms">
                                    I have read and agree to the terms and conditions
                                </label>
                                @error('agree_terms')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm mb-4 booking-summary">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Booking Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3" id="summary_car">
                            <p class="mb-1 text-muted">Car:</p>
                            <h6 id="summary_car_name">{{ isset($car) ? $car->brand . ' ' . $car->model . ' (' . $car->year . ')' : 'Please select a car' }}</h6>
                        </div>
                        <hr>
                        <div class="mb-3 row">
                            <div class="col-6">
                                <p class="mb-1 text-muted">Pickup Date:</p>
                                <h6 id="summary_start_date">{{ old('start_date', isset($start_date) ? $start_date : '---') }}</h6>
                            </div>
                            <div class="col-6">
                                <p class="mb-1 text-muted">Return Date:</p>
                                <h6 id="summary_end_date">{{ old('end_date', isset($end_date) ? $end_date : '---') }}</h6>
                            </div>
                        </div>
                        <div class="mb-3">
                            <p class="mb-1 text-muted">Duration:</p>
                            <h6 id="summary_duration">
                                @if(old('start_date') && old('end_date'))
                                    {{ \Carbon\Carbon::parse(old('start_date'))->diffInDays(\Carbon\Carbon::parse(old('end_date'))) }} days
                                @elseif(isset($start_date) && isset($end_date))
                                    {{ \Carbon\Carbon::parse($start_date)->diffInDays(\Carbon\Carbon::parse($end_date)) }} days
                                @else
                                    ---
                                @endif
                            </h6>
                        </div>
                        <hr>
                        <div class="mb-3">
                            <p class="mb-1 text-muted">Pickup Location:</p>
                            <h6 id="summary_pickup">{{ old('pickup_location') ? ucfirst(old('pickup_location')) : 'Main Office' }}</h6>
                        </div>
                        <div class="mb-3">
                            <p class="mb-1 text-muted">Return Location:</p>
                            <h6 id="summary_return">{{ old('return_location') ? ucfirst(old('return_location')) : 'Main Office' }}</h6>
                        </div>
                        <hr>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span>Daily Rate:</span>
                                <span id="daily_rate">${{ isset($car) ? number_format($car->price_per_day, 2) : '0.00' }}</span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span>Number of Days:</span>
                                <span id="num_days">
                                    @if(old('start_date') && old('end_date'))
                                        {{ \Carbon\Carbon::parse(old('start_date'))->diffInDays(\Carbon\Carbon::parse(old('end_date'))) }}
                                    @elseif(isset($start_date) && isset($end_date))
                                        {{ \Carbon\Carbon::parse($start_date)->diffInDays(\Carbon\Carbon::parse($end_date)) }}
                                    @else
                                        0
                                    @endif
                                </span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span>Subtotal:</span>
                                <span id="subtotal">$0.00</span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span>Tax (10%):</span>
                                <span id="tax">$0.00</span>
                            </div>
                        </div>
                        <hr>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <strong>Total Amount:</strong>
                                <strong class="text-primary" id="total_amount">$0.00</strong>
                            </div>
                        </div>
                        <input type="hidden" name="total_price" id="total_price" value="{{ old('total_price', '0') }}">
                        <input type="hidden" name="total_days" id="total_days" value="{{ old('total_days', '0') }}">

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-calendar-check me-2"></i> Confirm Booking
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle pickup/return location changes
        const pickupLocationSelect = document.getElementById('pickup_location');
        const returnLocationSelect = document.getElementById('return_location');
        const customPickupContainer = document.getElementById('custom_pickup_container');
        const customReturnContainer = document.getElementById('custom_return_container');
        const summaryPickup = document.getElementById('summary_pickup');
        const summaryReturn = document.getElementById('summary_return');

        pickupLocationSelect.addEventListener('change', function() {
            if (this.value === 'custom') {
                customPickupContainer.classList.remove('d-none');
            } else {
                customPickupContainer.classList.add('d-none');
            }
            summaryPickup.textContent = this.options[this.selectedIndex].text;
        });

        returnLocationSelect.addEventListener('change', function() {
            if (this.value === 'custom') {
                customReturnContainer.classList.remove('d-none');
            } else {
                customReturnContainer.classList.add('d-none');
            }
            summaryReturn.textContent = this.options[this.selectedIndex].text;
        });

        // Handle driver is renter checkbox
        const driverIsRenterCheckbox = document.getElementById('driver_is_renter');
        const additionalDriverContainer = document.getElementById('additional_driver_container');

        driverIsRenterCheckbox.addEventListener('change', function() {
            if (this.checked) {
                additionalDriverContainer.classList.add('d-none');
            } else {
                additionalDriverContainer.classList.remove('d-none');
            }
        });

        // Calculate total price
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');
        const carSelect = document.getElementById('car_id');
        let dailyRate = {{ isset($car) ? $car->price_per_day : 0 }};
        
        function updateSummary() {
            let startDate = startDateInput.value ? new Date(startDateInput.value) : null;
            let endDate = endDateInput.value ? new Date(endDateInput.value) : null;
            
            document.getElementById('summary_start_date').textContent = startDateInput.value || '---';
            document.getElementById('summary_end_date').textContent = endDateInput.value || '---';
            
            if (startDate && endDate && startDate < endDate) {
                const diffTime = Math.abs(endDate - startDate);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                
                document.getElementById('summary_duration').textContent = diffDays + ' days';
                document.getElementById('num_days').textContent = diffDays;
                
                const subtotal = dailyRate * diffDays;
                const tax = subtotal * 0.1; // 10% tax
                const total = subtotal + tax;
                
                document.getElementById('subtotal').textContent = '$' + subtotal.toFixed(2);
                document.getElementById('tax').textContent = '$' + tax.toFixed(2);
                document.getElementById('total_amount').textContent = '$' + total.toFixed(2);
                document.getElementById('total_price').value = total.toFixed(2);
                document.getElementById('total_days').value = diffDays;
            } else {
                document.getElementById('summary_duration').textContent = '---';
                document.getElementById('num_days').textContent = '0';
                document.getElementById('subtotal').textContent = '$0.00';
                document.getElementById('tax').textContent = '$0.00';
                document.getElementById('total_amount').textContent = '$0.00';
                document.getElementById('total_price').value = '0';
                document.getElementById('total_days').value = '0';
            }
        }

        startDateInput.addEventListener('change', function() {
            // Set minimum end date to start date + 1 day
            if (this.value) {
                const nextDay = new Date(this.value);
                nextDay.setDate(nextDay.getDate() + 1);
                const formattedDate = nextDay.toISOString().split('T')[0];
                endDateInput.min = formattedDate;
                
                // If end date is now invalid, clear it
                if (endDateInput.value && new Date(endDateInput.value) <= new Date(this.value)) {
                    endDateInput.value = formattedDate;
                }
            }
            updateSummary();
        });

        endDateInput.addEventListener('change', updateSummary);

        // Update car selection
        if (carSelect) {
            carSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (this.value) {
                    document.getElementById('summary_car_name').textContent = selectedOption.text;
                    // Extract daily rate from option text (assuming format: "Brand Model (Year) - $XXX.XX/day")
                    const priceMatch = selectedOption.text.match(/\$([0-9]+\.[0-9]+)\/day/);
                    if (priceMatch && priceMatch[1]) {
                        dailyRate = parseFloat(priceMatch[1]);
                        document.getElementById('daily_rate').textContent = '$' + dailyRate.toFixed(2);
                        updateSummary();
                    }
                } else {
                    document.getElementById('summary_car_name').textContent = 'Please select a car';
                    document.getElementById('daily_rate').textContent = '$0.00';
                    dailyRate = 0;
                    updateSummary();
                }
            });
        }

        // Initial update
        updateSummary();
    });
</script>
@endsection

@section('styles')
<style>
    .booking-summary {
        position: sticky;
        top: 20px;
    }
    
    @media (max-width: 991.98px) {
        .booking-summary {
            position: static;
        }
    }
</style>
@endsection
