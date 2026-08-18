<?php

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\SetLocal;
use App\Http\Middleware\SuperAdminMiddleware;
use App\Http\Middleware\UserMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Auth;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            SetLocal::class,
    ]);
    $middleware->alias([
            'adminMiddleware'=>AdminMiddleware::class,
            'superadminMiddleware'=>SuperAdminMiddleware::class,
            'userMiddleware'=>UserMiddleware::class,
        ]);
        //
        $middleware->redirectUsersTo(function () {
    if (Auth::check()) {
        $user = Auth::user();

        // ၁။ Admin ဖြစ်လျှင် - သူလာခဲ့တဲ့ စာမျက်နှာ (Intended URL) ဆီ ပြန်ပို့မယ်။ 
        // မရှိရင်တော့ Default အနေနဲ့ admin#home ကို သွားမယ်။
        if ($user->role === 'admin') {
            return redirect()->intended(route('admin#home'))->getTargetUrl();
        }
        if ($user->role === 'superadmin') {
            return redirect()->intended(route('admin#home'))->getTargetUrl();
        }
        
        // ၂။ User ဖြစ်ပြီး Approved ဖြစ်ထားလျှင် - သူလာခဲ့တဲ့နေရာဆီ ပြန်ပို့မယ်။ 
        // မရှိရင် Default အနေနဲ့ user#home ကို သွားမယ်။
        if ($user->role === 'user' && $user->is_approved == true) {
            return redirect()->intended(route('user#home'))->getTargetUrl();
        }

        // ၃။ User ဖြစ်ပြီး Approved မဖြစ်သေးလျှင် - Payment Page ဆီ ပို့မယ်။
        if ($user->role === 'user' && $user->is_approved == false) {           
            return route('payment#paymentDetails');
        }
    }
});
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
