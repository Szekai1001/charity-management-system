@extends('layout.app')

@section('title', 'Activities - PKKM Batu Pahat')

@section('content')

<style>
    :root {
        --theme-pink: rgb(254, 126, 122);
        --theme-pink-light: rgba(254, 126, 122, 0.08);
        --theme-yellow: #ffc107;
        --text-dark: #333;
    }

    /* --- 1. Featured / Summary Poster Section --- */
    .featured-section {
        background: linear-gradient(to bottom, #fff5f5, #ffffff);
        padding: 80px 0;
    }

    .poster-frame {
        background: white;
        padding: 12px;
        border-radius: 15px;
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.12);
        transform: rotate(-2deg);
        /* Stylish tilt */
        transition: transform 0.5s ease;
    }

    .poster-frame:hover {
        transform: rotate(0deg) scale(1.02);
        z-index: 10;
    }

    .poster-img {
        width: 100%;
        border-radius: 10px;
        /* Using auto height to fit your summary flyer dimensions */
        height: auto;
        object-fit: cover;
    }

    /* --- 2. Core Programs (Main Activities) --- */
    .core-card {
        background: white;
        border: 1px solid #eee;
        border-radius: 16px;
        padding: 30px;
        height: 100%;
        transition: all 0.3s;
        position: relative;
        overflow: hidden;
    }

    .core-card:hover {
        border-color: var(--theme-pink);
        box-shadow: 0 10px 30px rgba(254, 126, 122, 0.15);
        transform: translateY(-5px);
    }

    .core-icon {
        width: 60px;
        height: 60px;
        background: var(--theme-pink);
        color: white;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        margin-bottom: 20px;
    }

    .core-badge {
        position: absolute;
        top: 20px;
        right: 20px;
        background: var(--theme-pink-light);
        color: var(--theme-pink);
        font-size: 0.75rem;
        font-weight: 700;
        padding: 5px 12px;
        border-radius: 20px;
        text-transform: uppercase;
    }

    /* --- 3. Side Activities Grid --- */
    .side-card {
        border: none;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        transition: transform 0.3s;
        height: 100%;
        background: #fff;
    }

    .side-card:hover {
        transform: translateY(-5px);
    }

    .side-img-container {
        height: 180px;
        overflow: hidden;
        position: relative;
    }

    .side-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .category-tag {
        position: absolute;
        bottom: 10px;
        left: 10px;
        background: rgba(0, 0, 0, 0.7);
        color: white;
        padding: 3px 10px;
        border-radius: 4px;
        font-size: 0.7rem;
    }
</style>

<section class="featured-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 order-2 order-lg-1">
                <span class="text-uppercase fw-bold text-danger letter-spacing-2">What We Do</span>
                <h1 class="display-4 fw-bold mt-2 mb-4">Holistic Community Support</h1>
                <p class="lead text-muted mb-4">
                    PKKM Batu Pahat is dedicated to serving the community through a wide range of activities. From daily educational support to emergency welfare aid, our mission is to ensure no one is left behind.
                </p>
                <p class="text-secondary mb-4">
                    The poster here summarizes our key initiatives. Whether you need assistance or want to volunteer, there is a place for everyone in our activities.
                </p>

                <div class="d-flex gap-3">
                    <a href="#contact" class="btn btn-danger px-4 py-2 rounded-3 fw-bold" style="background-color: var(--theme-pink); border:none;">
                        Contact Us
                    </a>
                </div>
            </div>

            <div class="col-lg-5 offset-lg-1 order-1 order-lg-2 mb-5 mb-lg-0">
                <div class="poster-frame">
                    <img src="{{ asset('image/poster.png') }}" alt="Summary Poster of Activities" class="poster-img">
                </div>
                <div class="text-center mt-3 text-muted small fst-italic">
                    *Our General Activity Listing
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-white">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Our Core Programs</h2>
            <p class="text-muted">The pillars of our organization.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="core-card">
                    <span class="core-badge">Daily Service</span>
                    <div class="core-icon">
                        <i class="bi bi-backpack4-fill"></i>
                    </div>
                    <h3 class="fw-bold h4">Transit & Daycare Center</h3>
                    <p class="text-secondary mb-4">
                        A safe haven for students after school. We provide lunch, shower facilities, and homework supervision for children from working-class families.
                    </p>
                    <ul class="list-unstyled text-muted small mb-0">
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Homework Guidance</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Nutritious Meals Provided</li>
                        <li><i class="bi bi-check-circle-fill text-success me-2"></i> Safe Environment</li>
                    </ul>
                </div>
            </div>

            <div class="col-md-6">
                <div class="core-card">
                    <span class="core-badge">Monthly</span>
                    <div class="core-icon">
                        <i class="bi bi-box-seam-fill"></i>
                    </div>
                    <h3 class="fw-bold h4">Essential Supply Distribution</h3>
                    <p class="text-secondary mb-4">
                        Every month, we pack and deliver essential packages (Rice, Oil, Flour) to registered families in the Batu Pahat district.
                    </p>
                    <ul class="list-unstyled text-muted small mb-0">
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Strictly Vetted Beneficiaries</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Door-to-Door Delivery</li>
                        <li><i class="bi bi-check-circle-fill text-success me-2"></i> Emergency Relief Available</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5" style="background-color: #f8f9fa;">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <h3 class="fw-bold m-0">Side Activities & Events</h3>
                <p class="text-muted small m-0">Building a vibrant community through diverse activities.</p>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-6 col-md-3">
                <div class="side-card">
                    <div class="side-img-container">
                        <img src="{{ asset('image/bloodDonation.png') }}" class="side-img" alt="Blood Donation">
                        <span class="category-tag bg-danger">Health</span>
                    </div>
                    <div class="p-3">
                        <h6 class="fw-bold mb-1">Blood Donation Drive</h6>
                        <p class="text-muted small mb-0">Regular campaigns with Hospital Sultanah Nora Ismail.</p>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="side-card">
                    <div class="side-img-container">
                        <img src="{{ asset('image/bazaar.png') }}" class="side-img" alt="Bazaar">
                        <span class="category-tag bg-warning text-dark">Fundraising</span>
                    </div>
                    <div class="p-3">
                        <h6 class="fw-bold mb-1">Charity Bazaar</h6>
                        <p class="text-muted small mb-0">Sales of food and pre-loved items to raise funds.</p>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="side-card">
                    <div class="side-img-container">
                        <img src="{{ asset('image/run.png') }}" class="side-img" alt="Charity Run">
                        <span class="category-tag bg-success">Fitness</span>
                    </div>
                    <div class="p-3">
                        <h6 class="fw-bold mb-1">Charity Run</h6>
                        <p class="text-muted small mb-0">Annual 5km fun run to promote healthy living in Batu Pahat.</p>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="side-card">
                    <div class="side-img-container">
                        <img src="{{ asset('image/talk.png') }}" class="side-img" alt="Talk">
                        <span class="category-tag bg-info text-white">Education</span>
                    </div>
                    <div class="p-3">
                        <h6 class="fw-bold mb-1">Motivational Talks</h6>
                        <p class="text-muted small mb-0">Workshops and talks for students and parents.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>  

@endsection