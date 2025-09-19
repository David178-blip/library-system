<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Library Due Date Reminder</title>
</head>
<body>
    <h2>Hello {{ $borrow->user->name }},</h2>
    <p>This is a reminder that the book <b>{{ $borrow->book->title }}</b> 
       you borrowed from the library is due on:</p>

    <p><b>{{ $borrow->due_at->format('F j, Y') }}</b></p>

    <p>Please make sure to return the book on or before the due date to avoid penalties.</p>

    <p>Thank you,<br>Library Management System</p>
</body>
</html>
