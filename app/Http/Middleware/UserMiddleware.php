<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // 1. User Login ဝင်ထားပြီး role က 'user' ဟုတ်မဟုတ် စစ်မယ်
        if ($user && $user->role == 'user') {
            
            // 2. Status က 'approved' ဟုတ်မဟုတ် ထပ်စစ်မယ်
            if ($user->is_approved != 1) {
                // Admin Approve မလုပ်ရသေးရင် Pending မျက်နှာဆီ လွှဲပေးမယ်
                return to_route('user#paymentWait');
            }

            // အားလုံးမှန်ကန်မှ Home Page ဆီ ဆက်သွားခွင့်ပေးမယ်
            return $next($request);
        }

        return back();
    }
}