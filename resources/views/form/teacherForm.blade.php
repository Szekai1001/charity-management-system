@extends('layout.app')
@include('components.alerts')
@section('title', 'teacher Form')
@section('content')


@if($alreadyApplied)
<div class="col-6 mx-auto mt-5">
    <div class="alert alert-warning text-center">
        <h4 class="fw-bold">You already submitted this form</h4>
        <p>Our records show that you have already submitted this application. Thank you for your interest!</p>
    </div>
</div>
@else

@if($formControl)
<div class="my-5 text-center">
    <h2 class="fw-bold">Transit Service Teacher Application Form</h2>
</div>

<!-- Start Form -->
<div class="teacherFormContainer d-flex flex-column justify-content-center align-items-centr mx-auto" style="max-width: 1000px; min-height: 100vh">
    <form data-multi-step action="{{route('teacher.store')}}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="control_id" value="{{ $formControl->id }}">
        <!-- Personal Details (First Page)-->
        <div id="page1">
            <div class="bg-white p-4 rounded shadow my-3" style="width: fit-content; max-width: 100%;">
                <h3 class="mt-2 fw-bold">Personal Details</h3>
                <div class="row g-3 mb-5">
                    <div class="col-md-6">
                        <label for="tName" class="form-label">Teacher Name</label>
                        <input type="text" class="form-control" id="tName" name="tName" placeholder="Enter your full name" required>
                    </div>
                    <div class="col-md-6">
                        <label for="tIC" class="form-label">IC Number</label>
                        <input type="text" class="form-control" id="tIC" name="tIC" placeholder="Enter your IC number (e.g., 010203-10-1234)" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Gender</label><br>
                        <input class="form-check-input" type="radio" id="male" name="tGender" value="male" required>
                        <label class="form-check-label" for="male">Male</label>
                        <input class="form-check-input" type="radio" id="female" name="tGender" value="female" required>
                        <label class="form-check-label" for="female">Female</label>
                    </div>
                    <div class="col-md-6">
                        <label for="tBirthDate" class="form-label">Birth Date</label>
                        <input type="date" class="form-control" id="tBirthDate" name="tBirthDate" required>
                    </div>
                    <div class="col-md-6">
                        <label for="tPhone">Phone Number</label>
                        <input type="tel" class="form-control" id="tPhone" name="tPhone" placeholder="Enter your phone number (e.g., 012-3466789)" required>
                    </div>
                    <div class="col-md-6">
                        <label for="street" class="form-label">Street Address:</label>
                        <input type="text" class="form-control" id="street" name="street" required>
                    </div>
                    <div class="col-md-6">
                        <label for="area" class="form-label">Area:</label>
                        <input type="text" class="form-control" id="area" name="area" placeholder="e.g., Taman Bukit Perdana">
                    </div>
                    <div class="col-md-6">
                        <label for="city" class="form-label">City:</label>
                        <input type="text" class="form-control" id="city" name="city" value="Batu Pahat" readonly>
                    </div>
                    <div class="col-md-6">
                        <label for="state" class="form-label">State:</label>
                        <input type="text" class="form-control" id="state" name="state" value="Johor" readonly>
                    </div>
                    <div class="col-md-6">
                        <label for="zip" class="form-label">Postal Code:</label>
                        <input type="text" class="form-control" id="zip" name="zip" pattern="[0-9]{5}" placeholder="e.g., 83000" required>
                    </div>
                    <div class="col-md-6">
                        <label for="education" class="form-label">Education level</label>
                        <select name="education" id="education" class="form-select" required>
                            <option value="" disabled selected>-- Select education level --</option>
                            @foreach($educations as $education)
                            <option value="{{ $education }}">{{ $education }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="expertise">Field of Expertise</label>
                        <input type="text" name="field_of_expertise" class="form-control" placeholder="e.g., Mathematics, Science" required>
                    </div>
                    <div class="col-md-6">
                        <label for="experience_years">Years of Teaching Experience</label>
                        <input type="number" name="experience_years" class="form-control" min="0" required>
                    </div>
                    <div class="col-md-6">
                        <label for="experience_details">Teaching Experience Details</label>
                        <textarea name="experience_details" class="form-control" rows="4" placeholder="List your previous teaching experience including schools/tuition centers and subjects taught" required></textarea>
                    </div>
                </div>
            </div>
            <!-- Navigation Button (Next Page) -->
            <div class="mb-5 text-end">
                <button type="button" class="next-page btn btn-primary">Next</button>
            </div>
        </div>
        <!-- Upload Document (third page) -->
        <div id="page2">
            <div class="bg-white p-4 rounded shadow my-3" style="width: fit-content; max-width: 100%;">

                <h3 class="fw-bold">Required Document</h3>
                <p class="text-muted" style="font-size: 0.9rem;">
                    Accepted formats: <strong>PDF, JPG, JPEG, PNG</strong>.
                    Maximum file size: <strong>2MB</strong>.
                    Please ensure the document is clear, readable, and visibly contains the text
                    <strong>/For Official Use/</strong> before uploading, as this involves sensitive information.
                </p>
                <label for="tIC_copy" class="form-label">IC copy</label>
                <input type="file" id="tIC_copy" name="tIC_copy" class="form-control" accept=".pdf,.jpg,.jpeg,.png"required><br><br>
                <label for="resume" class="form-label">Resume / CV</label>
                <input type="file" id="resume" name="resume" class="form-control" accept=".pdf,.jpg,.jpeg,.png"required><br><br>

                <div class="extra-option">
                    <h3 class="mt-2 fw-bold">Other Certification</h3>
                    <div class="card p-3 my-3 position-relative extra-options">
                        <span class="remove-btn position-absolute top-0 end-0 mt-2 me-2" role="button" title="Remove">
                            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-x-lg ms-auto" viewBox="0 0 16 16">
                                <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z" />
                            </svg>
                        </span>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="otherCertName">Certification Name</label>
                                <input class="form-control" type="text" id="otherCertName" name="otherCertName[]"placeholder="Certification Name (e.g., TESL, Teaching License)">
                            </div>
                            <div class="col-md-6">
                                <label for="form-label" for="otherCertFile">Upload your file</label>
                                <input type="file" id="otherCertFile" name="otherCertFile[]"class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                            </div>
                        </div>
                    </div>
                    <button type="button" class="insert-before btn btn-secondary mt-3">Add Certification</button>
                </div>
            </div>

            <!-- Navigation Button (next and prev) -->
            <div class="mt-3 d-flex justify-content-between">
                <button type="button" class="prev-page btn btn-secondary">Back</button>
                <button type="submit" class="btn btn-success submit" onclick="return confirm('Are you sure you want to submit this form?')">Submit</button>
            </div>
        </div>
    </form>
</div>
    <!-- End Form -->
    @else
    <div class="col-6 mx-auto mt-5">
        <div class="alert alert-warning text-center">
            <h4 class="fw-bold">Application Not Available</h4>
            <p>The form is currently closed or unavailable.</p>
        </div>
    </div>
@endif
@endif

<!-- <script>
    // Navigate to different Page
    let currentPage = 1;
    const totalPages = 2;

    function showPage(page) {
        for (let i = 1; i <= totalPages; i++) {
            const section = document.getElementById('page' + i);
            section.style.display = (i === page) ? 'block' : 'none';
        }
    }

    function nextPage() {
        const currentSection = document.getElementById('page' + currentPage);
        const inputs = currentSection.querySelectorAll('input, select, textarea');
        let isValid = true;

        inputs.forEach(input => {
            if (input.hasAttribute('required') && !input.value.trim()) {
                isValid = false;
                input.classList.add('is-invalid'); //Add red border
            } else {
                input.classList.remove('is-invalid');
            }
        })

        if (!isValid) {
            alert("Please fill all required fields before processing");
            return;
        }

        if (currentPage < totalPages) {
            currentPage++;
            showPage(currentPage);
        }
    }

    function prevPage() {
        if (currentPage > 1) {
            currentPage--;
            showPage(currentPage);
        }
    }

    let docIndex = 1;

    function addMoreDocument() {
        const container = document.getElementById('document-upload-container');

        const newGroup = document.createElement('div');
        newGroup.classList.add('document-group', 'mb-3');

        newGroup.innerHTML = `
            <label for="documents[${docIndex}][type]" class="form-label">Document Type</label>
            <select name="documents[${docIndex}][type]" class="form-select" required>
                <option value="">-- Select Document Type --</option>
                <option value="resume">Resume / CV</option>
                <option value="certificate">Certificate</option>
                <option value="teaching_cert">Teaching Certification</option>
                <option value="id">Identity Document (IC/Passport)</option>
                <option value="other">Other</option>
            </select>

            <label for="documents[${docIndex}][file]" class="form-label mt-2">Upload File</label>
            <input type="file" name="documents[${docIndex}][file]" class="form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required>
        `;

        container.appendChild(newGroup);
        docIndex++;
    }

    // Ensure the code run after HTML fully loaded
    document.addEventListener('DOMContentLoaded', function() {
        // Show first page
        showPage(currentPage);

        // Setup 'Other' select behavior
        document.querySelectorAll('.other').forEach(function(select) {
            select.addEventListener('change', function() {
                const container = this.closest('.col-md-5');
                const otherInput = container.querySelector('.other-container');
                const inputField = otherInput.querySelector('input');

                if (this.value === 'Other') {
                    otherInput.classList.remove('d-none');
                    inputField.disabled = false;
                    inputField.required = true;
                } else {
                    otherInput.classList.add('d-none');
                    inputField.disabled = true;
                    inputField.required = false;
                    inputField.value = '';
                }
            });
        });
    });
</script> -->
@endsection