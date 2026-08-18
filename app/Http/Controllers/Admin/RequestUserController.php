<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RequestUserController extends Controller
{
    public function details(Request $request)
    {
        if ($request->has('search') && !$request->filled('search')) {
        session()->forget('user_request_last_query');
    } elseif ($request->filled('search')) {
        session(['user_request_last_query' => $request->search]);
    } elseif (session()->has('user_request_last_query')) {
        $request->merge(['search' => session('user_request_last_query')]);
    }
        $search = $request->input('search');

        // Nested Eager Load: fetch user with year, member, and the member's payment info
        $query = User::with(['year', 'member.payment'])->where('is_approved', false);
        
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('roll_number', 'LIKE', "%{$search}%")
                  ->orWhereRaw("DATE_FORMAT(created_at, '%d-%b-%Y') LIKE ?", ['%' . $search . '%'])
                  ->orWhereHas('year', function ($subQuery) use ($search) {
                  $subQuery->where('academic_year', 'LIKE', "%{$search}%");
              })
              ->orWhereHas('member.payment', function ($subQuery) use ($search) {
                  $subQuery->where('account_type', 'LIKE', "%{$search}%");
              });
            });
        }

        $totalRequests = $query->count();
        
        $requests = $query->latest('created_at')->get();

        return view('admin.requestUser.home', compact('requests', 'totalRequests'));
    }

    public function accept($id)
    {
        $user = User::findOrFail($id);
        $user->update(['is_approved' => true]);
        return redirect()->back()->with('success', __('messages.registration_approved', ['name' => $user->name]));
    }

    public function reject($id)
    {
        $user = User::findOrFail($id);
        DB::table('members')->where('user_id', $user->id)->delete();
        $user->delete();
        return redirect()->back()->with('error', __('User registration request was rejected.'));
    }
}