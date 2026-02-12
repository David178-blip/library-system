@extends('layouts.app')

@section('content')
<div class="container text-center">
    <h2 class="mb-4">📷 Scan Student/Faculty QR</h2>
    <div id="qr-reader" style="width: 400px; margin: auto;"></div>
    
    <div class="mt-4 p-3 border rounded bg-light" style="max-width: 400px; margin: auto;">
        <h5><i class="bi bi-upload me-2"></i>Upload QR Image</h5>
        <p class="text-muted small">If the camera isn't available, you can upload a photo of the QR code.</p>
        <input type="file" id="qr-input-file" accept="image/*" class="form-control">
    </div>

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

// ✅ Handle Image Upload
const fileInput = document.getElementById('qr-input-file');
fileInput.addEventListener('change', e => {
    if (e.target.files.length === 0) {
        return;
    }

    const imageFile = e.target.files[0];
    const html5QrCode = new Html5Qrcode("qr-reader");
    
    html5QrCode.scanFile(imageFile, true)
        .then(decodedText => {
            onScanSuccess(decodedText);
        })
        .catch(err => {
            alert("Error scanning file: " + err);
        });
});
</script>

@endsection
