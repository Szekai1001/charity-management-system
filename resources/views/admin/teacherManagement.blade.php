@extends('layout.admin')
@include('components.alerts')

@section('content')

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold text-dark mb-0">Register New Teacher</h4>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger py-2 mb-3 small">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body p-4">
            {{-- Make sure to define this route in web.php --}}
            <form action="{{ route('teacher.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <h6 class="text-primary fw-bold text-uppercase mb-3 small border-bottom pb-2">1. Personal Information</h6>
                <div class="row g-3 mb-4">
                    
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted">Full Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('tName') is-invalid @enderror" 
                               name="tName" value="{{ old('tName') }}" placeholder="e.g. Ahmad bin Ali" required>
                        @error('tName')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted">Email Address <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                               name="email" value="{{ old('email') }}" placeholder="teacher@school.com" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted">IC Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('tIC') is-invalid @enderror" 
                               name="tIC" value="{{ old('tIC') }}" placeholder="010203-10-1234" required>
                        <small class="text-muted" style="font-size: 0.7rem;">(Default Password)</small>
                        @error('tIC')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted">Phone Number <span class="text-danger">*</span></label>
                        <input type="tel" class="form-control @error('tPhone') is-invalid @enderror" 
                               name="tPhone" value="{{ old('tPhone') }}" placeholder="012-3456789" required>
                        @error('tPhone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted">Birth Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('tBirthDate') is-invalid @enderror" 
                               name="tBirthDate" value="{{ old('tBirthDate') }}" required>
                        @error('tBirthDate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted d-block">Gender <span class="text-danger">*</span></label>
                        <div class="d-flex gap-3 pt-1">
                            <div class="form-check">
                                <input class="form-check-input @error('tGender') is-invalid @enderror" type="radio" id="male" name="tGender" value="male" 
                                       {{ old('tGender') == 'male' ? 'checked' : '' }} required>
                                <label class="form-check-label small" for="male">Male</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input @error('tGender') is-invalid @enderror" type="radio" id="female" name="tGender" value="female" 
                                       {{ old('tGender') == 'female' ? 'checked' : '' }} required>
                                <label class="form-check-label small" for="female">Female</label>
                            </div>
                        </div>
                        @error('tGender')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                </div>

                <h6 class="text-primary fw-bold text-uppercase mb-3 small border-bottom pb-2">2. Address Details</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-8">
                        <label class="form-label small fw-bold text-muted">Street Address <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('street') is-invalid @enderror" 
                               name="street" value="{{ old('street') }}" placeholder="No. 123, Jalan..." required>
                        @error('street')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted">Area / Taman</label>
                        <input type="text" class="form-control @error('area') is-invalid @enderror" 
                               name="area" value="{{ old('area') }}" placeholder="Taman Bukit Perdana">
                        @error('area')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted">Postal Code <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('zip') is-invalid @enderror" 
                               name="zip" value="{{ old('zip') }}" pattern="[0-9]{5}" placeholder="83000" required>
                        @error('zip')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted">City</label>
                        <input type="text" class="form-control bg-light" name="city" value="Batu Pahat" readonly>
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted">State</label>
                        <input type="text" class="form-control bg-light" name="state" value="Johor" readonly>
                    </div>
                </div>

                <h6 class="text-primary fw-bold text-uppercase mb-3 small border-bottom pb-2">3. Qualifications & Experience</h6>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted">Highest Education <span class="text-danger">*</span></label>
                        <select name="education" class="form-select @error('education') is-invalid @enderror" required>
                            <option value="" disabled selected>-- Select --</option>
                            @foreach($educations as $education)
                                <option value="{{ $education }}" {{ old('education') == $education ? 'selected' : '' }}>
                                    {{ $education }}
                                </option>
                            @endforeach
                        </select>
                        @error('education')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted">Field of Expertise <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('field_of_expertise') is-invalid @enderror" 
                               name="field_of_expertise" value="{{ old('field_of_expertise') }}" placeholder="e.g. Mathematics" required>
                        @error('field_of_expertise')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted">Experience (Years) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('experience_years') is-invalid @enderror" 
                               name="experience_years" value="{{ old('experience_years') }}" min="0" placeholder="0" required>
                        @error('experience_years')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label small fw-bold text-muted">Experience Details <span class="text-danger">*</span></label>
                        <textarea name="experience_details" class="form-control @error('experience_details') is-invalid @enderror" 
                                  rows="2" placeholder="Brief description of previous roles..." required>{{ old('experience_details') }}</textarea>
                        @error('experience_details')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                    <button type="submit" class="btn btn-primary btn-sm px-5 fw-bold">Save Teacher</button>
                </div>

            </form>
        </div>
    </div>
</div>

@endsection