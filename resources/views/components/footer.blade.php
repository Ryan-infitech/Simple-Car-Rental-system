<footer class="bg-dark text-light py-5 mt-auto">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 mb-4 mb-lg-0">
                <img src="{{ asset('images/icon.jpeg') }}" alt="Car Rental Logo" height="40" class="mb-3"> Rental Oto
                <p class="mb-3">Mitra terpercaya Anda untuk penyewaan mobil. Kami menyediakan kendaraan berkualitas dengan harga terjangkau.</p>
                <div class="social-links">
                    <a href="#" class="text-light me-3"><i class="fab fa-facebook-f fa-lg"></i></a>
                    <a href="#" class="text-light me-3"><i class="fab fa-twitter fa-lg"></i></a>
                    <a href="#" class="text-light me-3"><i class="fab fa-instagram fa-lg"></i></a>
                    <a href="#" class="text-light"><i class="fab fa-youtube fa-lg"></i></a>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 mb-4 mb-md-0">
                <h5 class="text-uppercase mb-4">Quick Links</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="{{ route('home') }}" class="text-light text-decoration-none">Home</a></li>
                    <li class="mb-2"><a href="{{ route('cars.index') }}" class="text-light text-decoration-none">Cars</a></li>
                    <li class="mb-2"><a href="#" class="text-light text-decoration-none">About Us</a></li>
                    <li class="mb-2"><a href="#" class="text-light text-decoration-none">Contact</a></li>
                    <li class="mb-2"><a href="#" class="text-light text-decoration-none">Terms & Conditions</a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-4 mb-4 mb-md-0">
                <h5 class="text-uppercase mb-4">Customer Service</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="#" class="text-light text-decoration-none">FAQ</a></li>
                    <li class="mb-2"><a href="#" class="text-light text-decoration-none">How to Book</a></li>
                    <li class="mb-2"><a href="#" class="text-light text-decoration-none">Payment Methods</a></li>
                    <li class="mb-2"><a href="#" class="text-light text-decoration-none">Cancellation Policy</a></li>
                    <li class="mb-2"><a href="#" class="text-light text-decoration-none">Support</a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-4">
                <h5 class="text-uppercase mb-4">Contact Info</h5>
                <ul class="list-unstyled contact-info">
                    <li class="mb-2">
                        <i class="fas fa-map-marker-alt me-2"></i> jl.Soekarno , Padang, Indonesia
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-phone-alt me-2"></i> +123 456 7890
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-envelope me-2"></i> info@RentalOto.com
                    </li>
                    <li>
                        <i class="fas fa-clock me-2"></i> Mon-Sat: 09:00 AM - 06:00 PM
                    </li>
                </ul>
            </div>
        </div>
        <hr class="my-4 bg-light opacity-25">
        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start">
                <p class="mb-0">&copy; {{ date('Y') }} Rental Oto. All rights reserved.</p>
            </div>
            <div class="col-md-6 text-center text-md-end mt-3 mt-md-0">
                <div class="payment-methods">
                    <i class="fab fa-cc-visa fa-lg mx-1"></i>
                    <i class="fab fa-cc-mastercard fa-lg mx-1"></i>
                    <i class="fab fa-cc-paypal fa-lg mx-1"></i>
                    <i class="fab fa-cc-amex fa-lg mx-1"></i>
                </div>
            </div>
        </div>
    </div>
</footer>
