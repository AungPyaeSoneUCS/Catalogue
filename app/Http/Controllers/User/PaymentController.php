<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function details(){
        $payments=Payment::select('id','account_name','account_number','account_type','fee')->orderBy('account_type','asc')->get();
        return view('user.payment.home',compact('payments'));
    }
    public function create(Request $request){
    // 1. Validate form input fields
    $request->validate([
        'payment_method' => 'required',
        'payment_amount' => 'required',
        'receipt'        => 'required'
    ],[
        'payment_method.required'=>__('payment_method'),
        'receipt.required'=>__('receipt'),
    ]);

    try {
        // 2. Prepare data for database insertion
        $paymentHistoryData = [
            'user_id'        => auth()->user()->id,
            'payment_id' => $request->payment_method,
            'fee'   => $request->payment_amount
        ];
        
        // 3. Handle image upload
        if($request->hasFile('receipt')){
            $fileName = uniqid() . $request->file('receipt')->getClientOriginalName();
            $request->file('receipt')->move(public_path() . '/payslipImage/', $fileName);
            $paymentHistoryData['payslip'] = $fileName;
        }
        
        // 4. Save to database
        Member::create($paymentHistoryData);
        
        // Success Alert
        return to_route('user#paymentWait')->with(['success' => __('Payment submitted successfully!')]);

    } catch (\Exception $e) {
        
        // Error Alert if anything goes wrong during processing
        return back()->with(['error' => __('Something went wrong. Please try again.')]);
    }
}
    public function wait()
{
    $academic_year=User::select('years.academic_year as academic')
                    ->leftJoin('years','years.id','users.year_id')->where('users.id', Auth::id())->first();
    // လက်ရှိ login ဝင်ထားသော user ၏ အချက်အလက်ကို ထုတ်ယူခြင်း
    $memberData = Member::where('user_id', Auth::id())->first();

    // အကယ်၍ user က ငွေမသွင်းရသေးလျှင် တခြား page သို့ redirect လုပ်ပါ
    if (!$memberData) {
        return redirect()->route('payment#paymentDetails')->with('message', __('Please make a payment.'));
    }

    return view('user.payment.wait', compact('memberData','academic_year'));
}

}
