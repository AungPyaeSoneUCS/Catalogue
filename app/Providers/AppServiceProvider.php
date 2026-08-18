<?php

namespace App\Providers;

use App\Models\Message;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
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
    Paginator::useBootstrapFive();
    View::composer('*', function ($view) {
        if (Auth::check()) {
            $unreadCount = 0; // Default တန်ဖိုး 0 ထားပါ

            if (Auth::user()->isAdmin()) {
                // Admin အတွက်
                $unreadCount = Message::where('is_read', 0)
                                      ->where('receiver_id', Auth::id())
                                      ->where('sender_id', '!=', Auth::id())
                                      ->count();
            } else {
                // User အတွက်
                $unreadCount = Message::where('is_read', 0)
                                      ->where('receiver_id', Auth::id())
                                      ->where('sender_id', '!=', Auth::id())
                                      ->count();
            }
            
            // View တိုင်းကိုပို့ပေးပါ
            $view->with('unreadCount', $unreadCount);
        }
    });
}
}