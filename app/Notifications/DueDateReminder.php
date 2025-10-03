<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Borrow;

class DueDateReminder extends Notification
{
    use Queueable;

    public $borrow;

    public function __construct(Borrow $borrow)
    {
        $this->borrow = $borrow;
    }

    public function via($notifiable)
    {
        return ['mail']; // Or 'database' if in-app only
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Overdue Book Reminder')
            ->line('The book "' . $this->borrow->book->title . '" is overdue.')
            ->line('Please return or renew it as soon as possible.')
            ->line('Thank you!');
    }
}
