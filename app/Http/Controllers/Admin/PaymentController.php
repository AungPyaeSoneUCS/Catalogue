<?php

namespace App\Http\Controllers\Admin;

use App\Exports\MembersFeesExport;
use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class PaymentController extends Controller
{
    /**
     * Display a listing of payments filtered by search parameters.
     */
    public function details(Request $request): View
    {
        if ($request->has('search') && !$request->filled('search')) {
        // Search ကို ဖျက်ပြီး Enter ခေါက်ရင် Session ဖျက်မယ်
        session()->forget('payment_last_query');
    } elseif ($request->filled('search')) {
        // Search အသစ်လုပ်ရင် Session မှာ သိမ်းမယ်
        session(['payment_last_query' => $request->search]);
    } elseif (session()->has('payment_last_query')) {
        // Search မပါလာဘူး၊ Session မှာ ရှိနေရင် Session ကဟာကို ပြန်သုံးမယ်
        $request->merge(['search' => session('payment_last_query')]);
    }
        $search = $request->get('search');

        $payments = Payment::query()
            ->when($search, fn($query) => $query->where(fn($q) => $q
                ->where('account_name', 'like', "%{$search}%")
                ->orWhere('account_number', 'like', "%{$search}%")
                ->orWhere('account_type', 'like', "%{$search}%")
                ->orWhereRaw("DATE_FORMAT(created_at, '%d-%M-%Y') LIKE ?", ["%{$search}%"])
            ))
            ->latest()
            ->get();

        return view('admin.payment.home', compact('payments'));
    }

    /**
     * Store a newly created payment method in storage.
     */
    public function store(Request $request): RedirectResponse
{
    $validator = Validator::make($request->all(), [
        'account_name'   => ['required', 'string', 'max:255'],
        'account_number' => ['required', 'string', 'max:255', 'unique:payments,account_number'],
        'account_type'   => ['required', 'string', 'max:255'],
        'fee'            => ['required', 'integer', 'min:0'],
    ], [
        'account_name.required'   => __('account_name_required'),
        'account_number.required' => __('account_number_required'),
        'account_number.unique'   => __('account_number_unique'),
        'account_type.required'   => __('account_type_required'),
        'fee.required'            => __('fee_required'),
        'fee.integer'             => __('fee_integer'),
        'fee.min'                 => __('fee_min'),
    ]);

    if ($validator->fails()) {
        return back()->withErrors($validator)
                     ->withInput()
                     ->with('modal_type', 'create'); // Create modal ကို ပြန်ဖွင့်ရန်
    }

    Payment::create($validator->validated());

    return to_route('list#paymentDetails')
        ->with('createSuccess', __('Payment method created successfully.'));
}

public function update(Request $request, Payment $payment): RedirectResponse
{
    $validator = Validator::make($request->all(), [
        'account_name'   => ['required', 'string', 'max:255'],
        'account_number' => ['required', 'string', 'max:255', "unique:payments,account_number,{$payment->id}"],
        'account_type'   => ['required', 'string', 'max:255'],
        'fee'            => ['required', 'integer', 'min:0'],
    ], [
        'account_name.required'   => __('account_name_required'),
        'account_number.required' => __('account_number_required'),
        'account_number.unique'   => __('account_number_unique'),
        'account_type.required'   => __('account_type_required'),
        'fee.required'            => __('fee_required'),
        'fee.integer'             => __('fee_integer'),
        'fee.min'                 => __('fee_min'),
    ]);

    if ($validator->fails()) {
        return back()->withErrors($validator)
                     ->withInput()
                     ->with('modal_type', 'edit')
                     ->with('payment_id', $payment->id); // Edit modal ကို ပြန်ဖွင့်ရန်
    }

    $payment->update($validator->validated());

    return to_route('list#paymentDetails')
        ->with('updateSuccess', __('Payment method updated successfully.'));
}
    public function memberFee(Request $request)
{
    if ($request->has('search') && !$request->filled('search')) {
        session()->forget('member_fee_last_query');
    } elseif ($request->filled('search')) {
        session(['member_fee_last_query' => $request->search]);
    } elseif (session()->has('member_fee_last_query')) {
        $request->merge(['search' => session('member_fee_last_query')]);
    }
    $search = $request->input('search');

    // 1. Query ကို Member table မှ စတင်ပါ
    $query = Member::with(['user.year','payment']);

    // 2. Search ရှိမှသာ Filter လုပ်ပါ
    if (!empty($search)) {
        $query->where(function ($q) use ($search) {
            // User ဆိုင်ရာ Search များ
            $q->whereHas('user', function ($sub) use ($search) {
                $sub->where('is_approved', true) // အကယ်၍ ဒါကို အမြဲသုံးချင်ရင် ဒီမှာထားပါ
                    ->where(function ($s) use ($search) {
                        $s->where('name', 'LIKE', "%{$search}%")
                          ->orWhere('roll_number', 'LIKE', "%{$search}%")
                          ->orWhereHas('year', function ($sq) use ($search) {
                              $sq->where('academic_year', 'LIKE', "%{$search}%");
                          });
                    });
            })
            // Payment ဆိုင်ရာ Search များ (အသစ်ထည့်ခြင်း)
            ->orWhereHas('payment', function ($p) use ($search) {
                $p->where('account_name', 'LIKE', "%{$search}%")
                  ->orWhere('account_number', 'LIKE', "%{$search}%")
                  ->orWhere('account_type', 'LIKE', "%{$search}%");
            })
            // Member table ထဲက Date Search (OR အနေနဲ့ ထည့်ခြင်း)
            ->orWhereRaw("DATE_FORMAT(members.created_at, '%d-%m-%Y') LIKE ?", ["%$search%"]);
        });
    } else {
        // Search မရှိရင်လည်း Approved ဖြစ်တာကိုပဲ ပြချင်ရင်
        $query->whereHas('user', fn($q) => $q->where('is_approved', true));
    }

    // 3. စုစုပေါင်းတွက်ချက်ရန် clone သုံးခြင်း
    $totalFees = (clone $query)->sum('fee');
    $userCount = (clone $query)->count();

    // 4. စာရင်းထုတ်ယူခြင်း
    $approvedMembers = $query->latest()->get();

    return view('admin.payment.member', compact('approvedMembers', 'totalFees', 'userCount'));
}
   public function exportMembers(Request $request)
{
    $search = $request->input('search');

    $query = Member::with(['user.year'])->whereHas('user', function($q) {
        $q->where('is_approved', true);
    });

    if (!empty($search)) {
        $query->where(function($q) use ($search) {
            $q->whereHas('user', function($sub) use ($search) {
                $sub->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('roll_number', 'LIKE', "%{$search}%")
                    ->orWhereHas('year', function($sq) use ($search) {
                        $sq->where('academic_year', 'LIKE', "%{$search}%");
                    });
            })->orWhereRaw("DATE_FORMAT(members.created_at, '%d-%m-%Y') LIKE ?", ["%$search%"]);
        });
    }

    $members = $query->latest()->get();
    $totalFees = $query->sum('fee');

    // Export class ထéသို့ $search ကိုပါ ထည့်ပေးလိုက်ခြင်း
    return Excel::download(new MembersFeesExport($members, $totalFees, $search), 'members_fees.xlsx');
}


    /**
     * Remove the specified payment method using Route Model Binding.
     */
    public function destroy(Payment $payment): RedirectResponse
    {
        $payment->delete();

        return to_route('list#paymentDetails')
            ->with('deleteSuccess', __('Payment method deleted successfully.'));
    }
}