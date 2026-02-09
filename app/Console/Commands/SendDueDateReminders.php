<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Borrow;
use App\Models\Notification;
use App\Models\EmailLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\DueDateReminderMail;
use App\Mail\OverdueNoticeMail;

class SendDueDateReminders extends Command
{
    protected $signature = 'overdues:check';
    protected $description = 'Mark borrows overdue and notify users';

    public function handle()
    {
        $today = Carbon::today();

        // =============================
        // 1) Reminders 1–2 days before due date (once per day)
        // =============================
        $minReminderDate = $today->copy()->addDay();      // 1 day before
        $maxReminderDate = $today->copy()->addDays(2);    // 2 days before

        $dueSoon = Borrow::with('user', 'book')
            ->whereNull('returned_at')
            ->where('status', 'borrowed')
            ->whereDate('due_at', '>=', $minReminderDate)
            ->whereDate('due_at', '<=', $maxReminderDate)
            ->get();

        foreach ($dueSoon as $borrow) {
            if (! $borrow->user || ! $borrow->book) {
                continue;
            }

            // Days left from "today" until due date (1 or 2)
            $daysLeft = $today->diffInDays($borrow->due_at->copy()->startOfDay());
            if ($daysLeft < 1 || $daysLeft > 2) {
                continue; // safety guard
            }
            $daysText = $daysLeft === 1 ? '1 day' : $daysLeft . ' days';

            // Avoid sending multiple times per day for the same user/book
            $alreadyLogged = EmailLog::whereDate('sent_at', $today)
                ->where('user_id', $borrow->user_id)
                ->where('book_title', $borrow->book->title)
                ->where('type', 'due_soon')
                ->exists();

            if ($alreadyLogged) {
                continue;
            }

            // In-app notification (disable auto-email to prevent duplicate)
            Notification::$disableAutoEmail = true;
            Notification::create([
                'user_id' => $borrow->user_id,
                'title'   => 'Book Due Soon',
                'message' => 'Your borrowed book "' . $borrow->book->title . '" is due on ' . $borrow->due_at->format('M d, Y') . ' (' . $daysText . ' from now).',
            ]);
            Notification::$disableAutoEmail = false;

            // Email notification (due soon)
            Mail::to($borrow->user->email)->send(new DueDateReminderMail($borrow));

            // Log email send
            $log = new EmailLog();
            $log->user_id    = $borrow->user_id;
            $log->type       = 'due_soon';
            $log->book_title = $borrow->book->title;
            $log->sent_at    = now();
            $log->save();

            $this->info("Sent due-soon reminder for borrow_id={$borrow->id} ({$daysText} left)");
        }

        // =============================
        // 2) Reminders when book is due today
        // =============================
        $dueToday = Borrow::with('user', 'book')
            ->whereNull('returned_at')
            ->where('status', 'borrowed')
            ->whereDate('due_at', $today)
            ->get();

        foreach ($dueToday as $borrow) {
            if (! $borrow->user || ! $borrow->book) {
                continue;
            }

            $alreadyLogged = EmailLog::whereDate('sent_at', $today)
                ->where('user_id', $borrow->user_id)
                ->where('book_title', $borrow->book->title)
                ->where('type', 'due_today')
                ->exists();

            if ($alreadyLogged) {
                continue;
            }

            // In-app notification (disable auto-email to prevent duplicate)
            Notification::$disableAutoEmail = true;
            Notification::create([
                'user_id' => $borrow->user_id,
                'title'   => 'Book Due Today',
                'message' => 'Your borrowed book "' . $borrow->book->title . '" is due today (' . $borrow->due_at->format('M d, Y') . ').',
            ]);
            Notification::$disableAutoEmail = false;

            // Email notification (due today)
            Mail::to($borrow->user->email)->send(new DueDateReminderMail($borrow));

            $log = new EmailLog();
            $log->user_id    = $borrow->user_id;
            $log->type       = 'due_today';
            $log->book_title = $borrow->book->title;
            $log->sent_at    = now();
            $log->save();

            $this->info("Sent due-today reminder for borrow_id={$borrow->id}");
        }

        // =============================
        // 3) Mark overdue and notify (optional but keeps status accurate)
        // =============================
        $overdueBorrows = Borrow::with('user', 'book')
            ->whereNull('returned_at')
            ->where('status', 'borrowed')
            ->whereDate('due_at', '<', $today)
            ->get();

        foreach ($overdueBorrows as $borrow) {
            try {
                $borrow->status = 'overdue';
                $borrow->save();

                if ($borrow->user && $borrow->book) {
                    // In-app notification (disable auto-email since we send OverdueNoticeMail)
                    Notification::$disableAutoEmail = true;
                    Notification::create([
                        'user_id' => $borrow->user_id,
                        'title'   => 'Book Overdue',
                        'message' => 'Your borrowed book "' . $borrow->book->title . '" is overdue. Please return it as soon as possible.',
                    ]);
                    Notification::$disableAutoEmail = false;

                    // Only send overdue email once per day per user/book
                    $alreadyLogged = EmailLog::whereDate('sent_at', $today)
                        ->where('user_id', $borrow->user_id)
                        ->where('book_title', $borrow->book->title)
                        ->where('type', 'overdue')
                        ->exists();

                    if (! $alreadyLogged) {
                        Mail::to($borrow->user->email)->send(new OverdueNoticeMail($borrow));

                        $log = new EmailLog();
                        $log->user_id    = $borrow->user_id;
                        $log->type       = 'overdue';
                        $log->book_title = $borrow->book->title;
                        $log->sent_at    = now();
                        $log->save();
                    }
                }

                $this->info("Marked overdue: borrow_id={$borrow->id}");
            } catch (\Throwable $e) {
                Log::error("Overdue marking failed for borrow {$borrow->id}: " . $e->getMessage());
            }
        }

        $this->info('Due date reminder and overdue check completed.');
    }
}
