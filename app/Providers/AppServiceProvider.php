<?php

namespace App\Providers;

use App\Models\ActivityLog;
use App\Models\Notification;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

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
    // Used to run logic after everything is registered and the app is ready to use.
    public function boot(): void
    {
        DB::statement("SET time_zone='+08:00'");

        Paginator::useBootstrapFive();

        // For beneficiary site activities log
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $activities = Notification::where('user_id', Auth::id())
                    ->latest()
                    ->take(5)
                    ->get();

                $unreadCount = Notification::where('user_id', Auth::id())
                    ->where('is_read', false) // assuming unread logs have is_read = 0
                    ->count();


                $view->with([
                    'activities' => $activities,
                    'unreadCount' => $unreadCount,
                ]);
            }
        });
    }
}
