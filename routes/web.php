<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

require_once __DIR__."/admin.php";
require_once __DIR__."/user.php";
require_once __DIR__.'/auth.php';
Route::get('/', function () {
    if (Auth::check()) {
        // Login ဝင်ထားပြီးသားဆိုရင် ၎င်းရဲ့ Role အလိုက် နေရာမှန်ဆီ redirect ပြန်လုပ်ပေးမယ်
        if (Auth::user()->role === 'admin' || Auth::user()->role === 'superadmin') {
            return redirect()->route('admin#home');
        }
        return redirect()->route('user#home');
    }
    // Login မဝင်ရသေးရင်တော့ ပုံမှန်အတိုင်း Login Form ပြမယ်
    return view('auth.start');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('adminMiddleware')->name('dashboard');



Route::get('lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'mm'])) {
        session()->put('locale', $locale);
    }
    return redirect()->back();
})->name('lang.switch');


