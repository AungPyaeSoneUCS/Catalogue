<?php

namespace App\Http\Controllers\Admin;

use App\Exports\DashboardDataExport;
use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BorrowRequest;
use App\Models\Category;
use App\Models\Member;
use App\Models\Payment;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class DashboardController extends Controller
{
    public function dashboard(Request $request) {
    // ၁။ Card အတွက် Data များ
    $bookCounts=Book::count();
    $totalBooks = Book::sum('total_qty');
    $availableBooks=Book::sum('available_qty');
    $totalMemberFees=Member::sum('fee');
    $activeBorrowers = BorrowRequest::where('status', 'borrowed')->count();
    $returnedBooks = BorrowRequest::where('status', 'returned')->count();
    $pendingRequests = BorrowRequest::where('status', 'pending')->count();
    $overdueCount = BorrowRequest::where('status', 'overdue')->count();
    $fineCount = BorrowRequest::where('status', 'returned')->where('fine_amount', '>', 0)->count();
    $totalFineAmount = BorrowRequest::where('status', 'returned')->sum('fine_amount');
    $totalLostFineAmount = BorrowRequest::where('status', 'lost')->sum('lost_fine');
    $totalDamageFineAmount = BorrowRequest::where('status', 'damage')->sum('damage_fine');
    $totalMembers = User::where('role', 'user')->where('is_approved',1)->count();
    $settings = SystemSetting::pluck('value', 'key');
    $paymentCount = Payment::count();
    $categoryCount = Category::count();
    $categories = Category::withSum('books as total_books_qty', 'total_qty')
                ->latest()->take(2)->get(); 
    $categoryData = Category::withSum('books as total_books_qty', 'total_qty')->orderBy('total_books_qty','desc')->get();
    // ပြီးခဲ့သော ၇ ရက်စာ ရက်စွဲများကို အရင်ထုတ်ယူပါ
$dates = collect();
for ($i = 6; $i >= 0; $i--) {
    $dates->push(now()->subDays($i)->format('Y-m-d'));
}

// DB မှ Data ဆွဲထုတ်ခြင်း (Status စစ်ထုတ်ရန်)
$data = BorrowRequest::whereIn('status', ['pending', 'borrowed', 'overdue'])
    ->where('created_at', '>=', now()->subDays(7))
    ->selectRaw('DATE(created_at) as date, count(*) as total')
    ->groupBy('date')
    ->pluck('total', 'date');

// ရက်စွဲတစ်ခုချင်းစီအလိုက် Data ကို map လုပ်ပေးခြင်း
$dailyBorrowing = $dates->map(function ($date) use ($data) {
    return [
        'date' => $date,
        'total' => $data->get($date, 0) // Data မရှိရင် 0 ကို ယူမည်
    ];
});

    // ၂။ လစဉ်ချေးယူမှု (ယခုနှစ်အတွင်း)
    // $monthlyBorrowing = BorrowRequest::whereIn('borrow_requests.status', ['pending', 'borrowed','returned','overdue'])->selectRaw('MONTH(created_at) as month, count(*) as total')
    //     ->whereYear('created_at', date('Y'))
    //     ->groupBy('month')
    //     ->get();
    // ၁။ Database ထဲက ရှိသမျှနှစ်တွေကို dynamic ဆွဲထုတ်မယ်
   // ၁။ ဘယ်အချိန်မှာ ချေးယူတာလဲဆိုတာကို အခြေခံပြီး နှစ်တွေကို ထုတ်ယူပါ
$availableYears = BorrowRequest::selectRaw('YEAR(borrowed_at) as year')
    ->whereNotNull('borrowed_at') // borrowed_at မရှိသေးတာတွေကို ဖယ်ထုတ်ထားပါ
    ->distinct()
    ->orderBy('year', 'desc')
    ->pluck('year');

// ၂။ User ရွေးတဲ့နှစ်ကို ယူပါ
$selectedYear = $request->input('year', date('Y'));

// ၃။ ရွေးချယ်ထားတဲ့နှစ်အတွက် Data ဆွဲထုတ်ပါ
$monthlyBorrowing = BorrowRequest::whereIn('status', ['pending', 'borrowed', 'overdue','returned'])
    ->selectRaw('MONTH(borrowed_at) as month, count(*) as total') // borrowed_at ကို သုံးပါ
    ->whereYear('borrowed_at', $selectedYear) // borrowed_at ကို သုံးပါ
    ->whereNotNull('borrowed_at') // NULL ဖြစ်နေတာတွေကို ရှောင်ပါ
    ->groupBy('month')
    ->orderBy('month', 'asc')
    ->get();
    // After fetching $monthlyBorrowing from the database...
$dataFromDb = $monthlyBorrowing->pluck('total', 'month')->toArray();

// Create an array of 12 zeros
$chartData = [];
for ($i = 1; $i <= 12; $i++) {
    // If month exists in DB, use it, otherwise use 0
    $chartData[] = $dataFromDb[$i] ?? 0;
}
    $pendingCount = User::where('is_approved', false)->count();
    $yearLending = BorrowRequest::join('users', 'borrow_requests.user_id', '=', 'users.id')
        ->join('years', 'users.year_id', '=', 'years.id')
        ->whereIn('borrow_requests.status', ['pending', 'borrowed','overdue','returned'])
        ->select('years.academic_year', DB::raw('count(borrow_requests.id) as total'))
        ->groupBy('years.academic_year')
        ->get();
    // DashboardController.php
    $topCategories = Category::withCount(['books as total_borrowed' => function ($query) {
        $query->join('borrow_requests', 'books.id', '=', 'borrow_requests.book_id')
            ->whereIn('borrow_requests.status', ['pending','borrowed','overdue','returned']);
    }])
    ->orderBy('total_borrowed', 'desc') // အများဆုံးကနေ အနည်းဆုံး အစဉ်လိုက်ပြမယ်
    ->get(); // take(5) ကို ဖြုတ်လိုက်ပါပြီ

    $pieData = [
        'borrowed' => BorrowRequest::where('status', 'borrowed')->count(),
        'overdue'  => BorrowRequest::where('status', 'overdue')->count(),
        'returned'  => BorrowRequest::where('status', 'returned')->count(),
        'fine'     => BorrowRequest::where('status', 'returned')->where('fine_amount', '>', 0)->count(),
        'lost_fine'     => BorrowRequest::where('status', 'lost')->where('lost_fine', '>', 0)->count(),
        'damage_fine'     => BorrowRequest::where('status', 'damage')->where('damage_fine', '>', 0)->count(),
    ];
    return view('admin.dashboard.home', compact('returnedBooks','chartData','availableYears','selectedYear','totalMemberFees','pieData','topCategories','dailyBorrowing', 'monthlyBorrowing','categoryData','bookCounts','totalBooks','availableBooks', 'activeBorrowers', 
    'pendingRequests', 'totalMembers', 'yearLending','settings','paymentCount',
    'categoryCount','categories','pendingCount','overdueCount','fineCount','totalFineAmount','totalLostFineAmount','totalDamageFineAmount'));
}
public function exportDashboard(Request $request)
{
    // ၁။ Dashboard ပေါ်က Card Data တွေကို စုစည်းခြင်း
    $data = [
        ['Dashboard Summary', 'Value'],
        ['Total Users', User::where('role', 'user')->where('is_approved',1)->count()],
        ['Pending Users', User::where('is_approved', false)->count()],
        ['Total Books', Book::sum('total_qty')],
        ['Available Books', Book::sum('available_qty')],
        ['Total Member Fees', Member::sum('fee')],
        ['Booking Books',BorrowRequest::where('status', 'pending')->count()],
        ['Borrowed Books', BorrowRequest::where('status', 'borrowed')->count()],
        ['Returned Books', BorrowRequest::where('status', 'returned')->count()],
        ['Overdue Books', BorrowRequest::where('status', 'overdue')->count()],
        ['Total Fine Amount', BorrowRequest::where('status', 'returned')->sum('fine_amount')],
        ['Total Lost Books Fine Amount', BorrowRequest::where('status', 'lost')->sum('lost_fine')],
        ['Total Damage Books Fine Amount', BorrowRequest::where('status', 'damage')->sum('damage_fine')],
        ['', ''], // Empty row
        ['Monthly Lending Statistics', 'Total Borrowed'], // ခေါင်းစဉ်
    ];

    // ၂။ လအလိုက် Data (Jan-Dec) စုစည်းခြင်း
    // $monthlyBorrowing သည် DB မှလာသော [month, total] data ဖြစ်သည်
    // $monthlyBorrowing = BorrowRequest::selectRaw('MONTH(created_at) as month, count(*) as total')
    //     ->whereYear('created_at', date('Y'))
    //     ->where('status', '!=', 'canceled')
    //     ->groupBy('month')
    //     ->pluck('total', 'month')->toArray();

    // $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    
    // foreach ($months as $index => $monthName) {
    //     $monthNumber = $index + 1;
    //     $total = $monthlyBorrowing[$monthNumber] ?? 0;
    //     $data[] = [$monthName, $total];
    // }
    // ၂။ Dashboard တွင် အသုံးပြုသည့်အတိုင်း Year ကို ရယူပါ
    $selectedYear = $request->input('year', date('Y'));

    // ၃။ borrowed_at ကိုအခြေခံ၍ လအလိုက် Data ထုတ်ယူခြင်း
    $monthlyData = BorrowRequest::whereIn('status', ['pending', 'borrowed', 'overdue'])
        ->selectRaw('MONTH(borrowed_at) as month, count(*) as total')
        ->whereYear('borrowed_at', $selectedYear)
        ->whereNotNull('borrowed_at')
        ->groupBy('month')
        ->pluck('total', 'month')
        ->toArray();

    // ၄။ လ (၁၂) လစာအတွက် Data အစဉ်လိုက်ဖြစ်အောင် ပြင်ဆင်ခြင်း
    $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    
    foreach ($months as $index => $monthName) {
        $monthNumber = $index + 1;
        $total = $monthlyData[$monthNumber] ?? 0;
        $data[] = [$monthName, $total];
    }

    return Excel::download(new DashboardDataExport($data), 'Dashboard_Report_' . $selectedYear . '.xlsx');}
   
}
