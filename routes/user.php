<?php 

use App\Http\Controllers\ContactController;
use App\Http\Controllers\User\BorrowController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\PaymentController;
use App\Http\Controllers\User\ProfileController;
use Illuminate\Support\Facades\Route;
Route::middleware(['auth'])->group(function () {
    Route::group(['prefix'=>'payment'],function(){
            Route::get('/details',[PaymentController::class,'details'])->name('payment#paymentDetails');
            Route::post('create',[PaymentController::class,'create'])->name('user#paymentCreate');
            Route::get('/wait',[PaymentController::class,'wait'])->name('user#paymentWait');
        });
    });
    Route::group(['middleware'=>['userMiddleware','auth'],'prefix'=>'user'],function(){
        //user dashboard
        Route::get('/home_home',[DashboardController::class,'dashboard'])->name('user#home');
        // routes/web.php ထဲတွင် ဤ Route အား ထည့်သွင်းပေးပါ
        Route::group(['prefix'=>'details'],function(){
            Route::get('/books/{id}', [DashboardController::class, 'show'])->name('user#bookShow');
        });
        Route::group(['prefix'=>'profile'],function () {
        Route::get('/edit', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/update', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/destroy', [ProfileController::class, 'destroy'])->name('profile.destroy');
        Route::get('/rule', [ProfileController::class, 'rule'])->name('rule');
        Route::get('/about', [ProfileController::class, 'about'])->name('about');
        Route::get('/faq', [ProfileController::class, 'faq'])->name('faq');
    });
        Route::group(['prefix'=>'contact'],function(){
            Route::get('/start', [ContactController::class, 'startChat'])->name('chat.start');
            Route::get('/chat', [ContactController::class, 'index'])->name('chat.index');
            Route::post('/chat', [ContactController::class, 'store'])->name('user.chat.store');
        });
        
        
         
        Route::group(['middleware'=>['userMiddleware','auth'],'prefix'=>'user'], function(){
            Route::get('/booking-history', [BorrowController::class, 'bookingHistory'])->name('user#bookingHistory');
            Route::get('/current-borrows', [BorrowController::class, 'currentBorrows'])->name('user#currentBorrows');
            // Booking တင်ရန် Route
            Route::post('/books/{id}/booking', [BorrowController::class, 'requestBooking'])->name('user#requestBooking');
            // သက်တမ်းတိုးရန် Route
            Route::post('/borrow/{id}/extend', [BorrowController::class, 'extendBorrow'])->name('user#extendBorrow');

            // User မိမိဘာသာ Booking စာရင်းကို အပြီးဖျက်မည့် လမ်းကြောင်း
            Route::delete('/booking/cancel/{id}', [BorrowController::class, 'cancelBooking'])->name('user#cancelBooking');
        });
    });