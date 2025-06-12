@extends('layouts.customer')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Payment Checkout</h1>
            <p class="lead text-muted">Complete your booking by making a payment</p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('customer.bookings.show', $booking->id) }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> Back to Booking
            </a>
        </div>
    </div>

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row">
        <div class="col-lg-7 order-lg-2 mb-4">
            <!-- Booking Summary -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Booking Summary</h5>
                </div>
                <div class="card-body">
                    <div class="row booking-car-details align-items-center mb-4">
                        <div class="col-md-4 mb-3 mb-md-0">
                            <img src="{{ asset($booking->car->image_path) }}" alt="{{ $booking->car->brand }} {{ $booking->car->model }}" class="img-fluid rounded">
                        </div>
                        <div class="col-md-8">
                            <h5>{{ $booking->car->brand }} {{ $booking->car->model }}</h5>
                            <p class="mb-1 text-muted">{{ $booking->car->year }} • {{ ucfirst($booking->car->transmission) }} • {{ $booking->car->color }}</p>
                            <p class="mb-0">
                                <span class="badge bg-light text-dark me-2">
                                    <i class="fas fa-calendar-alt me-1"></i> {{ $booking->start_date->format('M d, Y') }} - {{ $booking->end_date->format('M d, Y') }}
                                </span>
                                <span class="badge bg-light text-dark">
                                    <i class="fas fa-clock me-1"></i> {{ $booking->start_date->diffInDays($booking->end_date) }} days
                                </span>
                            </p>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="mb-1 text-muted small">Pickup Location</p>
                            <p class="mb-0">{{ ucfirst($booking->pickup_location) }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1 text-muted small">Return Location</p>
                            <p class="mb-0">{{ ucfirst($booking->return_location) }}</p>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <!-- Pricing Breakdown -->
                    <div class="pricing-breakdown">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Base Rate ({{ $booking->start_date->diffInDays($booking->end_date) }} days x ${{ number_format($booking->car->price_per_day, 2) }})</span>
                            <span>${{ number_format($booking->car->price_per_day * $booking->start_date->diffInDays($booking->end_date), 2) }}</span>
                        </div>
                        
                        @if($booking->insurance_fee > 0)
                        <div class="d-flex justify-content-between mb-2">
                            <span>Insurance Fee</span>
                            <span>${{ number_format($booking->insurance_fee, 2) }}</span>
                        </div>
                        @endif
                        
                        @if($booking->additional_driver_fee > 0)
                        <div class="d-flex justify-content-between mb-2">
                            <span>Additional Driver Fee</span>
                            <span>${{ number_format($booking->additional_driver_fee, 2) }}</span>
                        </div>
                        @endif
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span>Taxes & Fees (10%)</span>
                            <span>${{ number_format($booking->tax_amount, 2) }}</span>
                        </div>
                        
                        @if($booking->discount_amount > 0)
                        <div class="d-flex justify-content-between mb-2 text-success">
                            <span>Discount</span>
                            <span>-${{ number_format($booking->discount_amount, 2) }}</span>
                        </div>
                        @endif
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between">
                            <strong>Total Amount</strong>
                            <strong class="text-primary">${{ number_format($booking->total_price, 2) }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cancellation Policy -->
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Cancellation Policy</h5>
                </div>
                <div class="card-body">
                    <ul class="policy-list">
                        <li>Free cancellation up to 48 hours before the pickup time</li>
                        <li>Cancellations made within 48 hours of the pickup time are subject to a penalty fee equivalent to 30% of the booking amount</li>
                        <li>No-shows will be charged the full booking amount</li>
                    </ul>
                    <div class="alert alert-info mb-0 mt-3">
                        <i class="fas fa-info-circle me-2"></i> By proceeding with the payment, you agree to our cancellation policy.
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5 order-lg-1">
            <!-- Payment Methods -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Payment Method</h5>
                </div>
                <div class="card-body">
                    <form id="paymentForm" action="{{ route('customer.payments.process', $booking->id) }}" method="POST">
                        @csrf
                        
                        <div class="mb-4">
                            <div class="form-check payment-option mb-3">
                                <input class="form-check-input" type="radio" name="payment_method" id="bank_transfer" value="bank_transfer" checked>
                                <label class="form-check-label w-100" for="bank_transfer">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="fw-bold">Bank Transfer</span>
                                            <p class="text-muted mb-0 small">Transfer to our bank account and upload the receipt</p>
                                        </div>
                                        <div class="payment-icons">
                                            <i class="fas fa-university fa-lg text-primary"></i>
                                        </div>
                                    </div>
                                </label>
                            </div>
                            
                            <div class="bank-options ms-4 mb-3" id="bankOptions">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="bank" id="bca" value="bca" checked>
                                    <label class="form-check-label" for="bca">
                                        BCA
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="bank" id="mandiri" value="mandiri">
                                    <label class="form-check-label" for="mandiri">
                                        Mandiri
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="bank" id="bni" value="bni">
                                    <label class="form-check-label" for="bni">
                                        BNI
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="bank" id="bri" value="bri">
                                    <label class="form-check-label" for="bri">
                                        BRI
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                            <label class="form-check-label" for="terms">
                                I agree to the <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">Terms and Conditions</a> and <a href="#" data-bs-toggle="modal" data-bs-target="#privacyModal">Privacy Policy</a>
                            </label>
                            @error('terms')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-credit-card me-2"></i> Pay Now
                            </button>
                            <a href="{{ route('customer.bookings.show', $booking->id) }}" class="btn btn-outline-secondary">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Payment Instructions -->
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Payment Instructions</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i> Please complete the payment within 24 hours to confirm your booking.
                    </div>
                    
                    <ol class="payment-steps">
                        <li>Select your preferred payment method</li>
                        <li>Click "Pay Now" to proceed</li>
                        <li>For bank transfer, you'll be shown our bank account details</li>
                        <li>Transfer the exact amount and upload your payment proof</li>
                        <li>Wait for our confirmation (usually within 1-2 hours during business hours)</li>
                    </ol>
                    
                    <div class="alert alert-info mb-0 mt-3">
                        <i class="fas fa-info-circle me-2"></i> Need help? Contact our support at <strong>support@carrentals.com</strong> or call <strong>(123) 456-7890</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Terms and Conditions Modal -->
<div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="termsModalLabel">Terms and Conditions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6>1. Booking and Payment</h6>
                <p>By confirming a booking, you agree to pay the full amount specified at the time of booking. Payment must be made within the specified timeframe to secure your reservation.</p>
                
                <h6>2. Cancellation Policy</h6>
                <p>Cancellations made more than 48 hours prior to the pickup time will receive a full refund. Cancellations within 48 hours of pickup will incur a fee of 30% of the total booking amount. No-shows will be charged the full booking amount.</p>
                
                <h6>3. Driver Requirements</h6>
                <p>Drivers must be at least 21 years old and possess a valid driver's license that has been held for at least one year. International customers must have an International Driving Permit.</p>
                
                <h6>4. Vehicle Usage</h6>
                <p>Vehicles must be used only for their intended purpose and within the agreed-upon areas. Off-road driving is strictly prohibited unless specifically permitted.</p>
                
                <h6>5. Damages and Liability</h6>
                <p>You are responsible for any damages to the vehicle during your rental period. Insurance coverage may apply as per your selected package.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">I Understand</button>
            </div>
        </div>
    </div>
</div>

<!-- Privacy Policy Modal -->
<div class="modal fade" id="privacyModal" tabindex="-1" aria-labelledby="privacyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="privacyModalLabel">Privacy Policy</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6>1. Information Collection</h6>
                <p>We collect personal information including your name, contact details, payment information, and driving history for the purpose of facilitating car rentals.</p>
                
                <h6>2. Information Usage</h6>
                <p>Your information is used to process bookings, verify identity, process payments, and communicate with you about your rental.</p>
                
                <h6>3. Data Security</h6>
                <p>We implement appropriate security measures to protect your personal information from unauthorized access, alteration, or disclosure.</p>
                
                <h6>4. Third-Party Sharing</h6>
                <p>We may share your information with payment processors, insurance providers, and legal authorities when required by law.</p>
                
                <h6>5. Your Rights</h6>
                <p>You have the right to access, correct, or delete your personal information. Please contact us to exercise these rights.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">I Understand</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .payment-option {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 12px;
        margin-bottom: 10px;
        transition: all 0.3s;
    }
    
    .payment-option:hover {
        border-color: #adb5bd;
        background-color: #f8f9fa;
    }
    
    .form-check-input:checked + .form-check-label .payment-option {
        border-color: #0d6efd;
        background-color: #f0f7ff;
    }
    
    .policy-list {
        padding-left: 20px;
    }
    
    .policy-list li {
        margin-bottom: 8px;
    }
    
    .payment-steps li {
        margin-bottom: 10px;
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const paymentMethodRadios = document.querySelectorAll('input[name="payment_method"]');
        
        paymentMethodRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'bank_transfer') {
                    document.getElementById('bankOptions').classList.remove('d-none');
                } else {
                    document.getElementById('bankOptions').classList.add('d-none');
                }
            });
        });
    });
</script>
@endsection
