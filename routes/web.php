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
use App\Models\Book;

Route::get('/', function () {
    $books = Book::all();   // fetch all books
    return view('welcome', compact('books'));
});

// Laravel default auth routes
Auth::routes();

// =========================
// Role-based Dashboards
// =========================
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
});

Route::middleware(['auth', 'role:student'])->group(function () {
    Route::get('/student/dashboard', [StudentController::class, 'dashboard'])->name('student.dashboard');
});

Route::middleware(['auth', 'role:faculty'])->group(function () {
    Route::get('/faculty/dashboard', [FacultyController::class, 'dashboard'])->name('faculty.dashboard');
});

// =========================
// Books Routes
// =========================

// All users (view only)
Route::middleware('auth')->group(function () {
    Route::get('books', [BookController::class, 'index'])->name('books.index');
    Route::get('books/{book}', [BookController::class, 'show'])->name('books.show'); // optional
});

// Admin-only (create/edit/delete)
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('books/create', [BookController::class, 'create'])->name('books.create');
    Route::post('books', [BookController::class, 'store'])->name('books.store');
    Route::get('books/{book}/edit', [BookController::class, 'edit'])->name('books.edit');
    Route::put('books/{book}', [BookController::class, 'update'])->name('books.update');
    Route::delete('books/{book}', [BookController::class, 'destroy'])->name('books.destroy');
});

// =========================
// Borrow Routes
// =========================
Route::middleware('auth')->group(function () {
    Route::get('books/{book}/borrow', [BorrowController::class, 'create'])->name('books.borrow');
    Route::post('books/{book}/borrow', [BorrowController::class, 'store'])->name('books.borrow.store');
    Route::post('borrows/{borrow}/return', [BorrowController::class, 'return'])->name('borrows.return');

    Route::get('profile', [UserProfileController::class, 'index'])->name('profile');
    Route::get('profile/qr', [UserProfileController::class, 'qr'])->name('profile.qr');

    // AI Chat
    Route::post('ai/chat', [ChatbotController::class, 'chat'])->name('ai.chat');
});

// Laravel’s default home route
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Auth scaffolding routes (if using Breeze/Jetstream)
require __DIR__.'/auth.php';
