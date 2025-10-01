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
    </style>
</head>
<body>
    <h2>📊 Library Report</h2>
    <p><strong>Date:</strong> {{ now()->format('M d, Y h:i A') }}</p>

    <h3>All Borrow Records</h3>
    <table>
        <thead>
            <tr>
                <th>User</th>
                <th>Book</th>
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
                    <td>{{ $borrow->borrowed_at->format('M d, Y') }}</td>
                    <td>{{ $borrow->due_at->format('M d, Y') }}</td>
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
                <th>Due Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($overdue as $borrow)
                <tr>
                    <td>{{ $borrow->user->name }}</td>
                    <td>{{ $borrow->book->title }}</td>
                    <td>{{ $borrow->due_at->format('M d, Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" style="text-align: center;">No overdue books 🎉</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
