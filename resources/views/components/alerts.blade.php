@if (session('success') || session('error') || session('info'))
    @php
        $type = session('success') ? 'success' : (session('error') ? 'danger' : 'info');
        $message = session('success') ?? session('error') ?? session('info');

        $config = [
            'success' => ['icon' => 'bi-check-circle-fill', 'color' => 'success', 'title' => 'Success'],
            'danger'  => ['icon' => 'bi-exclamation-octagon-fill', 'color' => 'danger', 'title' => 'Error'],
            'info'    => ['icon' => 'bi-info-circle-fill', 'color' => 'info', 'title' => 'Information'],
        ][$type];
    @endphp

    <div id="flash-message"
         class="position-fixed top-0 end-0 me-4 mt-4 shadow-lg rounded-3 overflow-hidden bg-white animate-slide-in-right"
         style="z-index: 1060; min-width: 300px; max-width: 90vw; border-left: 5px solid var(--bs-{{ $config['color'] }});">
        
        <div class="d-flex align-items-center p-3">
            {{-- Icon --}}
            <div class="flex-shrink-0 text-{{ $config['color'] }}">
                <i class="{{ $config['icon'] }} fs-4"></i>
            </div>
            
            {{-- Content --}}
            <div class="flex-grow-1 ms-3">
                <h6 class="mb-1 fw-bold text-{{ $config['color'] }}">
                    {{ $config['title'] }}
                </h6>
                <p class="mb-0 text-muted small lh-sm">
                    {{ $message }}
                </p>
            </div>

            {{-- Close --}}
            <button type="button" class="btn-close ms-2" onclick="closeFlashMessage()" aria-label="Close"></button>
        </div>

        {{-- Progress Bar --}}
        <div class="progress" style="height: 3px;">
            <div class="progress-bar bg-{{ $config['color'] }}" role="progressbar"
                 style="width: 100%; animation: shrink 4s linear forwards;"></div>
        </div>
    </div>

    <style>
        /* Slide in from right */
        @keyframes slideInRight {
            from { transform: translateX(120%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        .animate-slide-in-right {
            animation: slideInRight 0.5s ease-out forwards;
        }

        /* Progress bar shrink */
        @keyframes shrink {
            from { width: 100%; }
            to { width: 0%; }
        }
    </style>

    <script>
        function closeFlashMessage() {
            const msg = document.getElementById('flash-message');
            if (!msg) return;
            msg.style.transition = 'all 0.5s ease';
            msg.style.opacity = '0';
            msg.style.transform = 'translateX(120%)'; // Slide out to right
            setTimeout(() => msg.remove(), 500);
        }

        // Auto hide after 4 seconds
        setTimeout(closeFlashMessage, 4000);
    </script>
@endif
