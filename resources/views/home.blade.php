@extends('layout.app')
@section('title', 'Home')

@section('content')

{{-- Internal Styles for specific Home page tweaks --}}
<style>
    html,
    body {
        overflow-x: hidden;
    }

    .carousel {
        max-width: 100vw;
        overflow: hidden;
    }

    .carousel-img {
        display: block;
        width: 100%;
        max-width: 100%;
        height: auto;
    }

    @media (min-width: 768px) {
        .carousel-img {
            height: 400px;
            object-fit: cover;
        }
    }

    @media (min-width: 992px) {
        .carousel-img {
            height: 600px;
        }
    }
</style>

<div class="container-fluid p-0">
    <div class="carousel slide carousel-fade"
        data-bs-ride="carousel"
        data-bs-interval="5000">

        <div class="carousel-inner">
            <div class="carousel-item active">
                <picture>
                    <source media="(max-width: 768px)" srcset="{{ asset('image/banner1Mobile.png') }}">
                    <img src="{{ asset('image/banner1.png') }}"
                        class="carousel-img"
                        alt="Banner 1">
                </picture>
            </div>

            <div class="carousel-item">
                <picture>
                    <source media="(max-width: 768px)" srcset="{{ asset('image/banner2Mobile.png') }}">
                    <img src="{{ asset('image/banner2.png') }}"
                        class="carousel-img"
                        alt="Banner 2">
                </picture>
            </div>

            <div class="carousel-item">
                <picture>
                    <source media="(max-width: 768px)" srcset="{{ asset('image/banner3Mobile.png') }}">
                    <img src="{{ asset('image/banner3.png') }}"
                        class="carousel-img"
                        alt="Banner 3">
                </picture>
            </div>

            <div class="carousel-item">
                <picture>
                    <source media="(max-width: 768px)" srcset="{{ asset('image/banner4Mobile.png') }}">
                    <img src="{{ asset('image/banner4.png') }}"
                        class="carousel-img"
                        alt="Banner 4">
                </picture>
            </div>

            <div class="carousel-item">
                <picture>
                    <source media="(max-width: 768px)" srcset="{{ asset('image/banner5Mobile.png') }}">
                    <img src="{{ asset('image/banner5.png') }}"
                        class="carousel-img"
                        alt="Banner 5">
                </picture>
            </div>
            <div class="carousel-item">
                <picture>
                    <source media="(max-width: 768px)" srcset="{{ asset('image/banner6Mobile.png') }}">
                    <img src="{{ asset('image/banner6.png') }}"
                        class="carousel-img"
                        alt="Banner 6">
                </picture>
            </div>
        </div>

    </div>
</div>


<div class="container">
    <div class="row mt-5 align-items-center">
        <div class="col-lg-6 mb-4 mb-lg-0 text-center">
            {{-- img-fluid prevents it from overflowing the screen on mobile --}}
            <img src="{{ asset('image/homePageSecond.png') }}" class="img-fluid rounded-4 shadow-sm" alt="Our Mission">
        </div>

        <div class="col-lg-5 offset-lg-1">
            <div class="d-flex flex-column align-items-center text-center">
                <h1 class="fw-bold mb-3">Our Mission</h1>
                <p class="fs-5 text-muted">
                    "Through education and supply assistance, we help disadvantaged families and spread love more abundantly throughout the Batu Pahat community."
                </p>
                <p class="fs-5 text-muted">
                    透過教育與物資援助，幫助弱勢家庭，讓這個社區充滿更多的愛。
                </p>
            </div>
        </div>
    </div>

    <div class="my-5">
        <div class="rounded-5 border-0 p-4 p-md-5" id="services" style="background-color:rgba(249, 243, 231, 1)">

            <h2 class="text-center fw-semibold mb-5 fs-3">
                Looking to learn or receive support?<br>
                <span class="text-warning">Start your journey with us today.</span>
            </h2>

            <div class="row justify-content-center g-4">

                <div class="col-md-6 col-lg-5">
                    <div class="card h-100 p-3 rounded-4 border-0 shadow-sm">
                        <img src="{{ asset('image/supply.png') }}" class="card-img-top mx-auto" alt="Beneficiary"
                            style="height: 180px; object-fit: contain; width: 100%;">

                        <div class="card-body text-center d-flex flex-column">
                            <h3 class="card-title fw-bold">Beneficiary Application</h3>
                            <p class="card-text text-muted flex-grow-1">
                                Apply to become a registered beneficiary. Once approved, you will be eligible to submit applications for our monthly supply assistance packages.
                            </p>
                            <div class="mt-3">
                                <a href="{{ route('beneficiary.form') }}" class="btn text-white px-4 py-2 rounded-4 w-100" style="background-color: rgb(254,172,0);">
                                    <i class="bi bi-person-plus-fill"></i> Register as Beneficiary
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-5">
                    <div class="card h-100 p-3 rounded-4 border-0 shadow-sm">
                        <img src="{{ asset('image/tuition.png') }}" class="card-img-top mx-auto" alt="Student"
                            style="height: 180px; object-fit: contain; width: 100%;">

                        <div class="card-body text-center d-flex flex-column">
                            <h3 class="card-title fw-bold">Student Transit & Care</h3>
                            <p class="card-text text-muted flex-grow-1">
                                Apply for our <strong>free transit service</strong>. We provide transport from school to our center, offering students a safe place to finish homework and learn after school.
                            </p>
                            <div class="mt-3">
                                <a href="{{ route('student.form') }}" class="btn text-white px-4 py-2 rounded-4 w-100" style="background-color: rgb(254,172,0);">
                                    <i class="bi bi-bus-front-fill"></i> Apply for Transit
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection