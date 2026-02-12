@extends('layouts.app')

@section('content')
<div class="container d-flex flex-column align-items-center justify-content-center" style="min-height: 80vh;">
    <div class="card shadow-lg text-center" style="max-width: 400px; width: 100%;">
        <div class="card-body">
            <h3 class="mb-3">Your QR Code</h3>
            <p class="text-muted">Scan this QR to identify yourself in the library system.</p>
            <div class="d-flex justify-content-center" id="qr-code-container">
                {!! $qr !!}
            </div>
            <p class="mt-3"><strong>{{ $user->name }}</strong></p>
            <p class="text-muted">{{ $user->email }}</p>
            
            <button id="download-qr" class="btn btn-primary mt-3">
                <i class="bi bi-download me-2"></i>Download QR Code
            </button>
        </div>
    </div>
</div>

<script>
    document.getElementById('download-qr').addEventListener('click', function() {
        const svg = document.querySelector('#qr-code-container svg');
        if (!svg) return;

        const svgData = new XMLSerializer().serializeToString(svg);
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        const img = new Image();

        img.onload = function() {
            canvas.width = img.width * 2;
            canvas.height = img.height * 2;
            ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
            const pngFile = canvas.toDataURL("image/png");
            
            const downloadLink = document.createElement("a");
            downloadLink.download = "QR_Code_{{ str_replace(' ', '_', $user->name) }}.png";
            downloadLink.href = pngFile;
            downloadLink.click();
        };

        img.src = 'data:image/svg+xml;base64,' + btoa(svgData);
    });
</script>
@endsection
