@extends('layout.app')

@section('content')
<div class="container d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow-lg border-0 rounded-3">
            <div class="card-body p-5 text-center">
                
                <div class="mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="currentColor" class="bi bi-check-circle-fill text-success" viewBox="0 0 16 16">
                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                    </svg>
                </div>

                <h2 class="fw-bold text-dark mb-3">Application Submitted!</h2>
                
                <p class="text-muted mb-4">
                    Thank you. Your application has been securely received and is currently in our queue for review.
                </p>

                <hr class="my-4">

                <div class="text-start mb-4 bg-light p-3 rounded">
                    <h6 class="fw-bold text-secondary text-uppercase small ls-1">What happens next?</h6>
                    <ul class="list-unstyled text-muted small mb-0">
                        <li class="mb-2"><i class="bi bi-envelope me-2"></i> We have sent a confirmation email to you.</li>
                        <li class="mb-2"><i class="bi bi-clock me-2"></i> Review typically takes <strong>3-5 business days</strong>.</li>
                        <li><i class="bi bi-bell me-2"></i> You will be notified once the status changes.</li>
                    </ul>
                </div>

                <div class="d-grid gap-2">
                    <a href="{{ route('home') }}" class="btn btn-primary btn-lg">Return to Dashboard</a>
                    </div>

            </div>
        </div>
    </div>
</div>
@endsection