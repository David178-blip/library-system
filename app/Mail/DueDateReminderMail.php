<?php

namespace App\Mail;

use App\Models\Borrow;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DueDateReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public Borrow $borrow;
    public int $days; // days until due (negative = overdue)

    public function __construct(Borrow $borrow)
    {
        $this->borrow = $borrow;

        // Difference in days between today and the due date
        $this->days = Carbon::today()->diffInDays(
            $borrow->due_at->startOfDay(),
            false // signed difference: negative if overdue
        );
    }

    public function build()
    {
        if ($this->days === 0) {
            $subject = 'Reminder: Book Due Today';
        } elseif ($this->days === 1) {
            $subject = 'Reminder: Book Due Tomorrow';
        } elseif ($this->days > 1) {
            $subject = "Reminder: Book Due in {$this->days} Days";
        } else {
            $subject = 'Reminder: Book Overdue';
        }

        return $this->subject($subject)
            ->view('emails.due_reminder');
    }
}