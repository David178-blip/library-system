<?php

use Illuminate\Support\Facades\Schedule;
use App\Models\Borrow;
use App\Models\User;
use App\Notifications\DueDateReminder;
use Carbon\Carbon;

Schedule::call(function () {
    // Find overdue borrowed books
    $overdueBorrows = Borrow::where('status', 'borrowed')
        ->where('due_at', '<', Carbon::now())
        ->get();

    foreach ($overdueBorrows as $borrow) {
        $user = $borrow->user;

        // Send a notification to the user
        if ($user) {
            $user->notify(new DueDateReminder($borrow));
        }
    }
})->dailyAt('08:00');  // Runs every day at 8:00 AM
