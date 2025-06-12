<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">
            <img src="{{ asset('images/icon.png') }}" alt="Car Rental Logo" height="40"> Rental Oto
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('cars.index') || request()->routeIs('cars.show') ? 'active' : '' }}" href="{{ route('cars.index') }}">Cars</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">About Us</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Contact</a>
                </li>
            </ul>
            
            <ul class="navbar-nav ms-auto">
                @guest
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('login') ? 'active' : '' }}" href="{{ route('login') }}">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('register') ? 'active' : '' }}" href="{{ route('register') }}">Register</a>
                    </li>
                @else
                    @if(auth()->user()->isAdmin())
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.dashboard') }}">
                                <i class="fas fa-tachometer-alt me-1"></i> Admin Panel
                            </a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link position-relative {{ request()->routeIs('customer.notifications.*') ? 'active' : '' }}" href="{{ route('customer.notifications.index') }}">
                                <i class="fas fa-bell"></i>
                                @if(auth()->user()->unreadNotifications()->count() > 0)
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                        {{ auth()->user()->unreadNotifications()->count() }}
                                    </span>
                                @endif
                            </a>
                        </li>
                    @endif
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ auth()->user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            @if(!auth()->user()->isAdmin())
                                <li>
                                    <a class="dropdown-item" href="{{ route('customer.dashboard') }}">
                                        <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('customer.bookings.index') }}">
                                        <i class="fas fa-calendar-alt me-2"></i> My Bookings
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('customer.profile.edit') }}">
                                        <i class="fas fa-user-edit me-2"></i> My Profile
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                            @endif
                            <li>
                                <a class="dropdown-item" href="{{ route('logout') }}" 
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </li>
                        </ul>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>
