@extends('layout.teacher')
@include('components.alerts')

@section('content')

<div class="card shadow-sm border-0 border-start border-4 border-primary my-3">
    <div class="card-body">
        <div class="d-flex align-items-center mb-3">
            <i class="bi bi-info-circle-fill text-primary me-2"></i> <h6 class="fw-bold text-dark mb-0">Attendance Requirements</h6>
        </div>

        <div class="mb-3">
            <span class="badge bg-light text-dark border mb-1">Students</span>
            <p class="small text-muted mb-0">
                Ensure the teacher has <strong>started the session</strong> before scanning. Scans made prior will not be recorded.
            </p>
        </div>

        <div>
            <span class="badge bg-light text-dark border mb-1">Teachers</span>
            <p class="small text-muted mb-0">
                You cannot scan your attendance until the admin has <strong>opened the session</strong> for the day.
            </p>
        </div>
    </div>
</div>

<div class="row justify-content-center mt-4">
    <div class="col-md-5 col-lg-4">

        <div class="card border-0 shadow-lg rounded-4 overflow-hidden">

            <div class="d-flex" role="group">

                <input type="radio" class="btn-check" name="attendanceMode" id="modeCheckIn" autocomplete="off" checked>
                <label class="btn flex-fill  py-3 rounded-0 fw-bold d-flex align-items-center justify-content-center gap-2 transition-all"
                    for="modeCheckIn" id="labelCheckIn">
                    <i class="bi bi-box-arrow-in-right fs-5"></i> CHECK IN
                </label>

                <input type="radio" class="btn-check" name="attendanceMode" id="modeCheckOut" autocomplete="off">
                <label class="btn flex-fill py-3 rounded-0 fw-bold d-flex align-items-center justify-content-center gap-2 transition-all"
                    for="modeCheckOut" id="labelCheckOut">
                    <i class="bi bi-box-arrow-left fs-5"></i> CHECK OUT
                </label>

            </div>

            <div class="card-body p-0 position-relative bg-dark">

                <div id="reader" style="width: 100%; aspect-ratio: 1/1;" class="bg-black"></div>

                <div class="position-absolute top-0 start-0 w-100 p-2 text-center">
                    <span id="statusBadge" class="badge bg-success bg-opacity-75 backdrop-blur shadow-sm px-3 py-2 rounded-pill fw-normal">
                        <span class="spinner-grow spinner-grow-sm me-1" role="status" aria-hidden="true"></span>
                        Scanning for Check In...
                    </span>
                </div>

                <div class="position-absolute bottom-0 start-0 w-100 p-3 text-center bg-gradient-to-t">
                    <small class="text-white opacity-75">Point camera at QR Code</small>
                </div>
            </div>

        </div>
    </div>
</div>

<form id="attendanceForm" method="POST" action="{{ route('attendance.scan') }}">
    @csrf
    <input type="hidden" name="scanDetails" id="scanDetails">
    <input type="hidden" name="action" id="action" value="check_in">
</form>

<style>
    /* Custom Styling for the Tabs */
    #labelCheckIn,
    #labelCheckOut {
        border-bottom: 4px solid transparent;
        background-color: #f8f9fa;
        /* Light Gray default */
        color: #6c757d;
        /* Muted Text default */
    }

    /* Active State for Check In (Green) */
    #modeCheckIn:checked+#labelCheckIn {
        background-color: #fff;
        color: #198754;
        /* Success Green */
        border-bottom-color: #198754;
    }

    /* Active State for Check Out (Red) */
    #modeCheckOut:checked+#labelCheckOut {
        background-color: #fff;
        color: #dc3545;
        /* Danger Red */
        border-bottom-color: #dc3545;
    }

    /* Gradient overlay for text readability on video */
    .bg-gradient-to-t {
        background: linear-gradient(to top, rgba(0, 0, 0, 0.8), transparent);
    }

    .backdrop-blur {
        backdrop-filter: blur(4px);
    }

    /* Clean up scanner corners */
    #reader video {
        object-fit: cover;
    }

    #html5-qrcode-anchor-scan-type-change {
        display: none !important;
    }
</style>

<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    const modeCheckIn = document.getElementById('modeCheckIn');
    const modeCheckOut = document.getElementById('modeCheckOut');
    const actionInput = document.getElementById('action');
    const statusBadge = document.getElementById('statusBadge');

    // 1. Switch Logic
    function updateUI(mode) {
        actionInput.value = mode;
        localStorage.setItem('attendanceScanMode', mode);

        if (mode === 'check_in') {
            // *** FIX: Visually check the radio button ***
            modeCheckIn.checked = true;

            // Update UI Colors/Text
            statusBadge.classList.remove('bg-danger');
            statusBadge.classList.add('bg-success');
            statusBadge.innerHTML = `<span class="spinner-grow spinner-grow-sm me-1"></span> Scanning for Check In...`;
        } else {
            // *** FIX: Visually check the radio button ***
            modeCheckOut.checked = true;

            // Update UI Colors/Text
            statusBadge.classList.remove('bg-success');
            statusBadge.classList.add('bg-danger');
            statusBadge.innerHTML = `<span class="spinner-grow spinner-grow-sm me-1"></span> Scanning for Check Out...`;
        }
    }

    // Event Listeners for manual clicking
    modeCheckIn.addEventListener('change', () => updateUI('check_in'));
    modeCheckOut.addEventListener('change', () => updateUI('check_out'));

    // 2. ON PAGE LOAD: Check LocalStorage
    document.addEventListener("DOMContentLoaded", function() {
        // Get saved mode from storage, or default to 'check_in' if nothing saved
        const savedMode = localStorage.getItem('attendanceScanMode') || 'check_in';

        // Apply the saved mode immediately
        updateUI(savedMode);
    });

    // 3. Scanner Logic
    let lastScanTime = 0;
    const html5QrCode = new Html5Qrcode("reader");

    html5QrCode.start({
            facingMode: "environment"
        }, {
            fps: 10,
            qrbox: {
                width: 260,
                height: 220
            }
        },
        (decodedText) => {
            // Prevent double scans (3 seconds cooldown)
            let now = Date.now();
            if (now - lastScanTime < 3000) return;
            lastScanTime = now;

            // Optional: Vibrate phone on success
            if (navigator.vibrate) navigator.vibrate(200);

            // Submit Form
            document.getElementById("scanDetails").value = decodedText;
            document.getElementById("attendanceForm").submit();
        }
    ).catch(err => {
        document.getElementById('reader').innerHTML = `<div class="text-white p-4 text-center">Camera Permission Needed</div>`;
        console.error(err);
    });
</script>

@endsection