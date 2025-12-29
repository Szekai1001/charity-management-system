
<div class="container mt-5">
    <h2 class="fw-bold text-primary mb-4">Profile Settings</h2>

    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body">
            @include('profile.partials.update-profile-information-form')
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body">
            @include('profile.partials.update-password-form')
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</div>
