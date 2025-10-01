<h2>⚠️ Overdue Book Notice</h2>
<p>Hello {{ $borrow->user->name }},</p>
<p>Our records show that the following book is overdue:</p>

<strong>{{ $borrow->book->title }}</strong><br>
Was due on: <strong>{{ $borrow->due_at->format('M d, Y') }}</strong>

<p>Please return it immediately to avoid further penalties.</p>
<p>— Library Team</p>
