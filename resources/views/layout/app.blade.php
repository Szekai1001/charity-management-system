<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Home')</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        :root {
            --theme-pink: rgb(254, 126, 122);
            --theme-pink-hover: rgb(234, 106, 102);
            --theme-yellow: #ffc107;
            --text-dark: #333;
        }

        html,
        body {
            height: 100%;
            margin: 0;
            font-family: 'League Spartan', sans-serif;
            scroll-behavior: smooth;
            background-color: #f8f9fa;
            overflow-x: hidden;
            width: 100%;
        }

        body {
            display: flex;
            flex-direction: column;
            /* MOBILE DEFAULT: Pushes content down so Navbar doesn't cover it */
            padding-top: 70px;
        }

        /* --- Navbar Layout --- */
        .navbar-clean {
            background-color: #ffffff;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            transition: all 0.3s ease;
            min-height: 70px;
            padding-top: 0 !important;
            padding-bottom: 0 !important;
        }

        .navbar-collapse {
            background-color: #ffffff;
            /* Fills the dropdown with white */
            margin-top: 10px;
            /* Adds space between logo and links */
            padding-bottom: 20px;
        }

        .navbar-brand img {
            height: 55px;
            /* Mobile Logo Size */
            width: auto;
            object-fit: contain;
            transition: height 0.3s ease;
        }

        /* --- DESKTOP TWEAKS (Large Screens) --- */
        @media (min-width: 992px) {
            body {
                /* DESKTOP: Pushes content down further because navbar is taller */
                padding-top: 100px;
            }

            .navbar-clean {
                height: 100px;
                /* Desktop Height */
            }

            .navbar-brand img {
                height: 90px;
                /* Desktop Logo Size */
            }
        }

        /* --- MAIN CONTENT --- */
        main {
            flex: 1;
            width: 100%;
        }

        /* --- NAV LINKS --- */
        .nav-link-custom {
            color: #555;
            font-weight: 600;
            font-size: 1.05rem;
            padding: 0.5rem 1rem !important;
            transition: color 0.3s ease;
            position: relative;
        }

        .nav-link-custom:hover,
        .nav-link-custom.active {
            color: var(--theme-pink);
        }

        .nav-link-custom::after {
            content: '';
            position: absolute;
            bottom: 5px;
            left: 1rem;
            right: 1rem;
            height: 3px;
            background-color: var(--theme-pink);
            border-radius: 2px;
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .nav-link-custom.active::after,
        .nav-link-custom:hover::after {
            transform: scaleX(1);
        }

        /* --- BUTTONS --- */
        .btn-auth {
            padding: 8px 25px;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-size: 0.95rem;
            width: 100%;
            /* Mobile: Full Width */
        }

        @media (min-width: 992px) {
            .btn-auth {
                width: auto;
                /* Desktop: Auto Width */
            }
        }

        .btn-login-custom {
            color: var(--theme-pink);
            border: 2px solid var(--theme-pink);
            background-color: white;
        }

        .btn-login-custom:hover {
            background-color: var(--theme-pink);
            color: white;
            box-shadow: 0 4px 10px rgba(254, 126, 122, 0.2);
        }

        .btn-register-custom {
            background-color: var(--theme-pink);
            border: 2px solid var(--theme-pink);
            color: white;
        }

        .btn-register-custom:hover {
            background-color: var(--theme-pink-hover);
            border-color: var(--theme-pink-hover);
            box-shadow: 0 4px 10px rgba(254, 126, 122, 0.3);
            transform: translateY(-1px);
        }

        .btn-logout-custom {
            border-radius: 8px;
            font-weight: 600;
            padding: 8px 25px;
            width: 100%;
        }

        @media (min-width: 992px) {
            .btn-logout-custom {
                width: auto;
            }
        }

        /* --- FOOTER --- */
        footer {
            background: linear-gradient(to bottom, #2b3035, #212529);
        }

        .footer-heading {
            letter-spacing: 1px;
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .footer-link {
            transition: all 0.3s ease;
            display: inline-block;
        }

        .footer-link:hover {
            color: var(--theme-yellow) !important;
            transform: translateX(5px);
        }

        .social-icon {
            transition: transform 0.3s ease, color 0.3s ease;
        }

        .social-icon:hover {
            transform: translateY(-3px);
            color: var(--theme-yellow) !important;
        }
    </style>
</head>

<body class="@yield('body-class', 'bg-light')">

    {{-- Navbar fixed at top. z-index 1030 ensures it stays ABOVE the images --}}
    <nav class="navbar navbar-expand-lg position-fixed top-0 w-100 navbar-clean" style="z-index:1030;">
        <div class="container px-4">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
                <img src="{{ asset('/image/navbar.png') }}" alt="Logo">
            </a>

            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">

                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom {{ request()->routeIs('home') ? 'active' : '' }}"
                            href="{{ route('home') }}">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom"
                            href="{{ request()->routeIs('home') ? '#services' : route('home') . '#services' }}">
                            Services
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom {{ request()->routeIs('aboutUs') ? 'active' : '' }}"
                            href="{{ route('aboutUs') }}">About Us</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom {{ request()->routeIs('activities') ? 'active' : '' }}"
                            href="{{ route('activities') }}">Activities</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom {{ request()->routeIs('contact') ? 'active' : '' }}"
                            href="{{ route('contact') }}">Contact Us</a>
                    </li>
                </ul>

                <div class="d-flex flex-column flex-lg-row align-items-center ms-lg-auto gap-3 mt-3 mt-lg-0">
                    @auth
                    <form method="POST" action="{{ route('logout') }}" class="m-0 w-100 w-lg-auto">
                        @csrf
                        <button class="btn btn-danger btn-logout-custom" type="submit">
                            Logout
                        </button>
                    </form>
                    @else
                    <a class="btn btn-auth btn-login-custom" href="{{ route('login') }}">
                        Login
                    </a>
                    <a class="btn btn-auth btn-register-custom" href="{{ route('register') }}">
                        Register
                    </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main>
        @yield('content')
    </main>

    <footer class="text-white pt-5 pb-3 mt-auto">
        <div class="container text-center text-md-start">
            <div class="row g-4">
                <div class="col-md-4">
                    <h6 class="text-uppercase fw-bold text-warning footer-heading mb-4">PKKM Batu Pahat</h6>
                    <p class="text-white-50 small lh-lg">
                        Empowering the Batu Pahat community through education and essential aid. We are dedicated to building a resilient and supportive environment for everyone.
                    </p>
                </div>

                <div class="col-md-4">
                    <h6 class="text-uppercase fw-bold text-warning footer-heading mb-4">Quick Links</h6>
                    <ul class="list-unstyled">
                        <li class="mb-3"><a href="{{ route('home') }}" class="text-white-50 text-decoration-none footer-link small">Home</a></li>
                        <li class="mb-3"><a href="{{ route('home') }}#services" class="text-white-50 text-decoration-none footer-link small">Our Services</a></li>
                        <li class="mb-3"><a href="{{ route('contact') }}" class="text-white-50 text-decoration-none footer-link small">Contact Us</a></li>
                    </ul>
                </div>

                <div class="col-md-4">
                    <h6 class="text-uppercase fw-bold text-warning footer-heading mb-4">Get in Touch</h6>
                    <ul class="list-unstyled text-white-50 small">
                        <li class="mb-3">
                            <i class="bi bi-envelope-fill me-3 text-warning"></i>
                            ajb.batupahat@gmail.com
                        </li>
                        <li class="mb-3">
                            <i class="bi bi-telephone-fill me-3 text-warning"></i>
                            +6018 382 4890
                        </li>
                        <li class="mb-3">
                            <i class="bi bi-geo-alt-fill me-3 text-warning"></i>
                            Batu Pahat, Johor
                        </li>
                    </ul>

                    <div class="mt-4">
                        <a href="https://www.facebook.com/Care4BP/" target="_blank" class="text-white me-3 fs-5 social-icon">
                            <i class="bi bi-facebook"></i>
                        </a>
                        <a href="https://www.instagram.com/pkkmbp_ai.jia.bei/" target="_blank" class="text-white me-3 fs-5 social-icon">
                            <i class="bi bi-instagram"></i>
                        </a>
                        <a href="https://wa.me/60183824890" target="_blank" class="text-white fs-5 social-icon">
                            <i class="bi bi-whatsapp"></i>
                        </a>
                    </div>
                </div>
            </div>

            <hr class="my-5 border-secondary opacity-25">

            <div class="row align-items-center">
                <div class="col-md-12 text-center text-white-50 small">
                    <span class="opacity-75">© {{ date('Y') }} PKKM Batu Pahat. All rights reserved.</span>
                </div>
            </div>
        </div>
    </footer>

    @vite('resources/js/app.js')
</body>

</html>