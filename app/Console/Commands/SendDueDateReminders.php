<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Borrow;
use App\Mail\DueDateReminderMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\Mail\OverdueNoticeMail;
use App\Models\EmailLog;

class SendDueDateReminders extends Command
{
    protected $signature = 'library:send-reminders';
    protected $description = 'Send due date reminders to users';

    public function handle()
    {
        $tomorrow = Carbon::tomorrow();

        $borrows = Borrow::where('status', 'borrowed')
            ->whereDate('due_at', $tomorrow)
            ->with('user', 'book')
            ->get();

        $reminders = $borrows->filter(function ($borrow) use ($tomorrow) {
            return Carbon::parse($borrow->due_at)->isSameDay($tomorrow);
        });
foreach ($reminders as $borrow) {
    if ($borrow->user && $borrow->user->email) {
        Mail::to($borrow->user->email)->send(new DueDateReminderMail($borrow));

        EmailLog::create([
            'user_id' => $borrow->user->id,
            'type' => 'Reminder',
            'book_title' => $borrow->book->title,
        ]);

        $this->info("Reminder sent to {$borrow->user->email} for book: {$borrow->book->title}");
    }
}

        $overdues = Borrow::where('status', 'borrowed')
            ->whereDate('due_at', '<', Carbon::today())
            ->with('user', 'book')
            ->get();
foreach ($overdues as $borrow) {
    if ($borrow->user && $borrow->user->email) {
        Mail::to($borrow->user->email)->send(new OverdueNoticeMail($borrow));

        EmailLog::create([
            'user_id' => $borrow->user->id,
            'type' => 'Overdue',
            'book_title' => $borrow->book->title,
        ]);

        $this->info("Overdue notice sent to {$borrow->user->email} for book: {$borrow->book->title}");
    }

    $borrow->update(['status' => 'overdue']);
}
 

        return 0;
    }
}
