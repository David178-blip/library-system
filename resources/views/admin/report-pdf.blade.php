<!DOCTYPE html>
<html>
<head>
    <title>Library Report</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        h2 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #333; padding: 6px; text-align: left; }
        th { background: #eee; }

        .header-img {
            width: 100%;
            height: auto;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

    <!-- DOMPDF Header Image -->
    <img src="{{ public_path('images/hccd_header.png') }}" class="header-img" >

    <h2>Library Report</h2>
    <p><strong>Date:</strong> {{ now()->format('M d, Y h:i A') }}</p>

    @if(!empty($charts['statusChart']) || !empty($charts['monthlyChart']) || !empty($charts['courseChart']))
        <div style="page-break-after: always;">
            <h3>Analytics Overview</h3>
            <div style="text-align: center; margin-bottom: 20px;">
                @if(!empty($charts['statusChart']))
                    <div style="display: inline-block; width: 45%; margin: 10px; vertical-align: top;">
                        <h4 style="margin-bottom: 5px;">Status Distribution</h4>
                        <img src="{{ $charts['statusChart'] }}" style="width: 100%; border: 1px solid #ddd;">
                    </div>
                @endif

                @if(!empty($charts['courseChart']))
                    <div style="display: inline-block; width: 45%; margin: 10px; vertical-align: top;">
                        <h4 style="margin-bottom: 5px;">Borrows by Course</h4>
                        <img src="{{ $charts['courseChart'] }}" style="width: 100%; border: 1px solid #ddd;">
                    </div>
                @endif
            </div>

            @if(!empty($charts['monthlyChart']))
                <div style="text-align: center; margin-top: 20px;">
                    <h4 style="margin-bottom: 5px;">Monthly Trends</h4>
                    <img src="{{ $charts['monthlyChart'] }}" style="width: 80%; border: 1px solid #ddd;">
                </div>
            @endif
        </div>
    @endif

    <h3>All Borrow Records</h3>
    <table>
        <thead>
            <tr>
                <th>User</th>
                <th>Book</th>
                <th>Accession #</th>
                <th>Course</th>
                <th>Borrowed At</th>
                <th>Due At</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($borrows as $borrow)
                <tr>
                    <td>{{ $borrow->user->name }}</td>
                    <td>{{ $borrow->book->title }}</td>
                    <td>{{ $borrow->accession_number ?? '—' }}</td>
                    <td>{{ $borrow->user->role === 'student' ? $borrow->user->course : '—' }}</td>
                    <td>{{ optional($borrow->borrowed_at)->format('M d, Y') ?? '—' }}</td>
                    <td>{{ optional($borrow->due_at)->format('M d, Y') ?? '—' }}</td>
                    <td>{{ ucfirst($borrow->status) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h3>Overdue Books</h3>
    <table>
        <thead>
            <tr>
                <th>User</th>
                <th>Book</th>
                <th>Course</th>
                <th>Due Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($overdue as $borrow)
                <tr>
                    <td>{{ $borrow->user->name }}</td>
                    <td>{{ $borrow->book->title }}</td>
                    <td>{{ $borrow->user->role === 'student' ? $borrow->user->course : '—' }}</td>
                    <td>{{ optional($borrow->due_at)->format('M d, Y') ?? '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center;">No overdue books 🎉</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <h3>Lost / Missing Copies</h3>
    <table>
        <thead>
            <tr>
                <th>Book</th>
                <th>Accession #</th>
                <th>Course</th>
                <th>User (Who Lost)</th>
                <th>Date Marked Lost</th>
            </tr>
        </thead>
        <tbody>
            @forelse($lost as $lostCopy)
                <tr>
                    <td>{{ $lostCopy->book->title ?? '—' }}</td>
                    <td>{{ $lostCopy->accession_number }}</td>
                    <td>{{ $lostCopy->book->course ?? '—' }}</td>
                    <td>{{ $lostCopy->user->name ?? '—' }}</td>
                    <td>{{ optional($lostCopy->removed_at)->format('M d, Y') ?? '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center;">No lost or removed copies.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
