<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Borrow;

class DueDateReminder extends Notification implements ShouldQueue
{
    use Queueable;

    protected $borrow;

    public function __construct(Borrow $borrow)
    {
        $this->borrow = $borrow;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Library Due Date Reminder')
            ->greeting('Hello ' . $notifiable->name)
            ->line("This is a reminder that the book **{$this->borrow->book->title}** you borrowed is due on **{$this->borrow->due_at->toFormattedDateString()}**.")
            ->line('Please make sure to return it on time to avoid overdue penalties.')
            ->action('View Your Profile', url('/profile'))
            ->line('Thank you for using the library system!');
    }
}
