<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Borrow;
use App\Mail\DueDateReminderMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

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

        foreach ($borrows as $borrow) {
            Mail::to($borrow->user->email)->send(new DueDateReminderMail($borrow));
            $this->info("Reminder sent to " . $borrow->user->email);
        }

        return 0;
    }
}
