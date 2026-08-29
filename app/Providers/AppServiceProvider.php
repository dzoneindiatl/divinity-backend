<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

use App\Models\OrderNotifications;

class AppServiceProvider extends ServiceProvider
{

    public function boot()
    {
                    
        // View Composer for global data
        View::composer('*', function ($view) {
            $unreadNotificationsCount = OrderNotifications::where('user_id', auth()->id())
                ->where('is_read', 0)
                ->count();

            // Share the unread notifications count with all views
            $view->with('unreadNotificationsCount', $unreadNotificationsCount);
        });
        
        View::composer('*', function ($view) {
            $notifications = OrderNotifications::where('user_id', auth()->id())
                ->where('is_read', 0)
                ->orderBy('created_at', 'desc')
                ->limit(10) // Limit to the latest 10 notifications
                ->get();

            // Share notifications globally with all views
            $view->with('notifications', $notifications);
        });
        
    }
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
    // public function boot(): void
    // {
    //     //
    // }
}
