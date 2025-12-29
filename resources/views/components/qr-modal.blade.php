@props(['id', 'name', 'qr_code'])
<div class="modal fade qr-code" id="qrModal{{$id}}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-3 text-center">
            <div class="modal-header">
                <h5 class="modal-title">QR Code for {{$name}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="qr-{{ $id }}">
                    {!! QrCode::size(200)->generate($qr_code) !!}
                    <p class="mt-2"><strong>Name: </strong>{{ $name }}</p>
                </div>
            </div>
            <button class="btn btn-primary mt-3 print-qr-btn" data-qr-id="qr-{{ $id }}">
                Print QR
            </button>
        </div>
    </div>
</div>
