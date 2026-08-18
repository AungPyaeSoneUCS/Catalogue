<?php



use App\Http\Controllers\AddAdminController;
use App\Http\Controllers\Admin\AcademicYearController;
use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\Admin\BookController;
use App\Http\Controllers\Admin\BorrowRequestController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\RequestUserController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ContactController;
use Illuminate\Support\Facades\Route;
    Route::group(['middleware' => ['auth', 'superadminMiddleware'], 'prefix' => 'admin'], function () {
    // Admin dashboard routes
    Route::get('/create', [AddAdminController::class, 'addAdmin'])->name('admin.create');
    Route::post('/store', [AddAdminController::class, 'store'])->name('admin.store');
    Route::delete('/delete/{id}', [AddAdminController::class, 'destroy'])->name('admin.delete');
});
     Route::group(['middleware'=>['auth','adminMiddleware'],'prefix'=>'admin'],function(){
        //admin dashboard
        // Route::get('/create', [AddAdminController::class, 'addAdmin'])->name('admin.create');
        // Route::post('/store', [AddAdminController::class, 'store'])->name('admin.store');
        // Route::delete('/delete/{id}', [AddAdminController::class, 'destroy'])->name('admin.delete');

        Route::get('/dashboard',[DashboardController::class,'dashboard'])->name('admin#home');
        Route::group(['prefix'=>'requestUser'],function(){
            Route::get('/details',[RequestUserController::class,'details'])->name('request#userDetails');
            // Action processing routes
            Route::post('/{id}/accept', [RequestUserController::class, 'accept'])->name('request#acceptUser');
            Route::post('/{id}/reject', [RequestUserController::class, 'reject'])->name('request#rejectUser');
        });

        Route::group(['prefix'=>'userList'],function(){
            Route::get('/details',[UserController::class,'details'])->name('list#userDetails');
            Route::put('/users/{id}/password', [UserController::class, 'updatePassword'])->name('list#passwordUpdate');
    
            // 3. Delete User Action (Matches the Table Form DELETE method)
            Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('list#userDestroy');

            Route::get('users/export', [UserController::class, 'export'])->name('list#userExport');
            
        });
        Route::group(['prefix'=>'year'],function(){
            Route::get('/details',[AcademicYearController::class,'details'])->name('list#yearDetails');
            Route::post('/store', [AcademicYearController::class, 'store'])->name('store#yearDetails');
            Route::put('/update/{id}', [AcademicYearController::class, 'update'])->name('update#yearDetails');
            Route::delete('/delete/{id}', [AcademicYearController::class, 'destroy'])->name('delete#yearDetails');
            
        });
        Route::group(['prefix'=>'payment'],function(){
            Route::get('/details',[PaymentController::class,'details'])->name('list#paymentDetails');
            Route::post('/store', [PaymentController::class, 'store'])->name('store#paymentDetails');
            Route::put('/update/{payment}', [PaymentController::class, 'update'])->name('update#paymentDetails');
            Route::delete('/delete/{payment}', [PaymentController::class, 'destroy'])->name('delete#paymentDetails');
            Route::get('/member/fees',[PaymentController::class,'memberFee'])->name('list#memberFees');
            Route::get('/export', [PaymentController::class, 'exportMembers'])->name('admin.exportMembers');
            
        });
        Route::group(['prefix'=>'category'],function(){
            Route::get('/details',[CategoryController::class,'details'])->name('list#categoryDetails');
            Route::post('/store', [CategoryController::class, 'store'])->name('store#categoryDetails');
            Route::put('/update/{category}', [CategoryController::class, 'update'])->name('update#categoryDetails');
            Route::delete('/delete/{category}', [CategoryController::class, 'destroy'])->name('delete#categoryDetails');
            
        });
        Route::group(['prefix'=>'profile'],function(){
            Route::get('/edit', [AdminProfileController::class, 'edit'])->name('admin.profile.edit');
            Route::patch('/update', [AdminProfileController::class, 'update'])->name('admin.profile.update');
            Route::put('/update/password', [AdminProfileController::class, 'updatePassword'])->name('admin.password.update');
        });
        Route::group(['prefix'=>'contact'],function(){
            // 1. Admin က User တွေနဲ့ စကားပြောဖို့ စာရင်းကြည့်တဲ့နေရာ (Parameter မလို)
                Route::get('/list', [ContactController::class, 'list'])->name('admin.contact.list');
                
                // 2. Chat ဝင်ပြောမည့်နေရာ (Parameter လို)
                Route::get('/chat/{receiverId}', [ContactController::class, 'index'])->name('admin.chat.view');
                
                // Message ပို့သည့် Route
                Route::post('/chat/store', [ContactController::class, 'store'])->name('admin.chat.store');
        });
        Route::group(['prefix'=>'book'],function(){
            Route::get('details',[BookController::class,'details'])->name('list#bookDetails');
            Route::get('/books/{id}', [BookController::class, 'show'])->name('books.show');
            Route::get('/books/{id}/edit', [BookController::class, 'edit'])->name('books.edit');
            Route::put('/books/{book}', [BookController::class, 'update'])->name('books.update');
            
            // 💡 ပြင်ဆင်ချက် - destroy route ကို တစ်ခုတည်းသာ ထားပါ
            Route::delete('/books/{id}', [BookController::class, 'destroy'])->name('books.destroy');
            
            Route::post('/books/store', [BookController::class, 'store'])->name('books.store');
            Route::post('/books/import', [BookController::class, 'import'])->name('books.import');
            Route::get('/export', [BookController::class, 'export'])->name('list#bookExport');
            Route::get('/download-template', [BookController::class, 'downloadTemplate'])->name('books.download-template');
            
            // Borrow Routes များကို သီးသန့်အုပ်စုဖွဲ့နိုင်ပါတယ်
            Route::delete('/booking/{id}/delete', [BorrowRequestController::class, 'deleteBooking'])->name('admin#deleteBooking');
            Route::get('/lost/books', [BorrowRequestController::class, 'lostBooksList'])->name('admin#lostBooksList');
            Route::get('/damage/books', [BorrowRequestController::class, 'damageBooksList'])->name('admin#damageBooksList');
            Route::post('/lost/{id}', [BorrowRequestController::class, 'lostBook'])->name('admin#lostBook');
            Route::post('/damage/{id}', [BorrowRequestController::class, 'damageBook'])->name('admin#damageBook');
            Route::get('/returned-list', [BorrowRequestController::class, 'returnedList'])->name('admin#returnedList');
            Route::get('/booking-list', [BorrowRequestController::class, 'bookingList'])->name('admin#bookingList');
            Route::get('/borrow-list', [BorrowRequestController::class, 'borrowList'])->name('admin#borrowList');
            Route::get('/overdue-list', [BorrowRequestController::class, 'overdueList'])->name('admin#overdueList');
            Route::get('/returned-fines', [BorrowRequestController::class, 'showFines'])->name('admin.returnedFines');
            Route::get('/export-returned-fines', [BorrowRequestController::class, 'exportReturnedFines'])->name('admin.exportReturnedFines');
            Route::get('/returned-list/export', [BorrowRequestController::class, 'exportReturnedList'])->name('admin.exportReturnedList');
            Route::get('/dashboard/export', [DashboardController::class, 'exportDashboard'])->name('admin.exportDashboard');

            Route::post('/borrow-requests/{id}/accept', [BorrowRequestController::class, 'acceptBooking'])->name('admin#acceptBooking');
            Route::post('/borrow-requests/{id}/return', [BorrowRequestController::class, 'receiveBook'])->name('admin#receiveBook');
            
            Route::get('/library-settings', [BorrowRequestController::class, 'settingPage'])->name('admin#settingPage');
            Route::post('/borrow-settings', [BorrowRequestController::class, 'saveSettings'])->name('admin#saveSettings');
            // web.php တွင် အောက်ပါအတိုင်း ပြင်ပါ
            Route::get('/borrowed/export', [BorrowRequestController::class, 'exportBorrowedList'])->name('admin.exportBorrowedList');
            Route::get('/overdue/export', [BorrowRequestController::class, 'exportOverdue'])->name('admin.exportOverdue');
            Route::get('/lost-books/export', [BorrowRequestController::class, 'exportLostBooks'])->name('admin.exportLostBooks');
            Route::get('/damage-books/export', [BorrowRequestController::class, 'exportDamageBooks'])->name('admin.exportDamageBooks');
        });
});