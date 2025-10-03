<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }

  public static function redirectToDashboard()
{
    $user = auth()->user();

    if ($user->role === 'admin') {
        return '/admin/dashboard';
    } elseif ($user->role === 'faculty') {
        return '/faculty/dashboard';
    } elseif ($user->role === 'student') {
        return '/student/dashboard';
    }

    return '/';
}


}
