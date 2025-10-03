<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BorrowController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\FacultyController;
use App\Http\Controllers\UserController;
use App\Models\Book;
use App\Models\Borrow;
use App\Models\User;
use App\Http\Controllers\NotificationController;

// =========================
// Public Routes
// =========================
Route::get('/', function () {
    $books = Book::all();   // fetch all books
    return view('welcome', compact('books'));
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // QR Scanner
    Route::get('/scan-qr', function () {
        return view('admin.scan-qr');
    })->name('scan-qr');

    // Return book after scanning QR
    Route::get('/admin/return/{user}', [BorrowController::class, 'showReturnForm'])
         ->name('admin.return.form');
         
    Route::post('/admin/return-book/{borrow}', [BorrowController::class, 'markReturned'])
         ->name('admin.return.book');

    // Assign book after scanning QR
    Route::get('/user/{user}/borrow', [BorrowController::class, 'assign'])->name('assign.borrow');
    Route::post('/user/{user}/borrow', [BorrowController::class, 'store'])->name('borrow.store');

    // Books (CRUD only for admin)
    Route::resource('books', BookController::class);

    // Reports
    Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
    Route::get('/reports/download', [AdminController::class, 'downloadReport'])->name('reports.download');

    // User management
    Route::resource('users', UserController::class);

    // Borrow approval ✅ FIXED
    Route::get('/borrows/requests', [AdminController::class, 'borrowRequests'])->name('borrows.requests');
    Route::post('/borrows/{borrow}/approve', [AdminController::class, 'approveBorrow'])->name('borrows.approve');
    Route::post('/borrows/{borrow}/reject', [AdminController::class, 'rejectBorrow'])->name('borrows.reject');
});


// =========================
// Student Routes
// =========================
Route::middleware(['auth', 'role:student'])->prefix('student')->group(function () {
    Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('student.dashboard');
});

// =========================
// Faculty Routes
// =========================
Route::middleware(['auth', 'role:faculty'])->prefix('faculty')->group(function () {
    Route::get('/dashboard', [FacultyController::class, 'dashboard'])->name('faculty.dashboard');
});

// =========================
// Shared Routes (all logged-in users)
// =========================
Route::middleware('auth')->group(function () {

        // Book Search
Route::get('books/search', [BookController::class, 'search'])->name('books.search');

    // View books
    Route::get('books', [BookController::class, 'index'])->name('books.index');
    Route::get('books/{book}', [BookController::class, 'show'])->name('books.show');

    // Borrowing
    Route::get('books/{book}/borrow', [BorrowController::class, 'create'])->name('books.borrow');
    Route::post('books/{book}/borrow', [BorrowController::class, 'store'])->name('books.borrow.store');
    Route::post('borrows/{borrow}/return', [BorrowController::class, 'return'])->name('borrows.return');

    // User Profile
    Route::get('profile', [UserProfileController::class, 'index'])->name('profile');
    Route::get('profile/qr', [UserProfileController::class, 'qr'])->name('profile.qr');
    Route::get('profile/{user}', [UserProfileController::class, 'show'])->name('profile.show');

    // AI Chat
    Route::post('ai/chat', [ChatbotController::class, 'chat'])->name('ai.chat');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');



});

Route::get('/dashboard', function () {
    if (auth()->check()) {
        $role = auth()->user()->role;
        if ($role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($role === 'faculty') {
            return redirect()->route('faculty.dashboard');
        } elseif ($role === 'student') {
            return redirect()->route('student.dashboard');
        }
    }
    return redirect('/');
});


// =========================
// Default Home
// =========================
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Keep if using Laravel Breeze/Jetstream
require __DIR__.'/auth.php';
