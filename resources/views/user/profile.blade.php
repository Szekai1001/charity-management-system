@extends($layout) <!-- use the layout passed from controller -->
@include('components.alerts')
@section('content')

<div class="container-fluid px-0">
    <div class="row g-4">

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 text-center h-100">
                <div class="card-body p-4 d-flex flex-column align-items-center">

                    <div class="position-relative mb-3">
                        <img src="{{ $profile->avatar ? asset('storage/' . $profile->avatar) : asset('image/boy.png') }}"
                            alt="Avatar"
                            class="rounded-circle border border-3 border-light shadow-sm"
                            style="width: 140px; height: 140px; object-fit: cover;">

                        <span class="position-absolute bottom-0 end-0 p-2 bg-success border border-light rounded-circle">
                            <span class="visually-hidden">Active</span>
                        </span>
                    </div>

                    <h4 class="fw-bold text-dark mb-1">{{ $profile->name }}</h4>
                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill mb-4">
                        {{ ucfirst($role) }} Account
                    </span>

                    <div class="w-100 mt-auto">
                        <p class="text-muted small mb-2 text-start fw-bold">Update Profile Picture</p>
                        <form action="{{ route('user.updateAvatar') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="input-group input-group-sm">
                                <input type="file" name="avatar" class="form-control" id="avatarUpload" required>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-cloud-upload"></i>
                                </button>
                            </div>
                            <small class="text-muted fst-italic" style="font-size: 11px;">Max size: 2MB</small>
                        </form>
                    </div>

                </div>
            </div>
        </div>


        <div class="col-lg-8">

            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white py-3 px-4 border-bottom-0 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold text-dark mb-0">Personal Information</h5>
                    <button type="button" class="btn btn-light text-primary btn-sm fw-bold border shadow-sm"
                        data-bs-toggle="modal" data-bs-target="#editPersonalInfo">
                        <i class="bi bi-pencil-square me-2"></i>Edit Details
                    </button>
                </div>

                <div class="card-body px-4 pb-4 pt-0">
                    <div class="row g-4 mt-1">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-2">
                                <div class="bg-light p-2 rounded-circle me-3 text-secondary">
                                    <i class="bi bi-person fs-5"></i>
                                </div>
                                <div>
                                    <small class="text-uppercase text-muted fw-bold" style="font-size: 11px;">Full Name</small>
                                    <div class="fw-medium text-dark">{{ $profile->name }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-2">
                                <div class="bg-light p-2 rounded-circle me-3 text-secondary">
                                    <i class="bi bi-envelope fs-5"></i>
                                </div>
                                <div>
                                    <small class="text-uppercase text-muted fw-bold" style="font-size: 11px;">Email Address</small>
                                    <div class="fw-medium text-dark">{{ $profile->user->email }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-2">
                                <div class="bg-light p-2 rounded-circle me-3 text-secondary">
                                    <i class="bi bi-telephone fs-5"></i>
                                </div>
                                <div>
                                    <small class="text-uppercase text-muted fw-bold" style="font-size: 11px;">Phone Number</small>
                                    <div class="fw-medium text-dark">{{ $profile->phone_number ?? 'Not Set' }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-2">
                                <div class="bg-light p-2 rounded-circle me-3 text-secondary">
                                    <i class="bi bi-cake2 fs-5"></i>
                                </div>
                                <div>
                                    <small class="text-uppercase text-muted fw-bold" style="font-size: 11px;">Birth Date</small>
                                    <div class="fw-medium text-dark">
                                        {{ $profile->birth_date ? \Carbon\Carbon::parse($profile->birth_date)->format('d F Y') : 'Not Set' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-3 px-4 border-bottom-0">
                    <h5 class="fw-bold text-dark mb-0">Security Settings</h5>
                </div>
                <div class="card-body px-4 pb-4 pt-0">

                    <div class="alert alert-light border-start border-4 border-warning shadow-sm" role="alert">
                        <div class="d-flex">
                            <i class="bi bi-shield-lock-fill text-warning fs-4 me-3"></i>
                            <div>
                                <h6 class="fw-bold mb-1">Update Password</h6>
                                <small class="text-muted">Ensure your account uses a strong password (min. 8 characters).</small>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('user.password.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="current_password" class="form-label small fw-bold">Current Password</label>
                                <input type="password" name="current_password" class="form-control bg-light" required>
                                @error('current_password') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="password" class="form-label small fw-bold">New Password</label>
                                <input type="password" name="password" class="form-control bg-light" required>
                                @error('password') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="password_confirmation" class="form-label small fw-bold">Confirm New</label>
                                <input type="password" name="password_confirmation" class="form-control bg-light" required>
                            </div>

                            <div class="col-12 text-end mt-3">
                                <button type="submit" class="btn btn-dark px-4 shadow-sm">Update Password</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>


<div class="modal fade" id="editPersonalInfo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">Edit Profile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4">
                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PATCH')

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Full Name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $profile->name) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Email Address</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $profile->user->email) }}" required>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Phone Number</label>
                            <input type="text" name="phone_number" class="form-control" value="{{ old('phone_number', $profile->phone_number) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Birth Date</label>
                            <input type="date" name="birth_date" class="form-control" value="{{ old('birth_date', $profile->birth_date) }}" required>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection