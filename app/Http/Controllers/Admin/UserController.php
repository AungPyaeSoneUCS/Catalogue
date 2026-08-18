<?php

namespace App\Http\Controllers\Admin;
use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Year;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of all users joined with their respective year data.
     */
    public function details(Request $request)
    {
        if ($request->hasAny(['search', 'year_semester'])) {
        // User က တန်ဖိုးတစ်ခုခုရိုက်ထည့်လိုက်ရင် Session မှာ သိမ်းမယ်
        // တကယ်လို့ ဖျက်လိုက်ရင် (Empty string ဖြစ်ရင်) Session ကို ရှင်းမယ်
        if ($request->filled('search') || $request->filled('year_semester')) {
            session(['user_list_query' => $request->only(['search', 'year_semester'])]);
        } else {
            session()->forget('user_list_query');
        }
    } elseif (session()->has('user_list_query') && !$request->hasAny(['search', 'year_semester'])) {
        // Page ပြန်ဝင်လာရင် Session ထဲကဟာကို ပြန် merge လုပ်မယ်
        $request->merge(session('user_list_query'));
    }
        $years=Year::select('id','academic_year')->orderBy('id','desc')->get();
        // 1. Initialize query with explicit select and inner join
        $query = User::query()
            ->select('users.*', 'years.academic_year as year_name')
            ->join('years', 'users.year_id', '=', 'years.id');

        // 2. Handle Search Input (Targeting users table explicitly to prevent column ambiguity)
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('users.name', 'like', "%{$search}%")
                  ->orWhere('users.roll_number', 'like', "%{$search}%")
                  ->orWhere('users.email', 'like', "%{$search}%")
                  ->orWhereRaw("DATE_FORMAT(users.created_at, '%d-%b-%Y') LIKE ?", ['%' . $search . '%']);
            });
        }

        // 3. Handle Year Filter (Matching your year_id column)
        if ($request->has('year_semester') && $request->year_semester != '') {
            $query->where('users.year_id', $request->year_semester);
        }

        // Fetch records matching filters sorted by newest creations
        $query->where('users.is_approved', true)->where('users.role', 'user');
        $users = $query->latest('users.created_at')->get();

        return view('admin.userList.home', compact('users','years'));
    }

    /**
     * Update the specified user's password.
     */
    public function updatePassword(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        try {
            $user = User::findOrFail($id);
            $user->update([
                'password' => Hash::make($request->password)
            ]);

            return back()->with('success', __('User password updated successfully!'));
        } catch (\Exception $e) {
            return back()->with('error', __('Failed to update password. Please try again.'));
        }
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy($id)
{
    try {
        $user = User::findOrFail($id);
        
        // delete() ကိုခေါ်လိုက်တာနဲ့ Model ထဲက booted() အလုပ်လုပ်ပါမယ်
        // ပုံဖျက်တာ၊ စာအုပ် available ပြန်တိုးတာတွေက Model ထဲကနေပဲ အလိုအလျောက်လုပ်သွားပါမယ်
        $user->delete(); 

        return back()->with('success', __('User deleted successfully!'));
    } catch (\Exception $e) {
        // ဒီနေရာမှာ $e->getMessage() ကိုထုတ်ကြည့်ရင် အမှားတက်ရင် ဘာကြောင့်တက်လဲဆိုတာ အတိအကျသိနိုင်ပါတယ်
        // return back()->with('error', __('Something went wrong. Could not delete user.'));
        return back()->with('error', 'Error: ' . $e->getMessage());
    }
}

    public function export(Request $request)
{
    // ယူဆာ လက်ရှိ Filter လုပ်ထားသမျှ (Request) အားလုံးကို UsersExport ဆီ ပို့ပြီး Excel Download ဆွဲစေမယ်
    return Excel::download(new UsersExport($request), 'approved_users_list_' . now()->format('Y_m_d') . '.xlsx');
}
}