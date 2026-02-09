<?php

use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Mail;
use App\Models\Borrow;
use App\Models\Notification as InAppNotification;
use App\Mail\DueDateReminderMail;
use App\Mail\OverdueNoticeMail;
use Carbon\Carbon;

Schedule::call(function () {
    $today    = Carbon::today();
    $tomorrow = (clone $today)->addDay();
    $soonMax  = (clone $today)->addDays(2);

    // 1) Due TODAY
    $dueTodayBorrows = Borrow::with('user', 'book')
        ->whereNull('returned_at')
        ->where('status', 'borrowed')
        ->whereDate('due_at', $today)
        ->get();

    foreach ($dueTodayBorrows as $borrow) {
        if (! $borrow->user) {
            continue;
        }

        // Email reminder (subject/body already dynamic in DueDateReminderMail)
        Mail::to($borrow->user->email)->send(new DueDateReminderMail($borrow));

        // Dynamic phrase for in-app notification
        $days = Carbon::today()->diffInDays(optional($borrow->due_at)->startOfDay(), false);
        if ($days === 0) {
            $whenText = 'today';
        } elseif ($days === 1) {
            $whenText = 'tomorrow';
        } elseif ($days > 1) {
            $whenText = 'in ' . $days . ' days';
        } else {
            $whenText = 'on ' . optional($borrow->due_at)->format('M d, Y');
        }

        // In-app notification so it shows up in the Notifications page
        InAppNotification::firstOrCreate([
            'user_id' => $borrow->user_id,
            'title'   => 'Book Due Today',
            'message' => '"' . ($borrow->book->title ?? 'Unknown book') . '" is due ' . $whenText . '.',
        ]);
    }

    // 2) Due SOON (next 2 days)
    $dueSoonBorrows = Borrow::with('user', 'book')
        ->whereNull('returned_at')
        ->where('status', 'borrowed')
        ->whereDate('due_at', '>=', $tomorrow)
        ->whereDate('due_at', '<=', $soonMax)
        ->get();

    foreach ($dueSoonBorrows as $borrow) {
        if (! $borrow->user) {
            continue;
        }

        // Email reminder
        Mail::to($borrow->user->email)->send(new DueDateReminderMail($borrow));

        // Dynamic phrase for in-app notification
        $days = Carbon::today()->diffInDays(optional($borrow->due_at)->startOfDay(), false);
        if ($days === 0) {
            $whenText = 'today';
        } elseif ($days === 1) {
            $whenText = 'tomorrow';
        } elseif ($days > 1) {
            $whenText = 'in ' . $days . ' days';
        } else {
            $whenText = 'on ' . optional($borrow->due_at)->format('M d, Y');
        }

        // In-app notification so it shows up in the Notifications page
        InAppNotification::firstOrCreate([
            'user_id' => $borrow->user_id,
            'title'   => 'Book Due Soon',
            'message' => '"' . ($borrow->book->title ?? 'Unknown book') . '" is due ' . $whenText . '.',
        ]);
    }

    // 3) OVERDUE: due date has passed and book not yet returned
    $overdueBorrows = Borrow::with('user', 'book')
        ->whereNull('returned_at')
        ->whereIn('status', ['borrowed', 'overdue'])
        ->whereDate('due_at', '<', $today)
        ->get();

    foreach ($overdueBorrows as $borrow) {
        if (! $borrow->user) {
            continue;
        }

        // Mark as overdue (for penalty logic / reporting)
        $borrow->status = 'overdue';
        $borrow->save();

        // Email overdue notice
        Mail::to($borrow->user->email)->send(new OverdueNoticeMail($borrow));

        // In-app notification about possible penalties
        InAppNotification::firstOrCreate([
            'user_id' => $borrow->user_id,
            'title'   => 'Book Overdue',
            'message' => '"' . ($borrow->book->title ?? 'Unknown book') . '" is overdue since ' . optional($borrow->due_at)->format('M d, Y') . '. Penalties may apply.',
        ]);
    }
})->everyMinute();  // Runs every minute (testing)
