<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Attendance Report</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        h2 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #333; padding: 6px; text-align: left; }
        th { background: #eee; }
    </style>
</head>
<body>
    <img src="{{ public_path('images/hccd_header.png') }}" class="header-img" style="width:100%; max-height:100px;">
    <h2> Attendance Report</h2>
    <p><strong>Date:</strong> {{ now()->format('M d, Y h:i A') }}</p>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Role</th>
                <th>Course</th>
                <th>Time In</th>
                <th>Time Out</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $index => $rec)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $rec->user->name }}</td>
                    <td>{{ ucfirst($rec->user->role) }}</td>
                    <td>{{ $rec->user->course ?? '-' }}</td>
                    <td>{{ $rec->time_in ?? '-' }}</td>
                    <td>{{ $rec->time_out ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
