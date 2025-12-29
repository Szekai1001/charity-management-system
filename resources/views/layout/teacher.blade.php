<!DOCTYPE html>
<html>

<head>
    <title>@yield('title', 'My App')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css' rel='stylesheet' />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        :root {
            --sidebar-width: 280px;
            --primary-bg: #f5f7fb;
            --navbar-height: 70px;
            /* Define a fixed height for stability */
        }

        body {
                    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: var(--primary-bg);
            overflow-x: hidden;
            /* Critical for preventing side-scroll */
        }

        /* --- Sidebar Styling --- */
        .sidebar-menu {
            width: var(--sidebar-width)!important;
            background: white;
            border-right: 1px solid #eee;
            position: fixed;
            /* Changed to fixed for better stability */
            top: 0;
            left: 0;
            height: 100vh;
            overflow-y: auto;
            z-index: 1045;
            transition: transform 0.3s ease-in-out;
        }

        /* Desktop: Sidebar is visible */
        @media (min-width: 992px) {
            .sidebar-menu {
                transform: translateX(0);
            }

            .main-wrapper {
                margin-left: var(--sidebar-width);
                /* Push content to the right */
                width: calc(100% - var(--sidebar-width));
            }
        }

        /* Mobile: Sidebar behavior handled by Bootstrap offcanvas classes, 
       but we ensure z-index is high */
        @media (max-width: 991.98px) {
            .sidebar-menu {
                z-index: 1050;
                max-width: 85vw !important;
                /* Above navbar */
            }

            .main-wrapper {
                margin-left: 0;
                width: 100%;
            }
        }

        .student-item {
            transition: background-color 0.2s ease-in-out;
            cursor: pointer;
        }

        .student-item:hover {
            background-color: #f8f9fa;
        }

        /* --- Navbar Styling --- */
        .sticky-navbar {
            position: sticky;
            top: 0;
            z-index: 1020;
            /* FIX: Make navbar white so scrolling content goes BEHIND it visibly */
            background-color: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            /* Modern glass effect */
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
        }

        /* --- Notification Dropdown Fix --- */
        .notification-dropdown {
            width: 380px;
        }

        /* FIX: Prevent dropdown from breaking mobile layout */
        @media(max-width: 450px) {
            .notification-dropdown {
                width: 80vw !important;
                /* Use 90% of viewport width instead of fixed pixels */
                max-width: 350px;
                /* Cap it so it doesn't get too big */
                right: -30px !important;
            }
        }
    </style>

</head>

