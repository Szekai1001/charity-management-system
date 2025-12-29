@extends('layout.app') {{-- Assuming your main layout is here --}}

@section('title', 'About Us - PKKM Batu Pahat')

@section('content')

<style>
    /* Theme Variables (Matching your Navbar) */
    :root {
        --theme-pink: rgb(254, 126, 122);
        --theme-pink-light: rgba(254, 126, 122, 0.1);
        --theme-yellow: #ffc107;
        --text-dark: #333;
    }

    /* 1. Hero Section */
    .about-hero {
        background: linear-gradient(135deg, #fff5f5 0%, #ffffff 100%);
        padding: 80px 0 60px;
        text-align: center;
    }

    .section-title {
        color: var(--text-dark);
        font-weight: 700;
        margin-bottom: 1rem;
        position: relative;
        display: inline-block;
    }

    /* Small underline for titles */
    .section-title::after {
        content: '';
        display: block;
        width: 60px;
        height: 4px;
        background: var(--theme-pink);
        margin: 10px auto 0;
        border-radius: 2px;
    }

    /* 2. Mission Cards */
    .mission-card {
        border: none;
        border-radius: 12px;
        background: white;
        padding: 2rem;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        height: 100%;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.03);
    }

    .mission-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
    }

    .icon-box {
        width: 70px;
        height: 70px;
        background-color: var(--theme-pink-light);
        color: var(--theme-pink);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        margin-bottom: 1.5rem;
    }

    /* 3. Story Section */
    .story-img-container {
        position: relative;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    .story-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .story-img-container:hover .story-img {
        transform: scale(1.05);
    }

    /* 4. Stats Section */
    .stats-section {
        background-color: var(--theme-pink);
        color: white;
        padding: 60px 0;
    }

    .stat-number {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--theme-yellow);
    }

    /* 5. Team Section */
    .team-card {
        border: none;
        background: transparent;
        text-align: center;
    }

    .team-img {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid white;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        margin-bottom: 1rem;
        transition: transform 0.3s;
    }

    .team-card:hover .team-img {
        transform: scale(1.05);
        border-color: var(--theme-pink);
    }
</style>

<section class="about-hero">
    <div class="container">
        <span class="text-uppercase fw-bold" style="color: var(--theme-pink); letter-spacing: 2px;">About Us</span>
        <h1 class="display-4 fw-bold mt-2 mb-4">Empowering Batu Pahat</h1>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <p class="lead text-muted">
                    We are a community-driven organization dedicated to bridging the gap between those who need help and those who can give it. Through education and essential aid, we build a stronger future.
                </p>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="mission-card text-center">
                    <div class="icon-box mx-auto">
                        <i class="bi bi-bullseye"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Our Mission</h4>
                    <p class="text-muted small lh-lg">
                        Through education and supply assistance, we help disadvantaged families and spread love more abundantly throughout the Batu Pahat community.
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="mission-card text-center">
                    <div class="icon-box mx-auto">
                        <i class="bi bi-eye"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Our Vision</h4>
                    <p class="text-muted small lh-lg">
                        To transmit good values, positive emotions, and healthy living habits to this community. The organization aims to build a compassionate society by providing material aid, emotional support, and educational empowerment.
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="mission-card text-center">
                    <div class="icon-box mx-auto">
                        <i class="bi bi-heart-fill"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Our Values</h4>
                    <p class="text-muted small lh-lg">
                        Compassion, Transparency, and Community. We believe in the power of working together to solve local challenges with love and integrity.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-light">
    <div class="container py-4">
        <div class="row align-items-center">

            <div class="col-lg-6 mb-4 mb-lg-0">
                <div class="story-img-container position-relative">
                    {{-- Added img-fluid for responsiveness and rounded/shadow for polish --}}
                    <img src="{{ asset('image/story.png') }}"
                        alt="Volunteers working"
                        class="img-fluid rounded-4 shadow-sm w-100"
                        style="object-fit: cover; min-height: 400px;">
                </div>
            </div>

            <div class="col-lg-6 ps-lg-5">
                <h2 class="section-title fw-bold text-dark mb-3">Our Story</h2>
                <h5 class="fw-light text-muted mb-4">From humble beginnings to community impact.</h5>

                <p class="text-secondary lh-lg mb-3">
                    PKKM was founded by Mr. Lim Sern Wang with the mission of helping individuals and families in need without barriers. In the early stages, assistance was provided through church-based efforts. However, it was observed that some people were hesitant to seek help, as they were concerned that receiving assistance might require religious conversion.
                </p>

                <p class="text-secondary lh-lg mb-3">
                    To ensure that support could be offered freely and inclusively to everyone, regardless of background or belief, Mr. Lim Sern Wang decided to establish PKKM as an independent charity organization. This decision enabled the organization to reach a wider community and serve the underprivileged more effectively.
                </p>

                {{-- This text was previously inside a button. It is now a proper paragraph. --}}
                <p class="text-secondary lh-lg mb-4">
                    Before commencing full operations, PKKM underwent a six-month preparation period to gain experience and complete necessary arrangements, including license applications and facility renovations. In 2017, PKKM officially began its operations and continues to uphold its commitment to compassion, inclusivity, and transparency.
                </p>

                {{-- A proper Call to Action button --}}
                <a href="{{ route('contact') }}" class="btn btn-outline-danger px-4 py-2 rounded-pill fw-semibold shadow-sm" style="border-color: var(--theme-pink); color: var(--theme-pink);">
                    Contact Us
                </a>
            </div>
        </div>
    </div>
</section>

<section class="stats-section">
    <div class="container">
        <div class="row text-center g-4">
            <div class="col-md-4">
                <div class="stat-number">40+</div>
                <div class="text-white opacity-75">Families Assisted</div>
            </div>
            <div class="col-md-4">
                <div class="stat-number">20+</div>
                <div class="text-white opacity-75">Students Taught</div>
            </div>
            <div class="col-md-4">
                <div class="stat-number">20+</div>
                <div class="text-white opacity-75">Active Volunteers</div>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container py-4">
        <div class="text-center mb-5">
            <h2 class="section-title">Meet The Team</h2>
            <p class="text-muted">The dedicated individuals behind PKKM Batu Pahat.</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-4 col-lg-3">
                <div class="team-card">
                    <img src="https://ui-avatars.com/api/?name=Ali+Ahmad&background=fe7e7a&color=fff&size=200" alt="Team Member" class="team-img">
                    <h5 class="fw-bold mb-1">Mr. Ali Ahmad</h5>
                    <p class="text-muted small">Chairman</p>
                </div>
            </div>

            <div class="col-md-4 col-lg-3">
                <div class="team-card">
                    <img src="https://ui-avatars.com/api/?name=Sarah+Lee&background=fe7e7a&color=fff&size=200" alt="Team Member" class="team-img">
                    <h5 class="fw-bold mb-1">Ms. Sarah Lee</h5>
                    <p class="text-muted small">Head of Education</p>
                </div>
            </div>

            <div class="col-md-4 col-lg-3">
                <div class="team-card">
                    <img src="https://ui-avatars.com/api/?name=Tan+Ah+Meng&background=fe7e7a&color=fff&size=200" alt="Team Member" class="team-img">
                    <h5 class="fw-bold mb-1">Mr. Tan Ah Meng</h5>
                    <p class="text-muted small">Head of Welfare</p>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection