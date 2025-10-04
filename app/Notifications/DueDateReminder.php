<?php
namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Messages\MailMessage;

class DueDateReminder extends Notification
{
    protected $borrow;
    public function __construct($borrow) { $this->borrow = $borrow; }

    public function via($notifiable)
    {
        // database is the in-app type; add 'mail' if you configured mail
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'borrow_id' => $this->borrow->id,
            'book_title'=> $this->borrow->book->title ?? 'Unknown',
            'due_at'    => $this->borrow->due_at?->toDateTimeString(),
            'message'   => 'Your borrowed book is overdue. Please return it as soon as possible.'
        ];
    }

    // optional mail representation:
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Overdue book reminder')
            ->line("Your borrowed book \"{$this->borrow->book->title}\" is overdue (due {$this->borrow->due_at}).")
            ->action('View Profile', url('/profile'))
            ->line('Please return it or contact the library.');
    }
}
