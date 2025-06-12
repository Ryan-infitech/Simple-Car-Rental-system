{{-- filepath: c:\Users\ryans\Documents\PROJECT DEVELOP\A Github\car-rental-system\resources\views\layouts\customer.blade.php --}}
@extends('layouts.app')

@section('title', 'Customer Dashboard - Car Rental System')

@section('content')
    @include('components.navbar')
    
    <main class="flex-grow">
        @yield('content')
    </main>
    
    @include('components.footer')
@endsection

@push('styles')
<style>
    .customer-nav {
        background-color: #f8f9fa;
    }
    
    .customer-nav .nav-link {
        color: #495057;
    }
    
    .customer-nav .nav-link.active {
        color: #007bff;
        font-weight: 500;
    }
    
    .customer-nav .dropdown-menu {
        border-radius: 0.5rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
</style>
@endpush