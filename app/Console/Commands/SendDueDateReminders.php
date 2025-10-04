<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Borrow;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\DueDateReminderMail; // if you want email
use App\Notifications\OverdueNotification; // if you want in-app

class SendDueDateReminders extends Command
{
    protected $signature = 'overdues:check';
    protected $description = 'Mark borrows overdue and notify users';

    public function handle()
    {
        $today = Carbon::today();

        // Select borrows that are not returned, currently 'borrowed' and due before today
        $borrows = Borrow::with('user','book')
            ->whereNull('returned_at')
            ->where('status', 'borrowed')
            ->whereDate('due_at', '<', $today)
            ->get();

        foreach ($borrows as $borrow) {
            try {
                $borrow->status = 'overdue';
                $borrow->save();

                // Send in-app notification (database)
                if ($borrow->user) {
                    $borrow->user->notify(new SendDueDateReminders($borrow));
                }

                // Optional: send email if you use mailer
                // Mail::to($borrow->user->email)->send(new DueDateReminderMail($borrow));

                $this->info("Marked overdue: borrow_id={$borrow->id}");
            } catch (\Throwable $e) {
                Log::error("Overdue marking failed for borrow {$borrow->id}: ".$e->getMessage());
            }
        }

        $this->info('Overdue check completed.');
    }
}
