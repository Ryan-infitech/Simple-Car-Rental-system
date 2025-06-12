@extends('layouts.app')

@section('content')
<div class="hero-section position-relative">
    <div class="hero-image">
        <img src="{{ asset('images/hero-car-rental.webp') }}" alt="Car Rental Hero" class="w-100">
        <div class="overlay"></div>
    </div>
    <div class="container hero-content">
        <div class="row">
            <div class="col-lg-8 col-md-10">
                <h1 class="text-white display-4 fw-bold">Temukan Kendaraan Sempurna Anda</h1>
                <p class="text-white lead">Pilih dari berbagai pilihan kendaraan berkualitas untuk setiap kesempatan.</p>
                <a href="{{ route('cars.index') }}" class="btn btn-primary btn-lg mt-3">Lihat Mobil</a>
            </div>
        </div>
    </div>
</div>

<div class="container py-5">
    <!-- Featured Cars -->
    <div class="row mb-5">
        <div class="col-md-12 text-center mb-4">
            <h2>Kendaraan Unggulan</h2>
            <p class="lead text-muted">Jelajahi mobil-mobil terbaik kami yang tersedia untuk disewa</p>
        </div>
        
        @foreach($featuredCars as $car)
            <div class="col-lg-4 col-md-6 mb-4">
                @include('components.car-card', ['car' => $car])
            </div>
        @endforeach
        
        <div class="col-12 text-center mt-4">
            <a href="{{ route('cars.index') }}" class="btn btn-outline-primary">Lihat Semua Mobil</a>
        </div>
    </div>

    <!-- How It Works -->
    <div class="how-it-works py-5">
        <div class="row">
            <div class="col-md-12 text-center mb-4">
                <h2>Cara Kerjanya</h2>
                <p class="lead text-muted">Sewa mobil hanya dengan 3 langkah mudah</p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 mb-4 mb-md-0">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="step-number">1</div>
                        <h3 class="h4 mt-4">Pilih Mobil Anda</h3>
                        <p class="text-muted">Jelajahi armada kami yang luas dan temukan mobil yang sempurna untuk kebutuhan Anda.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4 mb-md-0">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="step-number">2</div>
                        <h3 class="h4 mt-4">Buat Pesanan</h3>
                        <p class="text-muted">Pilih tanggal pengambilan dan pengembalian lalu selesaikan reservasi.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="step-number">3</div>
                        <h3 class="h4 mt-4">Nikmati Perjalanan Anda</h3>
                        <p class="text-muted">Ambil mobil Anda dan nikmati perjalanan dengan kendaraan berkualitas kami.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Testimonials -->
    <div class="testimonials py-5">
        <div class="row">
            <div class="col-md-12 text-center mb-4">
                <h2>Apa Kata Pelanggan Kami</h2>
                <p class="lead text-muted">Jangan hanya percaya pada kata-kata kami</p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 mb-4 mb-md-0">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex mb-3">
                            <div class="text-warning">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                        <p class="testimonial-text">"Layanan yang hebat dan mobil yang sangat baik. Seluruh proses berjalan lancar dan tanpa kesulitan. Sangat direkomendasikan!"</p>
                        <div class="d-flex align-items-center mt-3">
                            <div class="testimonial-name">
                                <h5 class="mb-0">John Smith</h5>
                                <p class="text-muted small mb-0">Eksekutif Bisnis</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4 mb-md-0">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex mb-3">
                            <div class="text-warning">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                        <p class="testimonial-text">"Saya telah menyewa dari beberapa perusahaan sebelumnya, tetapi yang ini menonjol. Mobil bersih, staf ramah, dan harga yang bagus!"</p>
                        <div class="d-flex align-items-center mt-3">
                            <div class="testimonial-name">
                                <h5 class="mb-0">Sarah Johnson</h5>
                                <p class="text-muted small mb-0">Blogger Perjalanan</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex mb-3">
                            <div class="text-warning">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                            </div>
                        </div>
                        <p class="testimonial-text">"Sistem pemesanan online sangat user-friendly dan mobilnya persis seperti yang diiklankan. Akan menggunakan lagi di perjalanan saya berikutnya!"</p>
                        <div class="d-flex align-items-center mt-3">
                            <div class="testimonial-name">
                                <h5 class="mb-0">Michael Brown</h5>
                                <p class="text-muted small mb-0">Wisatawan Keluarga</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Call to action -->
<div class="cta-section py-5 bg-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 mb-4 mb-lg-0">
                <h3>Siap untuk memesan mobil sempurna Anda?</h3>
                <p class="lead mb-0">Bergabunglah dengan ribuan pelanggan puas yang memilih layanan rental kami.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="{{ route('register') }}" class="btn btn-primary me-2">Daftar Sekarang</a>
                <a href="{{ route('cars.index') }}" class="btn btn-outline-primary">Lihat Mobil</a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .hero-section {
        height: 500px;
        overflow: hidden;
        margin-bottom: 40px;
    }
    
    .hero-image {
        height: 100%;
        position: relative;
    }
    
    .hero-image img {
        object-fit: cover;
        height: 100%;
    }
    
    .overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
    }
    
    .hero-content {
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        transform: translateY(-50%);
        z-index: 10;
    }
    
    .step-number {
        width: 50px;
        height: 50px;
        background-color: #007bff;
        color: white;
        font-size: 24px;
        font-weight: bold;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
    }
    
    .testimonial-text {
        position: relative;
        font-style: italic;
    }
</style>
@endsection
