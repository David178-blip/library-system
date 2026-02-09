<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Library Due Date Reminder</title>
</head>
<body>
@php
    use Carbon\Carbon;

    $today = Carbon::today();
    $due   = $borrow->due_at->copy()->startOfDay();

    // Overdue strictly means due date is BEFORE today
    $isOverdue = $due->lt($today);

    // For non-overdue items, days left = absolute diff between today and due
    $daysLeft = $isOverdue ? 0 : $today->diffInDays($due);
@endphp

<h2>
    @if ($isOverdue)
        📚 Book Overdue
    @elseif ($daysLeft === 0)
        📚 Book Due Today
    @elseif ($daysLeft === 1)
        📚 Book Due Tomorrow
    @else
        📚 Book Due in {{ $daysLeft }} Days
    @endif
</h2>

<p>Hello {{ $borrow->user->name }},</p>

@if ($isOverdue)
    <p>This book is already <strong>overdue</strong>:</p>
@elseif ($daysLeft === 0)
    <p>This is a reminder that your borrowed book is due <strong>today</strong>:</p>
@elseif ($daysLeft === 1)
    <p>This is a reminder that your borrowed book will be due <strong>tomorrow</strong>:</p>
@else
    <p>This is a reminder that your borrowed book will be due in <strong>{{ $daysLeft }} days</strong>:</p>
@endif

<strong>{{ $borrow->book->title }}</strong><br>
Due on: <strong>{{ $borrow->due_at->format('M d, Y') }}</strong>

<p>Please return it on time to avoid penalties.</p>
<p>— Library Team</p>

</body>
</html>