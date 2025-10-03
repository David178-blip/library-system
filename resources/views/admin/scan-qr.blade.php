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
    document.getElementById('qr-result').innerText = decodedText;

    // ✅ Extract numeric ID from scanned text (e.g., "USER:3 - Student")
    const userIdMatch = decodedText.match(/\d+/); 
    if (userIdMatch) {
        const userId = userIdMatch[0];
        // ✅ Redirect to the return form route
        window.location.href = "/admin/return/" + userId;
    } else {
        alert("Invalid QR format. No user ID found.");
    }
}

function onScanFailure(error) {
    console.warn(`QR error = ${error}`);
}

let html5QrcodeScanner = new Html5QrcodeScanner(
    "qr-reader", { fps: 10, qrbox: 250 }
);
html5QrcodeScanner.render(onScanSuccess, onScanFailure);
</script>

@endsection
