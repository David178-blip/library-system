<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Page Not Found — {{ config('app.name', 'Library System') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light min-vh-100 d-flex align-items-center justify-content-center p-3">
    <div class="text-center">
        <div class="mb-4">
            <img src="{{ asset('images/hccd_logo.png') }}" alt="Logo" width="64" height="64">
        </div>
        <i class="bi bi-search text-muted display-1"></i>
        <h1 class="mt-3 fw-bold text-primary">Page not found</h1>
        <p class="lead text-muted">The page you're looking for doesn't exist or was moved.</p>
        <a href="{{ url('/') }}" class="btn btn-primary rounded-3 px-4">
            <i class="bi bi-house me-2"></i>Back to home
        </a>
    </div>
</body>
</html>
