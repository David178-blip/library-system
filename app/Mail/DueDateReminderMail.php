<?php

namespace App\Mail;

use App\Models\Borrow;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DueDateReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $borrow;

    public function __construct(Borrow $borrow)
    {
        $this->borrow = $borrow;
    }

    public function build()
    {
        return $this->subject('Library Due Date Reminder')
            ->view('emails.due_date_reminder');
    }
}
