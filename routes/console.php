<?php

use Illuminate\Support\Facades\Schedule;
use App\Models\Borrow;
use App\Notifications\DueDateReminder;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BookController;

// Define a scheduled task
Schedule::call(function () {
    $tomorrow = Carbon::today()->addDay();

    $borrows = Borrow::with('user','book')
        ->whereNull('returned_at')
        ->whereDate('due_at', $tomorrow->toDateString())
        ->get();

    foreach ($borrows as $b) {
        $b->user->notify(new DueDateReminder($b));
    }

    // Mark overdue
    $overdue = Borrow::whereNull('returned_at')
        ->whereDate('due_at', '<', Carbon::today()->toDateString())
        ->get();

    foreach ($overdue as $o) {
        $o->update(['status'=>'overdue']);
    }
    
})->dailyAt('08:00');

Route::middleware(['auth','role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::resource('/books', BookController::class);
});

