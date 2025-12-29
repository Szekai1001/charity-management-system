    <!DOCTYPE html>
    <html lang="en">

    <head>
        <title>@yield('title', 'Beneficiary Portal')</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        {{-- CSS Dependencies --}}
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=League+Spartan&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

        {{-- JS Dependencies --}}
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

        <style>
            :root {
                --sidebar-width: 280px;
                --primary-bg: rgb(245, 247, 251);
            }

            body {
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
                background-color: var(--primary-bg);
                /* Prevent horizontal scroll on mobile */
                overflow-x: hidden;
            }


            .sidebar-menu {
                width: var(--sidebar-width);
                background: white;
                border-right: 1px solid #eee;
                /* Sticky Logic */
                position: sticky;
                top: 0;
                height: 100vh;
                /* Full viewport height */
                overflow-y: auto;
                /* Internal scroll */
                z-index: 1045;
            }

            @media (max-width: 991.98px) {
                .sidebar-menu {
                    position: fixed;
                    /* Slide-out drawer on mobile */
                    width: 250px !important;
                    height: 100%;
                    z-index: 1050;
                }
            }

            .navbar-title {
                font-size: 1.1rem !important;
                /* Small enough for phones */
            }

            /* Tablet View (iPad) */
            @media (min-width: 768px) {
                .navbar-title {
                    font-size: 1.5rem !important;
                }
            }

            /* Web / Desktop View (Laptops and Monitors) */
            @media (min-width: 992px) {
                .navbar-title {
                    font-size: 1.85rem !important;
                    /* This will make it significantly larger */
                    letter-spacing: -0.02em;
                    /* Makes bold titles look professional */
                }
            }

            /* --- Notification Icon Sizing --- */
            .nav-icon-btn {
                width: 38px;
                height: 38px;
                transition: all 0.2s;
            }

            /* --- Profile Image Sizing --- */
            .profile-img-nav {
                width: 38px;
                height: 38px;
                transition: all 0.2s;
            }

            /* --- Web View Adjustments (Desktop) --- */
            @media (min-width: 992px) {
                .nav-icon-btn {
                    width: 48px !important;
                    height: 48px !important;
                }

                .profile-img-nav {
                    width: 48px !important;
                    height: 48px !important;
                }

                .bi-bell-fill {
                    font-size: 1.3rem !important;
                    /* Makes the bell icon larger on web */
                }
            }

            /* NAVIGATION LINKS STYLING */
            .nav-link {
                color: #555;
                transition: all 0.2s ease-in-out;
                padding: 12px 16px;
                border-radius: 12px;
                font-weight: 500;
            }

            /* Hover State */
            .nav-link:hover {
                background-color: #e7f1ff;
                color: #0d6efd;
            }

            /* Active State */
            .nav-link.active {
                background-color: #0d6efd !important;
                color: white !important;
                box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
            }

            /* Main Content Wrapper */
            .main-wrapper {
                /* Takes full width minus sidebar on desktop */
                flex: 1;
                min-width: 0;
                display: flex;
                flex-direction: column;
            }

            .sticky-navbar {
                position: sticky;
                top: 0;
                z-index: 1020;
                background: rgba(245, 247, 251, 0.95);
                /* Semi-transparent match to body */
                backdrop-filter: blur(8px);
                /* Blur effect */
            }

            /* Responsive Notification Dropdown */
            .notification-dropdown {
                width: 350px;
                /* Ideal for Web */
                max-width: 90vw;
                /* Prevents overflow on Mobile */
            }

            @media (max-width: 576px) {
                .notification-dropdown {
                    width: 300px;
                    position: absolute;
                    /* Centers the dropdown on small screens if necessary */
                    right: -50px !important;
                }
            }

            /* Custom scrollbar for better UI */
            .notification-list::-webkit-scrollbar {
                width: 4px;
            }

            .notification-list::-webkit-scrollbar-thumb {
                background: #e0e0e0;
                border-radius: 10px;
            }
        </style>
    </head>

    <body>

        <div class="d-flex min-vh-100">

            <div class="offcanvas-lg offcanvas-start sidebar-menu p-3 d-flex flex-column shadow-sm"
                tabindex="-1"
                id="sidebarMenu"
                style="height: 100vh; overflow-y: auto; z-index: 1045;"
                aria-labelledby="sidebarMenuLabel">

                {{-- Mobile Close Button Header --}}
                <div class="offcanvas-header justify-content-end p-0 mb-3 d-lg-none">
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#sidebarMenu" aria-label="Close"></button>
                </div>

                {{-- Logo --}}
                <div class="d-flex align-items-center mb-5 mt-lg-2 px-2">
                    <a href="#" class="d-flex align-items-center text-decoration-none text-dark">
                        <img src="{{ asset('image/logo.jpg') }}" onerror="this.src='https://via.placeholder.com/45'" alt="Logo" style="height: 45px; width: 45px; object-fit: cover;" class="rounded-circle me-2 shadow-sm border">
                        <span class="fs-4 fw-bold tracking-tight">PKKM</span>
                    </a>
                </div>

                {{-- Menu Items --}}
                <div class="d-flex flex-column flex-grow-1 overflow-y-auto">
                    <small class="text-uppercase text-muted fw-bold mb-3 ms-2" style="font-size: 0.75rem; letter-spacing: 1px;">Menu</small>

                    <ul class="nav nav-pills flex-column gap-2">
                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center {{ request()->routeIs('beneficiary.dashboard') ? 'active' : '' }}" href="{{ route('beneficiary.dashboard') }}">
                                <i class="bi bi-grid-fill me-3 fs-5"></i>
                                <span class="fs-6">Dashboard</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center {{ request()->routeIs('supplyRequest.create') ? 'active' : '' }}" href="{{ route('supplyRequest.create') }}">
                                <i class="bi bi-bag-heart-fill me-3 fs-5"></i>
                                <span class="fs-6">Apply for Supply</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center {{ request()->routeIs('beneficiary.viewPastApplication') ? 'active' : '' }}" href="{{ route('beneficiary.viewPastApplication') }}">
                                <i class="bi bi-clock-history me-3 fs-5"></i>
                                <span class="fs-6">History</span>
                            </a>
                        </li>

                        <li class="nav-item mt-4 mb-2">
                            <small class="text-uppercase text-muted fw-bold ms-2" style="font-size: 0.75rem; letter-spacing: 1px;">Account</small>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center {{ request()->routeIs('beneficiary.notification') ? 'active' : '' }}" href="{{ route('beneficiary.notification') }}">
                                <i class="bi bi-bell-fill me-3 fs-5"></i>
                                <span class="fs-6">Notifications</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center {{ request()->routeIs('user.profile') ? 'active' : '' }}" href="{{ route('user.profile') }}">
                                <i class="bi bi-person-circle me-3 fs-5"></i>
                                <span class="fs-6">My Profile</span>
                            </a>
                        </li>
                    </ul>
                </div>

                {{-- Logout --}}
                <div class="mt-auto border-top pt-3">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="nav-link w-100 d-flex align-items-center text-danger bg-danger-subtle rounded px-3 py-2 border-0" type="submit">
                            <i class="bi bi-box-arrow-right me-3 fs-5"></i>
                            <span class="fs-6 fw-bold">Logout</span>
                        </button>
                    </form>
                </div>
            </div>
            {{-- End Sidebar --}}


            {{-- MAIN CONTENT AREA --}}
            <div class="main-wrapper">

                {{-- Top Navbar --}}
                <nav class="navbar px-2 px-md-4 py-2 py-md-3 border-bottom d-flex align-items-center justify-content-between sticky-navbar flex-nowrap">

                    {{-- Left Side: Toggle (Mobile) + Responsive Titles --}}
                    <div class="d-flex align-items-center gap-2 gap-md-3 overflow-hidden">

                        {{-- Mobile Toggle Button --}}
                        <button class="btn btn-light border shadow-sm d-lg-none rounded-circle p-0 d-flex align-items-center justify-content-center flex-shrink-0"
                            type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu"
                            style="width: 40px; height: 40px;">
                            <i class="bi bi-list fs-5"></i>
                        </button>

                        <div class="d-flex flex-column overflow-hidden">
                            <h3 class="fw-bold mb-0 text-truncate navbar-title">
                                @switch(Route::currentRouteName())
                                @case('beneficiary.dashboard') Dashboard @break
                                @case('supplyRequest.create') Supply Application @break
                                @case('beneficiary.viewPastApplication') Supply Application History @break
                                @case('beneficiary.notification') Notifications @break
                                @case('user.profile') My Profile @break
                                @default Portal
                                @endswitch
                            </h3>
                            <p class="text-muted mb-0 d-none d-md-block small">
                                @switch(Route::currentRouteName())
                                @case('beneficiary.dashboard')Welcome back, {{ Auth::user()->name }} @break
                                @case('supplyRequest.create') Submit supply application @break
                                @case('beneficiary.viewPastApplication') Check application history @break
                                @case('beneficiary.notification') Receive notifications @break
                                @case('user.profile') Update profile details @break
                                @default Portal
                                @endswitch
                            </p>
                        </div>
                    </div>

                    @php
                    $user = Auth::user();
                    $profile = $user->beneficiary;
                    @endphp

                    {{-- Right Side: Profile & Notification --}}
                    <div class="d-flex align-items-center gap-2 gap-md-3 flex-shrink-0">

                        {{-- Notifications Dropdown --}}
                        <div class="dropdown">
                            <button class="btn btn-light bg-primary bg-opacity-10 position-relative rounded-circle border-0 d-flex align-items-center justify-content-center nav-icon-btn"
                                type="button" data-bs-toggle="dropdown" aria-expanded="false" style="width: 40px; height: 40px;">
                                <i class="bi bi-bell-fill text-primary"></i>
                                @if(isset($unreadCount) && $unreadCount > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-circle bg-danger border border-2 border-white p-1" style="font-size: 0.6rem;">
                                    {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                                </span>
                                @endif
                            </button>

                            <div class="dropdown-menu dropdown-menu-end shadow border-0 mt-3 p-0 rounded-4 notification-dropdown">
                                {{-- Header --}}
                                <div class="px-4 py-3 border-bottom d-flex justify-content-between align-items-center bg-light rounded-top-4">
                                    <span class="fw-bold text-dark">Notifications</span>
                                    @if(isset($unreadCount) && $unreadCount > 0)
                                    <a href="#" class="text-decoration-none small fw-bold text-primary">Mark all read</a>
                                    @endif
                                </div>

                                {{-- Body --}}
                                <div class="list-group list-group-flush notification-list" style="max-height: 400px; overflow-y: auto;">
                                    @forelse($activities ?? [] as $activity)
                                    <a href="#" class="list-group-item list-group-item-action border-0 py-3 px-4">
                                        <div class="d-flex gap-3">
                                            {{-- Status Indicator --}}
                                            <div class="mt-1">
                                                <i class="bi bi-circle-fill {{ $activity->read_at ? 'text-light' : 'text-primary' }}" style="font-size: 8px;"></i>
                                            </div>
                                            <div class="text-break">
                                                <p class="mb-1 text-dark {{ $activity->read_at ? 'fw-normal' : 'fw-bold' }} small" style="line-height: 1.4;">
                                                    {{ $activity->message }}
                                                </p>
                                                <div class="d-flex align-items-center text-muted" style="font-size: 0.7rem;">
                                                    <i class="bi bi-clock me-1"></i>
                                                    {{ $activity->created_at->diffForHumans() }}
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                    @empty
                                    <div class="text-center py-5">
                                        <i class="bi bi-bell-slash text-muted opacity-25" style="font-size: 2.5rem;"></i>
                                        <p class="text-muted small mt-2 mb-0">No new notifications</p>
                                    </div>
                                    @endforelse
                                </div>

                                {{-- Footer --}}
                                <div class="p-2 text-center border-top bg-light rounded-bottom-4">
                                    <a href="{{ route('beneficiary.notification') }}" class="text-decoration-none small fw-bold text-primary py-2 d-block">
                                        View All Notifications
                                    </a>
                                </div>
                            </div>
                        </div>

                        {{-- Profile Dropdown --}}
                        <div class="dropdown">
                            <a href="#" class="d-flex align-items-center text-decoration-none text-dark gap-2" data-bs-toggle="dropdown" aria-expanded="false">
                                <img src="{{ $profile->avatar ? asset('storage/' . $profile->avatar) : asset('image/boy.png') }}"
                                    class="rounded-circle border shadow-sm object-fit-cover profile-img-nav" alt="Profile">
                                <div class="d-none d-lg-block text-start" style="line-height: 1.2;">
                                    <span class="fw-bold d-block text-dark small">{{ auth()->user()->name }}</span>
                                    <span class="text-muted" style="font-size: 0.7rem;">Beneficiary</span>
                                </div>
                                <i class="bi bi-chevron-down text-muted small d-none d-sm-block"></i>
                            </a>

                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-3 p-2 rounded-4" style="min-width: 240px;">
                                <li class="px-3 py-2">
                                    <p class="mb-0 fw-bold text-dark text-truncate">{{ auth()->user()->name }}</p>
                                    <p class="mb-0 text-muted small text-truncate">{{ auth()->user()->email }}</p>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <a class="dropdown-item rounded-2" href="{{ route('user.profile') }}">
                                        <i class="bi bi-person me-2"></i> My Profile
                                    </a>
                                </li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item rounded-2 text-danger">
                                            <i class="bi bi-box-arrow-right me-2"></i> Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </nav>

                {{-- Page Content --}}
                <div class="container-fluid p-4">
                    @yield('content')
                </div>

            </div>
        </div>

    </body>

    </html>