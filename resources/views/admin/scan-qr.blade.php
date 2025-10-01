@extends('layouts.app')

@section('content')
<div class="container text-center">
    <h2 class="mb-4">📷 Scan Student/Faculty QR</h2>
    <div id="qr-reader" style="width: 400px; margin: auto;"></div>
    <div class="mt-3">
        <h5>Scanned Result:</h5>
        <p id="qr-result" class="fw-bold text-success"></p>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
function onScanSuccess(decodedText, decodedResult) {
    // Show scanned data
    document.getElementById('qr-result').innerText = decodedText;

    // Redirect to user profile (or auto-borrow form)
    window.location.href = "/admin/user/" + decodedText + "/borrow";
}

function onScanFailure(error) {
    // Ignore scan errors (happens when no QR is detected)
    console.warn(`QR error = ${error}`);
}

let html5QrcodeScanner = new Html5QrcodeScanner(
    "qr-reader", { fps: 10, qrbox: 250 }
);
html5QrcodeScanner.render(onScanSuccess, onScanFailure);
</script>
@endsection
