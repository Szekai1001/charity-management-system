@extends('layout.app')

@section('content')

<div class="container py-5">

    {{-- 1. HEADER SECTION --}}
    <div class="text-center mb-5">
        <h2 class="fw-bold text-dark">Contact Support</h2>
        <p class="text-muted col-md-8 mx-auto">
            Have questions about supply distribution or your account?
            Reach out to us via our direct contacts below.
        </p>
    </div>

    {{-- 2. QUICK CONTACT CARDS --}}
    <div class="row g-4 mb-5 justify-content-center">
        {{-- Card 1: Phone --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 text-center py-4 rounded-4 hover-card">
                <div class="card-body">
                    <div class="mb-3 text-primary bg-primary-subtle d-inline-block p-3 rounded-circle">
                        <i class="bi bi-telephone-fill fs-4"></i>
                    </div>
                    <h6 class="fw-bold">Call Us</h6>
                    <p class="text-muted small mb-0">+6018 382 4890</p>
                    <p class="text-muted small">Mon-Fri, 9am - 5pm</p>
                </div>
            </div>
        </div>

        {{-- Card 2: Email --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 text-center py-4 rounded-4 hover-card">
                <div class="card-body">
                    <div class="mb-3 text-success bg-success-subtle d-inline-block p-3 rounded-circle">
                        <i class="bi bi-envelope-fill fs-4"></i>
                    </div>
                    <h6 class="fw-bold">Email Us</h6>
                    <p class="text-muted small mb-0">ajb.batupahat@gmail.com</p>
                    <p class="text-muted small">We reply within 24 hours</p>
                </div>
            </div>
        </div>

        {{-- Card 3: Location --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 text-center py-4 rounded-4 hover-card">
                <div class="card-body">
                    <div class="mb-3 text-danger bg-danger-subtle d-inline-block p-3 rounded-circle">
                        <i class="bi bi-geo-alt-fill fs-4"></i>
                    </div>
                    <h6 class="fw-bold">Visit Us</h6>
                    <p class="text-muted small mb-0">18a, Jalan Perdana 2/24, Taman Bukit Perdana</p>
                    <p class="text-muted small">83000 Batu Pahat, Johor, Malaysia</p>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. INFO SECTION (FAQ & MAP) --}}
    <div class="row g-4">

        {{-- Left Column: FAQ --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100 rounded-4 p-4">
                <div class="card-body">
                    <h5 class="fw-bold mb-4"><i class="bi bi-question-circle-fill me-2 text-warning"></i>Frequently Asked</h5>

                    <div class="accordion accordion-flush" id="faqAccordion">
                        {{-- FAQ 1: Eligibility & Opening Dates --}}
                        <div class="accordion-item border-0 mb-3 bg-light rounded-3">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed bg-transparent shadow-none fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    Am I eligible to make an application?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-muted small pt-0">
                                    Eligibility is based on need, focusing on families facing emergencies, poverty, or specific hardships. <br><br>
                                    <strong>Important:</strong> Please pay close attention to the <strong>"Open Date"</strong> listed on each specific form. You can only submit an application during the open period stated for that program.
                                </div>
                            </div>
                        </div>

                        {{-- FAQ 2: Free Transit / Reading Class Availability --}}
                        <div class="accordion-item border-0 mb-3 bg-light rounded-3">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed bg-transparent shadow-none fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    Is there still space for the free transit service?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-muted small pt-0">
                                    Spaces for our <strong>transit service</strong> are limited as we prioritize children from vulnerable families who need academic support. Please contact the center directly to check the current availability for your child's age group.
                                </div>
                            </div>
                        </div>

                        {{-- FAQ 3: Donations --}}
                        <div class="accordion-item border-0 bg-light rounded-3">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed bg-transparent shadow-none fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    How can I donate supplies?
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-muted small pt-0">
                                    We welcome donations of material goods (food, daily necessities) and financial support to help families in need. You can drop off supplies at our center or contact us to arrange a collection. We also invite you to join us as a volunteer!
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column: Map --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100 rounded-4 p-4">
                <div class="card-body h-100 d-flex flex-column">
                    <h5 class="fw-bold mb-4"><i class="bi bi-map-fill me-2 text-info"></i>Find Us Here</h5>

                    <div class="ratio ratio-4x3 rounded-4 overflow-hidden shadow-sm border flex-grow-1">
                        {{-- Added a sample Google Maps Embed link (Johor Bahru) --}}
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3987.7610233245396!2d102.9548245!3d1.8400440999999996!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31d057182b0e695d%3A0x738e8e7c8299b474!2zUGVydHVidWhhbiBLZWJhamlrYW4gS2FzaWggTXVybmkg54ix5Yqg5YCN56S-5Yy65YWz5oCA5Lit5b-D!5e0!3m2!1sen!2smy!4v1765977394434!5m2!1sen!2smy"
                            width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>

                    <div class="mt-3 text-center">
                        <a href="https://maps.app.goo.gl/LRjXA4mm2yeWrVVK7" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill px-4">
                            Open in Google Maps <i class="bi bi-box-arrow-up-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection