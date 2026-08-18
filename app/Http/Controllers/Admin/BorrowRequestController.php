<?php

namespace App\Http\Controllers\Admin;

use App\Exports\BorrowedListExport;
use App\Exports\DamageBooksExport;
use App\Exports\LostBooksExport;
use App\Exports\OverdueExport;
use App\Exports\ReturnedFinesExport;
use App\Exports\ReturnedListExport;
use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BorrowRequest;
use App\Models\SystemSetting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class BorrowRequestController extends Controller
{
    public function bookingList(Request $request)
{
    // Session Logic
    if ($request->has('search') && !$request->filled('search')) {
        session()->forget('booking_list_last_query');
    } elseif ($request->filled('search')) {
        session(['booking_list_last_query' => $request->search]);
    } elseif (session()->has('booking_list_last_query')) {
        $request->merge(['search' => session('booking_list_last_query')]);
    }

    $searchTerm = $request->input('search');

    $query = BorrowRequest::with(['user.year', 'book'])
        ->where('status', 'pending')->orWhere('status','canceled');

    if (!empty($searchTerm)) {
        $query->where(function($q) use ($searchTerm) {
            $q->whereHas('user', function($u) use ($searchTerm) {
                $u->where('name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('roll_number', 'LIKE', "%{$searchTerm}%")
                  ->orWhereHas('year', fn($y) => $y->where('academic_year', 'LIKE', "%{$searchTerm}%"));
            })
            ->orWhereHas('book', function($b) use ($searchTerm) {
                $b->where('title', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('code', 'LIKE', "%{$searchTerm}%");
            });
        });
    }

    $requests = $query->latest()->get();
            
    return view('admin.borrow.booking', compact('requests', 'searchTerm'));
}
public function deleteBooking($id)
    {
        // Booking ID နဲ့ ရှာပြီး ဖျက်ပစ်ခြင်း
        $booking = BorrowRequest::findOrFail($id);
        $booking->delete();

        // ဖျက်ပြီးရင် ပြန် redirect လုပ်ပေးခြင်း
        return redirect()->back()->with('success', __('Booking request deleted successfully.'));
    }
    public function borrowList(Request $request)
{
    if ($request->has('search') && !$request->filled('search')) {
        session()->forget('borrow_list_last_query');
    } elseif ($request->filled('search')) {
        session(['borrow_list_last_query' => $request->search]);
    } elseif (session()->has('borrow_list_last_query')) {
        $request->merge(['search' => session('borrow_list_last_query')]);
    }
    $requests = BorrowRequest::with(['user', 'book'])
        ->join('users', 'borrow_requests.user_id', '=', 'users.id')
        ->join('books', 'borrow_requests.book_id', '=', 'books.id')
        ->join('years', 'users.year_id', '=', 'years.id')
        ->where('borrow_requests.status', 'borrowed')
        ->select('borrow_requests.*'); 

    if ($request->has('search') && $request->search != '') {
        $key = $request->search;
        $requests->where(function($query) use ($key) {
            $query->whereAny([
                'users.name',
                'users.roll_number',
                'books.title',
                'books.code',
                'years.academic_year'
            ], 'like', '%' . $key . '%')
           
            ->orWhereRaw("DATE_FORMAT(borrow_requests.due_at, '%d-%b-%Y') LIKE ?", ['%' . $key . '%'])
            ->orWhereRaw("DATE_FORMAT(borrow_requests.due_at, '%Y-%m-%d') LIKE ?", ['%' . $key . '%']);
        });
    }

    $requests = $requests->latest('borrow_requests.created_at')->get();
    //$totalBorrow = BorrowRequest::where('status', 'borrowed')->count();
    $totalBorrow = (clone $requests)->count();
    return view('admin.borrow.borrowed', compact('requests','totalBorrow'));
}

    public function overdueList(Request $request)
{
    if ($request->has('search') && !$request->filled('search')) {
        session()->forget('overdue_list_last_query');
    } elseif ($request->filled('search')) {
        session(['overdue_list_last_query' => $request->search]);
    } elseif (session()->has('overdue_list_last_query')) {
        $request->merge(['search' => session('overdue_list_last_query')]);
    }

    // users နှင့် years ကို join လုပ်ရန် with သို့မဟုတ် join သုံးနိုင်သည်
    $query = BorrowRequest::with(['user.year', 'book'])
        ->where('status', 'overdue');

    if ($request->filled('search')) {
        $key = $request->search;
        $query->where(function($q) use ($key) {
            $q->whereHas('user', function($sub) use ($key) {
                $sub->where('name', 'LIKE', "%{$key}%")
                    ->orWhere('roll_number', 'LIKE', "%{$key}%")
                    // ကျောင်းသား၏ Year သို့မဟုတ် Academic Year ကို ရှာရန် (year ဆက်သွယ်ချက်ရှိသည်ဟု ယူဆပါသည်)
                    ->orWhereHas('year', function($yearSub) use ($key) {
                        $yearSub->where('name', 'LIKE', "%{$key}%") // သို့မဟုတ် column နာမည်အမှန်
                                ->orWhere('academic_year', 'LIKE', "%{$key}%");
                    });
            })
            ->orWhereHas('book', function($sub) use ($key) {
                $sub->where('title', 'LIKE', "%{$key}%")
                    ->orWhere('code','LIKE', "%{$key}%");
            })
            ->orWhereRaw("DATE_FORMAT(due_at, '%d-%b-%Y') LIKE ?", ["%{$key}%"]);
        });
    }

    $requests = $query->orderBy('borrow_requests.created_at', 'desc')->get();
            
    return view('admin.borrow.overdue', compact('requests'));
}

        
public function showFines(Request $request)
{
    if ($request->has('search') && !$request->filled('search')) {
        session()->forget('fine_list_last_query');
    } elseif ($request->filled('search')) {
        session(['fine_list_last_query' => $request->search]);
    } elseif (session()->has('fine_list_last_query')) {
        $request->merge(['search' => session('fine_list_last_query')]);
    }

    // user.year ကို with ထဲသို့ ထည့်သွင်းပေးခြင်း
    $query = BorrowRequest::with(['user.year', 'book'])
                ->where('status', 'returned')
                ->where('fine_amount', '>', 0);

    if ($request->has('search') && !empty($request->search)) {
        $searchTerm = $request->search;
        $query->where(function($q) use ($searchTerm) {
            $q->whereHas('user', function($userQuery) use ($searchTerm) {
                $userQuery->where('name', 'LIKE', "%{$searchTerm}%")
                          ->orWhere('roll_number', 'LIKE', "%{$searchTerm}%")
                          // Academic Year ဖြင့် ရှာဖွေနိုင်ရန် ထည့်သွင်းခြင်း
                          ->orWhereHas('year', function($yearQuery) use ($searchTerm) {
                              $yearQuery->where('academic_year', 'LIKE', "%{$searchTerm}%");
                          });
            })->orWhereHas('book', function($bookQuery) use ($searchTerm) {
                $bookQuery->where('title', 'LIKE', "%{$searchTerm}%")
                          ->orWhere('code', 'LIKE', "%{$searchTerm}%");
            })
            ->orWhereRaw("DATE_FORMAT(returned_at, '%d-%b-%Y') LIKE ?", ["%{$searchTerm}%"])
            ->orWhereRaw("DATE_FORMAT(returned_at, '%Y-%m-%d') LIKE ?", ["%{$searchTerm}%"])
            ->orWhereRaw("DATE_FORMAT(due_at, '%d-%b-%Y') LIKE ?", ["%{$searchTerm}%"])
            ->orWhereRaw("DATE_FORMAT(due_at, '%Y-%m-%d') LIKE ?", ["%{$searchTerm}%"]);
        });
    }

    $fines = $query->orderBy('updated_at', 'desc')->get();

    return view('admin.borrow.returned_fines', compact('fines'));
}
public function exportReturnedFines(Request $request)
    {
        if ($request->has('search') && !$request->filled('search')) {
            session()->forget('returned_fines_last_query');
        } elseif ($request->filled('search')) {
            session(['returned_fines_last_query' => $request->search]);
        } elseif (session()->has('returned_fines_last_query')) {
            $request->merge(['search' => session('returned_fines_last_query')]);
        }

        $query = BorrowRequest::with(['user.year', 'book'])
                    ->where('status', 'returned')
                    ->where('fine_amount', '>', 0);

        if ($request->filled('search')) {
            $key = $request->search;
            $query->where(function($q) use ($key) {
                $q->whereHas('user', function($sub) use ($key) {
                    $sub->where('name', 'LIKE', "%{$key}%")
                        ->orWhere('roll_number', 'LIKE', "%{$key}%")
                        ->orWhereHas('year', function($yearSub) use ($key) {
                            $yearSub->where('academic_year', 'LIKE', "%{$key}%");
                        });
                })
                ->orWhereHas('book', function($sub) use ($key) {
                    $sub->where('title', 'LIKE', "%{$key}%")
                        ->orWhere('code', 'LIKE', "%{$key}%");
                })
                ->orWhereRaw("DATE_FORMAT(returned_at, '%d-%b-%Y') LIKE ?", ["%{$key}%"])
                ->orWhereRaw("DATE_FORMAT(returned_at, '%Y-%m-%d') LIKE ?", ["%{$key}%"])
                ->orWhereRaw("DATE_FORMAT(due_at, '%d-%b-%Y') LIKE ?", ["%{$key}%"])
                ->orWhereRaw("DATE_FORMAT(due_at, '%Y-%m-%d') LIKE ?", ["%{$key}%"]);
            });
        }

        $fines = $query->latest()->get();
        $search = $request->search;

        return Excel::download(new ReturnedFinesExport($fines, $search), 'returned_fines.xlsx');
    }


    public function settingPage()
    {
        return view('admin.borrow.settings');
    }

    public function acceptBooking($id)
    {
        $borrow = BorrowRequest::findOrFail($id);
        $book = $borrow->book;


        $duration = (int)(SystemSetting::where('key', 'borrow_duration_days')->value('value') ?? 7);
        $borrow->update([
            'status' => 'borrowed',
            'borrowed_at' => now(),
            'due_at' => Carbon::now()->addDays($duration)->endOfDay() 
        ]);

        

        return back()->with('success', __('book_issued_success'));
    }

   public function returnedList(Request $request)
{
    if ($request->has('search') && !$request->filled('search')) {
        session()->forget('returned_list_last_query');
    } elseif ($request->filled('search')) {
        session(['returned_list_last_query' => $request->search]);
    } elseif (session()->has('returned_list_last_query')) {
        $request->merge(['search' => session('returned_list_last_query')]);
    }

    // user.year ကို with ထဲသို့ ထည့်သွင်းပေးခြင်း
    $query = BorrowRequest::with(['user.year', 'book'])
        ->where('status', 'returned');

    if ($request->has('search') && !empty($request->search)) {
        $searchTerm = $request->search;
        $query->where(function($q) use ($searchTerm) {
            $q->whereHas('user', function($userQuery) use ($searchTerm) {
                $userQuery->where('name', 'LIKE', "%{$searchTerm}%")
                          ->orWhere('roll_number', 'LIKE', "%{$searchTerm}%")
                          // Academic Year ဖြင့် ရှာဖွေနိုင်ရန် ထည့်သွင်းခြင်း
                          ->orWhereHas('year', function($yearQuery) use ($searchTerm) {
                              $yearQuery->where('academic_year', 'LIKE', "%{$searchTerm}%");
                          });
            })->orWhereHas('book', function($bookQuery) use ($searchTerm) {
                $bookQuery->where('title', 'LIKE', "%{$searchTerm}%")
                          ->orWhere('code', 'LIKE', "%{$searchTerm}%");
            })
            ->orWhereRaw("DATE_FORMAT(returned_at, '%d-%b-%Y') LIKE ?", ["%{$searchTerm}%"])
            ->orWhereRaw("DATE_FORMAT(returned_at, '%Y-%m-%d') LIKE ?", ["%{$searchTerm}%"]);
        });
    }

    $requests = $query->orderBy('returned_at', 'desc')->get();

    return view('admin.borrow.returned', compact('requests'));
}
    public function lostBooksList(Request $request)
    {
        if ($request->has('search') && !$request->filled('search')) {
        session()->forget('lost_books_last_query');
    } elseif ($request->filled('search')) {
        session(['lost_books_last_query' => $request->search]);
    } elseif (session()->has('lost_books_last_query')) {
        $request->merge(['search' => session('lost_books_last_query')]);
    }
        $lostBooks = BorrowRequest::with(['user.year', 'book'])
            ->where('status', 'lost')
            ->when($request->search, function ($query) use ($request) {
                $query->whereHas('user', function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%')
                        ->orWhere('roll_number', 'like', '%' . $request->search . '%')
                        ->orWhere('email', 'like', '%' . $request->search . '%')
                        ->orWhere('phone', 'like', '%' . $request->search . '%')
                        ->orWhereHas('year', function($y) use ($request) {
                        $y->where('academic_year', 'LIKE', '%' . $request->search . '%');
                  });
                })
                ->orWhereHas('book', function ($sub) use ($request) {
                    $sub->where('title', 'like', '%' . $request->search . '%')
                        ->orWhere('code', 'like', '%' . $request->search . '%');
                })
            ->orWhereRaw("DATE_FORMAT(borrow_requests.returned_at, '%d-%b-%Y') LIKE ?", ['%' . $request->search . '%'])
                ;
            })
            ->latest()
            ->get();

        return view('admin.borrow.lost_books', compact('lostBooks'));
    }
     public function damageBooksList(Request $request)
    {
        if ($request->has('search') && !$request->filled('search')) {
        session()->forget('damage_books_last_query');
    } elseif ($request->filled('search')) {
        session(['damage_books_last_query' => $request->search]);
    } elseif (session()->has('damage_books_last_query')) {
        $request->merge(['search' => session('damage_books_last_query')]);
    }
        $damageBooks = BorrowRequest::with(['user.year', 'book'])
            ->where('status', 'damage')
            ->when($request->search, function ($query) use ($request) {
                $query->whereHas('user', function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%')
                        ->orWhere('roll_number', 'like', '%' . $request->search . '%')
                        ->orWhere('email', 'like', '%' . $request->search . '%')
                        ->orWhere('phone', 'like', '%' . $request->search . '%')
                        ->orWhereHas('year', function($y) use ($request) {
                        $y->where('academic_year', 'LIKE', '%' . $request->search . '%');
                  });
                })
                ->orWhereHas('book', function ($sub) use ($request) {
                    $sub->where('title', 'like', '%' . $request->search . '%')
                        ->orWhere('code', 'like', '%' . $request->search . '%');
                })
            ->orWhereRaw("DATE_FORMAT(borrow_requests.returned_at, '%d-%b-%Y') LIKE ?", ['%' . $request->search . '%'])
                ;
            })
            ->latest()
            ->get();

        return view('admin.borrow.damage_books', compact('damageBooks'));
    }
    public function exportLostBooks(Request $request) 
{
    return Excel::download(new LostBooksExport($request->search), 'LostBooks_' . now()->format('Y-m-d') . '.xlsx');
}
public function exportDamageBooks(Request $request) 
{
    return Excel::download(new DamageBooksExport($request->search), 'DamageBooks_' . now()->format('Y-m-d') . '.xlsx');
}
    public function lostBook(Request $request, $id) 
{
    $req = BorrowRequest::findOrFail($id);
    $request->validate([
    'lost_fine' => 'required|numeric',
], [
        'lost_fine.required' => __('The lost fine field is required.'), 
        'lost_fine.numeric' => __('The lost fine must be a number.'), 
    ]);
    $req->update([
        'status' => 'lost', 
        'lost_fine' => $request->lost_fine, 
        'returned_at' => now(), 
    ]);
    $req->book->decrement('total_qty');
    
    return back()->with('successLostFine', __('Book marked as lost successfully.'));
}
 public function damageBook(Request $request, $id) 
{
    $req = BorrowRequest::findOrFail($id);
    $request->validate([
    'damage_fine' => 'required|numeric',
], [
        'damage_fine.required' => __('The lost fine field is required.'), 
        'damage_fine.numeric' => __('The lost fine must be a number.'), 
    ]);
    $req->update([
        'status' => 'damage', 
        'damage_fine' => $request->damage_fine, 
        'returned_at' => now(), 
    ]);
    $req->book->increment('available_qty');
    return back()->with('successDamageFine', __('Book marked as damage successfully.'));
}
    public function receiveBook(Request $request, $id)
{   
    $borrow = BorrowRequest::findOrFail($id);
    
    $book = Book::find($borrow->book_id);
    
    if ($book) {
        $currentQty = $book->available_qty;
        $book->available_qty = $currentQty + 1;
        $book->save();
    }
    
    $borrow->update([
        'status' => 'returned',
        'returned_at' => now(),
        'fine_amount' => $request->input('fine_amount', 0)
    ]);

    return redirect()->back()->with('success', __('Book returned!'));
}


    
public function exportReturnedList(Request $request)
{
    // Session ဖြင့် search query ကို ထိန်းသိမ်းခြင်း
    if ($request->has('search') && !$request->filled('search')) {
        session()->forget('returned_list_last_query');
    } elseif ($request->filled('search')) {
        session(['returned_list_last_query' => $request->search]);
    } elseif (session()->has('returned_list_last_query')) {
        $request->merge(['search' => session('returned_list_last_query')]);
    }

    $query = BorrowRequest::with(['user.year', 'book'])
                ->where('status', 'returned');

    if ($request->filled('search')) {
        $key = $request->search;
        $query->where(function($q) use ($key) {
            $q->whereHas('user', function($u) use ($key) {
                $u->where('name', 'LIKE', "%{$key}%")
                  ->orWhere('roll_number', 'LIKE', "%{$key}%")
                  ->orWhereHas('year', function($y) use ($key) {
                      $y->where('academic_year', 'LIKE', "%{$key}%");
                  });
            })->orWhereHas('book', function($b) use ($key) {
                $b->where('title', 'LIKE', "%{$key}%")
                  ->orWhere('code', 'LIKE', "%{$key}%");
            })
            ->orWhereRaw("DATE_FORMAT(returned_at, '%d-%b-%Y') LIKE ?", ["%{$key}%"])
            ->orWhereRaw("DATE_FORMAT(returned_at, '%Y-%m-%d') LIKE ?", ["%{$key}%"]);
        });
    }

    $requests = $query->latest()->get();
    $search = $request->search;

    return Excel::download(new ReturnedListExport($requests, $search), 'Returned_Books_List.xlsx');
}
    public function saveSettings(Request $request)
{
    $request->validate([
        'max_borrow_limit'     => 'required|integer|min:1',
        'daily_fine_rate'      => 'required|integer|min:0',
        'booking_expire_hours' => 'required|numeric|min:0.01',
        'borrow_duration_days' => 'required|integer|min:1', 
    ]);

    SystemSetting::updateOrCreate(['key' => 'max_borrow_limit'],     ['value' => $request->max_borrow_limit]);
    SystemSetting::updateOrCreate(['key' => 'daily_fine_rate'],      ['value' => $request->daily_fine_rate]);
    SystemSetting::updateOrCreate(['key' => 'booking_expire_hours'], ['value' => $request->booking_expire_hours]);
    SystemSetting::updateOrCreate(['key' => 'borrow_duration_days'], ['value' => $request->borrow_duration_days]); // အသစ်ထည့်သွင်းခြင်း

    return redirect()->route('admin#settingPage')
                     ->with('success', __('Rules and regulations updated successfully.'));
}



// 
public function exportOverdue(Request $request)
{
    $search = $request->input('search');

    $query = BorrowRequest::with(['user', 'book'])
        ->join('users', 'borrow_requests.user_id', '=', 'users.id')
        ->join('years', 'users.year_id', '=', 'years.id')
        ->where('status', 'overdue'); 

    if (!empty($search)) {
        $query->where(function($q) use ($search) {
            $q->whereHas('user', function($sub) use ($search) {
                $sub->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('roll_number', 'LIKE', "%{$search}%")
                    ->orWhere('years.academic_year', 'LIKE', "%{$search}%");
            })->orWhereHas('book', function($sub) use ($search) {
                $sub->where('title', 'LIKE', "%{$search}%")
                    ->orWhere('code','LIKE', "%{$search}%");
            })
            ->orWhereRaw("DATE_FORMAT(due_at, '%d-%M-%Y') LIKE ?", ["%{$search}%"]);
        });

        // Search လုပ်ထားသော စာသားကို Filter ခေါင်းစဉ်အဖြစ် သတ်မှတ်ခြင်း
        $filterTitle = "Title: " . $search;
    } else {
        $filterTitle = "All Overdue Books Report";
    }

    $requests = $query->get();

    // OverdueExport သို့ requests နှင့်အတူ filterTitle ကိုပါ ပေးပို့ခြင်း
    return Excel::download(new OverdueExport($requests, $filterTitle), 'overdue_list.xlsx');
}
    public function exportBorrowedList(Request $request)
{
    $search = $request->input('search');
    
    return Excel::download(
        new BorrowedListExport($search), 
        'Filtered_Borrowed_List_' . date('Y-m-d') . '.xlsx'
    );
}
}