<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Library Due Date Reminder</title>
</head>
<body>
<h2>📚 Book Due Reminder</h2>
<p>Hello {{ $borrow->user->name }},</p>
<p>This is a reminder that your borrowed book:</p>

<strong>{{ $borrow->book->title }}</strong><br>
Due on: <strong>{{ $borrow->due_at->format('M d, Y') }}</strong>

<p>Please return it on time to avoid penalties.</p>
<p>— Library Team</p>

</body>
</html>