<body>

    <!-- Sidebar -->
    <div class="d-flex min-vh-100">

        <div class="offcanvas-lg offcanvas-start sidebar-menu p-3 d-flex flex-column shadow-sm" tabindex="-1" id="sidebarMenu" style="height: 100vh; overflow-y: auto; z-index: 1045;"
            aria-labelledby="sidebarMenuLabel">

            <div class="offcanvas-header justify-content-end p-0 mb-3 d-lg-none">
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#sidebarMenu" aria-label="Close"></button>
            </div>

            <div class="d-flex align-items-center mb-5 mt-lg-2 px-2">
                <a href="#" class="d-flex align-items-center text-decoration-none text-dark">
                    <img src="{{ asset('image/logo.jpg') }}" alt="Logo" style="height: 45px; width: 45px; object-fit: cover;" class="rounded-circle me-2 shadow-sm border">
                    <span class="fs-4 fw-bold tracking-tight">PKKM</span>
                </a>
            </div>


            <div class="d-flex flex-column flex-grow-1 overflow-auto">
                <small class="text-uppercase text-muted fw-bold mb-2 ms-2 label" style="font-size: 0.75rem; letter-spacing: 1px;">Overview</small>

                <ul class="nav nav-pills flex-column gap-2">
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center {{ request()->is('teacher/dashboard') ? 'active' : 'text-dark' }}"
                            href="{{ route('teacher.dashboard') }}">
                            <i class="bi bi-grid-fill me-3 fs-5"></i>
                            <span class="fs-6 fw-medium label">Dashboard</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center {{ request()->is('teacher/attendance/scanner') ? 'active' : 'text-dark' }}"
                            href="{{ route('attendance.scanner') }}">
                            <i class="bi bi-qr-code-scan me-3 fs-5"></i>
                            <span class="fs-6 fw-medium label">QR Scanner</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center {{ request()->is('teacher/attendance/student*') ? 'active' : 'text-dark' }}"
                            href="{{ route('attendance.student.teacher') }}">
                            <i class="bi bi-mortarboard me-3 fs-5"></i>
                            <span class="fs-6 fw-medium label">Student Attendances</span>
                        </a>
                    </li>

                    <li class="nav-item mt-3 mb-1">
                        <small class="text-uppercase text-muted fw-bold ms-2 label" style="font-size: 0.75rem; letter-spacing: 1px;">Personal</small>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center {{ request()->is('attendance/teacher') ? 'active' : 'text-dark' }}"
                            href="{{ route('attendance.teacher') }}">
                            <i class="bi bi-calendar-check me-3 fs-5"></i>
                            <span class="fs-6 fw-medium label">My Attendance</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center {{ request()->is('teacher/salary') ? 'active' : 'text-dark' }}"
                            href="{{ route('teacher.salaryView') }}">
                            <i class="bi bi-wallet2 me-3 fs-5"></i>
                            <span class="fs-6 fw-medium label">My Salary</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center {{ request()->is('teacher/notification') ? 'active' : 'text-dark' }}"
                            href="{{ route('teacher.notification') }}">
                            <i class="bi bi-bell-fill me-3 fs-5"></i>
                            <span class="fs-6 fw-medium label">Notification</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center {{ request()->is('profile') ? 'active' : 'text-dark' }}"
                            href="{{ route('user.profile') }}">
                            <i class="bi bi-person-circle me-3 fs-5"></i>
                            <span class="fs-6 fw-medium label">My Profile</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="mt-auto border-top pt-3">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="nav-link w-100 d-flex align-items-center text-danger bg-danger-subtle rounded px-3 py-2 border-0" type="submit">
                        <i class="bi bi-box-arrow-right me-3 fs-5"></i>
                        <span class="fs-6 fw-bold label">Logout</span>
                    </button>
                </form>
            </div>
        </div>


        <div class="main-wrapper">

            <nav class="navbar px-3 px-lg-4 py-3 sticky-navbar mb-4">
                {{-- Added flex-nowrap to prevent ugly stacking on very small screens --}}
                <div class="container-fluid d-flex justify-content-between align-items-center w-100 flex-nowrap">

                    {{-- LEFT SIDE: Toggle + Title --}}
                    <div class="d-flex align-items-center gap-2 gap-lg-3 overflow-hidden">

                        {{-- Mobile Toggle Button --}}
                        <button class="btn d-lg-none flex-shrink-0 px-0" type="button"
                            data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu">
                            <i class="bi bi-list fs-4"></i>
                        </button>

                        {{-- Title Section --}}
                        <div class="d-flex flex-column text-truncate">
                            {{-- Responsive Font Size: fs-5 (smaller) on mobile, fs-3 (larger) on desktop --}}
                            <h3 class="fw-bold mb-0 fs-5 fs-lg-2 text-truncate">
                                @switch(Route::currentRouteName())
                                @case('teacher.dashboard') Dashboard @break
                                @case('attendance.scanner') QR Scanner @break {{-- Shortened for mobile --}}
                                @case('attendance.student.teacher') Student Attendance @break
                                @case('attendance.teacher') My Attendance @break
                                @case('teacher.salaryView') My Salary @break
                                @case('teacher.notification') Notifications @break
                                @default My Profile
                                @endswitch
                            </h3>
                            {{-- Subtitle: Hidden on mobile (d-none), visible on tablet+ (d-md-block) --}}
                            <p class="text-muted mb-0 small d-none d-md-block text-truncate">
                                 @switch(Route::currentRouteName())
                                @case('teacher.dashboard')  @break
                                @case('attendance.scanner') Scan the QR code to mark attenances. @break {{-- Shortened for mobile --}}
                                @case('attendance.student.teacher') Start and View Student Attendance Records. @break
                                @case('attendance.teacher') View My Attendance Records. @break
                                @case('teacher.salaryView') View My Salary Records. @break
                                @case('teacher.notification') View My Notification Records. @break
                                @default View and Update My Profile.
                                @endswitch
                            </p>
                        </div>
                    </div>

                    @php
                    $user = Auth::user();
                    $profile = $user->teacher;
                    @endphp

                    {{-- RIGHT SIDE: Notifications + Profile --}}
                    {{-- Reduced gap on mobile (gap-2), larger on desktop (gap-4) --}}
                    <div class="d-flex align-items-center gap-2 gap-md-4 flex-shrink-0">

                        {{-- Notification Dropdown --}}
                        <div class="dropdown">
                            <button class="btn btn-light bg-primary bg-opacity-10 position-relative rounded-circle border-0 d-flex align-items-center justify-content-center"
                                type="button" data-bs-toggle="dropdown" aria-expanded="false" style="width: 45px; height: 45px;">
                                <i class="bi bi-bell-fill text-primary fs-5"></i>
                                @if(isset($unreadCount) && $unreadCount > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-circle bg-danger border border-2 border-white p-1" style="font-size: 0.6rem;">
                                    {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                                </span>
                                @endif
                            </button>

                            {{-- FIX APPLIED HERE: max-width + negative margin --}}
                            <div class="dropdown-menu dropdown-menu-end shadow border-0 mt-3 p-0 rounded-4"
                                style="width: 320px; max-width: 70vw; margin-right: -3rem;">

                                <div class="px-4 py-3 border-bottom d-flex justify-content-between align-items-center bg-light rounded-top-4">
                                    <span class="fw-bold text-dark">Notifications</span>
                                    @if(isset($unreadCount) && $unreadCount > 0)
                                    <a href="#" class="text-decoration-none small fw-bold text-primary">Read all</a>
                                    @endif
                                </div>

                                <div class="list-group list-group-flush" style="max-height: 60vh; overflow-y: auto;">
                                    @forelse($activities as $activity)
                                    <a href="#" class="list-group-item list-group-item-action border-0 py-3 px-4">
                                        <div class="d-flex gap-3">
                                            <div class="mt-1"><i class="bi bi-circle-fill text-primary" style="font-size: 8px;"></i></div>
                                            <div class="text-break">
                                                <p class="mb-1 text-dark fw-medium small">{{ $activity->message }}</p>
                                                <span class="text-muted" style="font-size: 0.75rem;">{{ $activity->created_at->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                    </a>
                                    @empty
                                    <div class="text-center py-4">
                                        <p class="text-muted small mb-0">No new notifications</p>
                                    </div>
                                    @endforelse
                                </div>

                                <div class="p-2 text-center border-top bg-light rounded-bottom-4">
                                    <a href="{{ route('teacher.notification') }}" class="text-decoration-none small fw-bold text-primary">View All</a>
                                </div>
                            </div>
                        </div>

                        {{-- Profile Dropdown --}}
                        <div class="dropdown">
                            <a href="#" class="d-flex align-items-center text-decoration-none text-dark gap-2 gap-md-3"
                                data-bs-toggle="dropdown" aria-expanded="false">

                                {{-- Profile Image: Smaller on mobile (40px), Standard on Desktop (50px) --}}
                                <img src="{{ $profile->avatar ? asset('storage/' . $profile->avatar) : asset('image/boy.png') }}"
                                    class="rounded-circle border shadow-sm object-fit-cover"
                                    style="width: 40px; height: 40px;"
                                    alt="Profile">

                                {{-- Name & Role: Hidden on mobile/tablet, visible on large screens --}}
                                <div class="d-none d-lg-flex flex-column text-start">
                                    <span class="fw-bold fs-6 text-dark">{{ auth()->user()->name }}</span>
                                    <span class="text-muted small">Teacher</span>
                                </div>

                                {{-- Chevron: Hidden on mobile to save space --}}
                                <div class="bg-light rounded-circle d-none d-md-flex align-items-center justify-content-center border"
                                    style="width: 32px; height: 32px;">
                                    <i class="bi bi-chevron-down text-dark fw-bold" style="font-size: 14px;"></i>
                                </div>
                            </a>

                            <ul class="dropdown-menu dropdown-menu-end shadow-lg border mt-3 p-0 rounded-4"
                                style="min-width: 200px;">
                                <li><a class="dropdown-item py-2" href="{{ route('user.profile') }}"><i class="bi bi-person me-2"></i> Profile</a></li>
                                <li>
                                    <hr class="dropdown-divider my-0">
                                </li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button class="dropdown-item py-2 text-danger" type="submit"><i class="bi bi-box-arrow-right me-2"></i> Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>

                </div>
            </nav>

            <!-- Content Section -->
            <div class="main-content px-3 px-lg-5 pb-5">
                @yield('content')
            </div>
        </div>
    </div>

    @vite('resources/js/attendance.js')

</body>

</html>