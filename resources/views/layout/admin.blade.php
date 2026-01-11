<!DOCTYPE html>
<html>

<head>
    <title>@yield('title', 'My App')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @vite('resources/css/user.css')
</head>

<style>
    html,
    body {
        height: 100%;
        margin: 0;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        background-color: #f2f2f2;
    }

    .nav-item:hover {
        background-color: #d0e3ea;
        transition: background-color 0.3s ease-in-out;
    }


    .nav-link.active {
        background-color: #d0e3ea;
        transition: background-color 0.3s ease-in-out;
    }

    .soft-dark {
        color: #495057;
    }
</style>


<body>

    <!-- Sidebar -->
    <div id="sidebar" class="d-flex flex-column sidebar soft-dark p-3 position-fixed top-0 start-0 bg-white" style="font-size: 0.9rem;">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center">
                <img src="{{ asset('image/logo.jpg') }}" alt="Logo" style="height: 40px;" class="rounded-circle me-2">
                <h5 class="mb-0 fw-bold label">PKKM</h5>
            </div>
            <button class="btn sidebarToggle btn-sm" type="button">
                <i class="bi bi-arrow-bar-left"></i>
            </button>
        </div>
        <button class="btn d-none mt-5 arrow-expanded" type="button"><i class="bi bi-arrow-bar-right"></i></button>

        <hr class="text-dark mt-0 mb-3">

        <ul class="nav flex-column">
            <li class="nav-item mb-2">
                <a class="nav-link {{ request()->is('admin/dashboard') ? 'active' : '' }} soft-dark py-1" href="{{ route('admin.dashboard') }}">
                    <i class="bi bi-speedometer fw-bold me-2"></i>
                    <span class="label fw-semibold">Dashboard</span>
                </a>
            </li>

            <span class="small text-uppercase text-muted mt-2 mb-1 label" style="font-size: 0.75rem; letter-spacing: 1px;">Application</span>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('admin/form-control') ? 'active' : '' }} soft-dark py-1" href="{{ route('admin.formControl') }}">
                    <i class="bi bi-pencil-square me-2 fw-bold"></i>
                    <span class="label fw-semibold">Form Control</span>
                </a>
            </li>
            <li class="nav-item mb-2">
                <a class="nav-link {{ request()->is('admin/application') ? 'active' : '' }} soft-dark py-1" href="{{ route('application.index') }}">
                    <i class="bi bi-file-earmark-text-fill fw-bold me-2"></i>
                    <span class="label fw-semibold">Manage Application</span>
                </a>
            </li>

            <span class="small text-uppercase text-muted mt-2 mb-1 label" style="font-size: 0.75rem; letter-spacing: 1px;">Attendance & Salary</span>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('admin/attendance/student') ? 'active' : '' }} soft-dark py-1" href="{{ route('attendance.student') }}">
                    <i class="bi bi-people-fill fw-bold me-2"></i>
                    <span class="label fw-semibold">Student Attendance</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('attendance/teacher') ? 'active' : '' }} soft-dark py-1" href="{{ route('attendance.teacher') }}">
                    <i class="bi bi-person-badge-fill fw-bold me-2"></i>
                    <span class="label fw-semibold">Teacher Attendance</span>
                </a>
            </li>
            <li class="nav-item mb-2">
                <a class="nav-link {{ request()->is('admin/salary') ? 'active' : '' }} soft-dark py-1" href="{{ route('salary') }}">
                    <i class="bi bi-cash-stack fw-bold me-2"></i>
                    <span class="label fw-semibold">Manage Salary</span>
                </a>
            </li>

            <span class="small text-uppercase text-muted mt-2 mb-1c label" style="font-size: 0.75rem; letter-spacing: 1px;">Monthly Supply</span>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('admin/packages/supplyForm') ? 'active' : '' }} soft-dark py-1" href="{{ route('packages.index') }}">
                    <i class="bi bi-box-seam-fill fw-bold me-2"></i>
                    <span class="label fw-semibold">Set Package Form</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('admin/packages/manage') ? 'active' : '' }} soft-dark py-1" href="{{ route('admin.packages') }}">
                    <i class="bi bi-box-seam-fill fw-bold me-2"></i>
                    <span class="label fw-semibold">Manage Packages</span>
                </a>
            </li>
            <li class="nav-item mb-2">
                <a class="nav-link {{ request()->is('admin/supplyView') ? 'active' : '' }} soft-dark py-1" href="{{ route('supplyRequest.show') }}">
                    <i class="bi bi-bag-check-fill fw-bold me-2"></i>
                    <span class="label fw-semibold">View Requests</span>
                </a>
            </li>

            <span class="small text-uppercase text-muted mt-2 mb-1 label" style="font-size: 0.75rem; letter-spacing: 1px;">User Management</span>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('admin/form/teacherForm') ? 'active' : '' }} soft-dark py-1" href="{{ route('teacher.form') }}">
                    <i class="bi bi-person-workspace fw-bold me-2"></i>
                    <span class="label fw-semibold">Manage Teachers</span>
                </a>
            </li>
            <li class="nav-item mb-2">
                <a class="nav-link {{ request()->is('admin/users') ? 'active' : '' }} soft-dark py-1" href="{{ route('users.index') }}">
                    <i class="bi bi-person-fill fw-bold me-2"></i>
                    <span class="label fw-semibold">Manage Users</span>
                </a>
            </li>

            <span class="small text-uppercase text-muted mt-2 mb-1 label" style="font-size: 0.75rem; letter-spacing: 1px;">Reporting</span>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('admin/attendanceInsights') ? 'active' : '' }} soft-dark py-1" href="{{ route('attendance.insights') }}">
                    <i class="bi-person-check-fill fw-bold me-2"></i>
                    <span class="label fw-semibold">Attendance Insights</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('admin/supplyRequestInsights') ? 'active' : '' }} soft-dark py-1" href="{{ route('supplyRequest.insights') }}">
                    <i class="bi bi-box-seam-fill fw-bold me-2"></i>
                    <span class="label fw-semibold">Supply Insights</span>
                </a>
            </li>
        </ul>
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


    <nav class="navbar bg-body-tertiary" id="navbar">
        <div class="container-fluid d-flex justify-content-between align-items-center">

            <div class="d-flex flex-column">

                <!-- Left side (Dashboard title) -->
                <h3 class="fw-bold">    
                    @switch(Route::currentRouteName())
                    @case('admin.formControl')
                    Application Form Control
                    @break
                    @case('application.index')
                    Manage Application
                    @break
                    @case('attendance.student')
                    Student Attendance
                    @break
                    @case('attendance.teacher')
                    Teacher Attendance
                    @break
                    @case('salary')
                    Manage Salary
                    @break
                    @case('packages.index')
                    Supply Request Form
                    @break
                    @case('admin.packages')
                    Manage Packages
                    @break
                    @case('supplyRequest.show')
                    View Supply Request
                    @break
                    @case('attendance.insights')
                    @case('attendance.filter')
                    View Attendance Insights
                    @break
                    @case('supplyRequest.insights')
                    @case('supplyRequestReporting.filter')
                    View Supply Request Insights
                    @break
                    @case('teacher.form')
                    Manage Teachers
                    @break
                    @case('users.index')
                    Manage users
                    @break
                    @default
                    @endswitch
                </h3>

                @switch(Route::currentRouteName())
                @case('admin.formControl')
                <p class="text-muted mb-0">
                    Manage and monitor application from availability and submissions.
                </p>
                @break
                @case('application.index')
                <p class="text-muted mb-0">Review and process student and beneficiary applications</p>
                @break
                @case('attendance.student')
                <p class="text-muted mb-0">Track and manage student attendance records.</p>
                @break
                @case('attendance.teacher')
                <p class="text-muted mb-0">Track and manage teacher attendance records.</p>
                @break
                @case('salary')
                <p class="text-muted mb-0">Manage employee hourly rates and calculate salaries.</p>
                @break
                @case('packages.index')
                <p class="text-muted mb-0">Manage packages, delivery dates, and sessions</p>
                @break
                @case('admin.packages')
                <p class="text-muted mb-0">Manage packages and their items with full add and edit capabilities.</p>
                @break
                @case('supplyRequest.show')
                <p class="text-muted mb-0">View supply requests from beneficiaries and track items pending purchase.</p>
                @break
                @case('attendance.insights')
                @case('attendance.filter')
                <p class="text-muted mb-0">View detailed attendance insights across different months and years for better tracking.</p>
                @break
                @case('supplyRequest.insights')
                @case('supplyRequestReporting.filter')
                <p class="text-muted mb-0">View detailed supply request insights across different months and years for better tracking.</p>
                @break
                @case('teacher.form')
                <p class="text-muted mb-0">Add Teacher Information and Create Profile.</p>
                @break
                @case('users.index')
                <p class="text-muted mb-0">View all user records—students, beneficiaries, and teachers—with options to view and delete.</p>
                @break
                @default
                @endswitch
            </div>

            <!-- Right side (User info + Logout) -->
            <div class="d-flex align-items-center">
                <!-- User Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor"
                    class="bi bi-person-circle me-2" viewBox="0 0 16 16">
                    <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0" />
                    <path fill-rule="evenodd"
                        d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 
                    0 0 0-5.468 11.37C3.242 11.226 
                    4.805 10 8 10s4.757 1.225 5.468 
                    2.37A7 7 0 0 0 8 1" />
                </svg>

                <!-- Check if a user is logged in -->
                @if(auth()->check())
                <div class="user-info text-start me-3">
                    <p class="mb-0 fw-bold">{{ auth()->user()->name }}</p>
                    <small class="text-muted">{{ auth()->user()->email }}</small>
                </div>
                @endif

                <!-- Logout Button -->
                <form method="POST" action="{{ route('logout') }}" class="mb-0">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger">Logout</button>
                </form>
            </div>
        </div>
    </nav>


    <!-- Main Content -->
    <div class="main-content">
        <div class="p-3 me-5">
            @yield('content')
        </div>
    </div>

</body>

@vite('resources/js/userManagement.js')

</html>