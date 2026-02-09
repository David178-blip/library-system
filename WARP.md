# WARP.md

This file provides guidance to WARP (warp.dev) when working with code in this repository.

## Tech stack overview

- Laravel 12 application targeting PHP 8.2+ (`composer.json`), using the standard MVC structure (`app`, `routes`, `resources`, `database`).
- MySQL relational database for core data (books, borrows, users, attendance, notifications).
- Front-end assets built with Vite and Tailwind via the `laravel-vite-plugin` (`package.json`, `vite.config.js`, `tailwind.config.js`).
- Pest (with PHPUnit under the hood) for automated tests (`tests/Pest.php`, `phpunit.xml`).
- PDF generation via `barryvdh/laravel-dompdf` and QR code utilities via `simplesoftwareio/simple-qrcode`.
- Separate Node.js/Express chatbot service in `server.js` that talks directly to MySQL and OpenAI.

## Common commands

### Install dependencies

- PHP dependencies:
  - `composer install`
- Node dependencies (Vite + Node chatbot):
  - `npm install`

### Local development

- Full Laravel dev stack (HTTP server, queue listener, log viewer, Vite dev server) via Composer script:
  - `composer dev`
  - This runs `php artisan serve`, `php artisan queue:listen --tries=1`, `php artisan pail --timeout=0`, and `npm run dev` concurrently.

- Run just the Laravel HTTP server:
  - `php artisan serve`

- Run only the Vite dev server for front-end assets:
  - `npm run dev`

- Start the standalone Node.js chatbot API defined in `server.js` (Express on port 3000 by default):
  - `npm start`

### Builds

- Build production front-end assets with Vite:
  - `npm run build`

### Tests

- Run the full backend test suite (Pest via `php artisan test`):
  - `composer test`

- Run a specific test file:
  - `php artisan test tests/Feature/ExampleTest.php`

- Run tests matching a specific name (useful while iterating on one test case):
  - `php artisan test --filter=AttendanceTest`

### Linting / formatting

- PHP code style / formatting using Laravel Pint (installed as a dev dependency):
  - `php vendor/bin/pint`

## Laravel application architecture

### Routing and entry points

- `routes/web.php` is the main HTTP entry point:
  - Public root `/` renders `welcome` with all books (`Book::all()`).
  - Admin routes are grouped under the `admin` prefix with `auth` and `role:admin` middleware (e.g., `/admin/dashboard`, `/admin/books`, `/admin/borrows/requests`).
  - Student routes are grouped under `student` with `role:student` and provide a student dashboard.
  - Faculty routes are grouped under `faculty` with `role:faculty` and provide a faculty dashboard.
  - Shared authenticated routes include:
    - Book catalog and detail (`/books`, `/books/{book}`, `books.search`).
    - Borrowing endpoints for creating and returning borrows.
    - User profile and QR code endpoints.
    - AI chat endpoint at `POST /ai/chat` handled by `ChatbotController@chat`.
    - Attendance routes (`/attendance`, `/attendance/time-in`, `/attendance/time-out`).
  - A `/dashboard` route redirects authenticated users to an appropriate dashboard based on their `role` (`admin`, `faculty`, `student`).
  - `routes/auth.php` contains the standard Laravel authentication, password reset, and email verification routes.

- `routes/console.php` defines a scheduled task that runs daily at 08:00 to find overdue `Borrow` records and dispatch `DueDateReminder` notifications.

### Core domain models

- `App\Models\Book`
  - Represents a library book (`title`, `author`, `year`, `copies`).
  - Defines a `hasMany` relationship to `Borrow` records (`borrows()`).

- `App\Models\Borrow`
  - Represents a borrowing record linking a `user` to a `book`.
  - Tracks status/approval lifecycle: `status`, `approval`, `borrowed_at`, `due_at`, `returned_at` (cast to `datetime`).
  - Belongs to both `Book` and `User`.

- `App\Models\Attendance`
  - Stores `time_in` / `time_out` per user in an `attendance` table.
  - Belongs to `User`.

- `App\Models\Notification`
  - Simple Eloquent model used for in-app notifications with fields like `user_id`, `title`, `message`, `is_read`.

- `App\Models\EmailLog` and the standard `App\Models\User` model support ancillary features (logging, user metadata like `role` and `course`).

### Controllers and main flows

- `App\Http\Controllers\BookController`
  - Handles the searchable/paginatable book catalog (filtering by title, author, year, course) and exposes a `books.index` view.
  - Computes `topBooks` via `Book::withCount('borrows')` to show most borrowed titles.
  - Provides admin CRUD for books (`create`, `store`, `edit`, `update`, `destroy`) and a dedicated `search` endpoint for simpler keyword searches.

- `App\Http\Controllers\BorrowController`
  - Student/faculty self-service borrowing:
    - `create(Book $book)` renders the borrow form.
    - `store(Request $request, Book $book)` creates a `Borrow` with `status`/`approval` set to `pending` (no inventory changes yet).
  - Admin-driven assignment after QR scan:
    - `assign($userId)` lets admins select a book for a scanned user.
    - `storeForUser(Request $request, $userId)` creates a `Borrow` with immediate `borrowed_at`/`due_at`, decrements `Book.copies` and redirects back to the admin dashboard.
  - Returning books:
    - `return(Borrow $borrow)` and `markReturned($borrowId)` set `returned_at`, mark `status` as `returned`, and increment `Book.copies` to keep inventory in sync.

- `App\Http\Controllers\AdminController`
  - `dashboard()` aggregates counts for books, users, borrows, overdue items, and recent borrows and pulls recent `Notification` records, then renders `admin.dashboard`.
  - `reports()` builds a filterable view over `Borrow` records (filtering by user name, book title, status, and date range) and a separate overdue subset.
  - `downloadReport()` applies the same filters and generates a PDF via `PDF::loadView('admin.report-pdf', ...)`.
  - `borrowRequests()`, `approveBorrow()`, and `rejectBorrow()` manage pending borrow approvals, update `Borrow` status/approval fields, adjust book copies, and persist in-app `Notification` objects to inform users.

- `App\Http\Controllers\AttendanceController`
  - `index()` shows the current user’s active attendance (if any) and renders the main attendance page.
  - `timeIn()` and `timeOut()` create or close an `Attendance` record while preventing duplicate open sessions.
  - `report()` provides a filterable, paginated attendance report (by user, role, course, and date range).
  - `downloadReport()` mirrors the filters and generates a PDF report for the selected range.

- `App\Http\Controllers\FacultyController`, `StudentController`, `UserProfileController`, `NotificationController`, and `UserController`
  - Implement role-specific dashboards, user profile/QR views, notification listing, and admin user management. These controllers build on the `Book`, `Borrow`, `Attendance`, and `Notification` models and the route groups described above.

### Views

- `resources/views` is organized by feature area:
  - `admin/*` contains dashboards, book and borrow management screens, QR scan/return flows, and report/report-pdf templates.
  - `attendance/*` contains the attendance screen and the report/report-pdf templates used by `AttendanceController`.
  - `books/*` and `borrows/*` contain the catalog, detail, search results, and borrow creation templates.
  - `auth/*` contains the authentication scaffolding views.
  - `faculty/*` plus `layouts/*` define role-specific dashboards and shared layout/navigation components.

### Helpers

- `app/Helpers/RedirectHelper.php` defines a global `redirectToDashboard()` function that centralizes role-based redirects to the correct dashboard and enforces logout on unknown roles. This is the preferred place to extend/adjust dashboard routing logic.

## AI chatbot components

### Laravel chatbot controller

- `App\Http\Controllers\ChatbotController`
  - Exposes a `chat(Request $request)` method used by the `POST /ai/chat` route.
  - Uses `GuzzleHttp\Client` to call the OpenAI Chat Completions API with a simple system prompt ("You are a helpful library assistant.") and the user’s message.
  - Reads the `OPENAI_API_KEY` from the Laravel `.env` file and returns JSON with a `reply` string.

### Node.js/Express chatbot service (`server.js`)

- `server.js` defines an Express app that serves a single `POST /chat` JSON endpoint and connects directly to MySQL using `mysql2/promise`:
  - Reads database connection settings from `DB_HOST`, `DB_USERNAME`, `DB_PASSWORD`, `DB_DATABASE` and `OPENAI_API_KEY` from environment variables.
  - Maintains an in-memory `userMemory.lastResults` array to support numeric selection of previously returned search results.
  - Attempts to interpret the incoming `message` in stages:
    - Numeric input selects from the last search results.
    - Free-text input triggers a `books` table search (title/author) to build a formatted list of matches.
    - Non book-related chat is explicitly rejected with a fixed message.
    - For book-related questions, it loads the full catalog from the `books` table, formats it into a system prompt, and calls OpenAI’s chat completions API with the `gpt-5-chat-latest` model.
  - Responses are HTML-friendly (newlines converted to `<br>`) and prefixed with the `BOT_NAME` ("Libby").

- This Node service is started with `npm start` and is independent of the Laravel HTTP server, but it shares the same database and environment configuration.

## Background jobs and queues

- The scheduled task in `routes/console.php` runs daily at 08:00 and scans `Borrow` records with `status = 'borrowed'` and `due_at < now()` to send `DueDateReminder` notifications to affected users.
- The `composer dev` script starts a queue listener (`php artisan queue:listen --tries=1`), so during local development queued jobs (notifications, emails, etc.) will be processed automatically while the dev script is running.
