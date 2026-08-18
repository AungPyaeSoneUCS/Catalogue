<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
{
    $validated = $request->validateWithBag('updatePassword', [
        'current_password' => ['required', 'current_password'],
        'password' => ['required', Password::defaults(), 'confirmed'],
    ]);

    $request->user()->update([
        'password' => Hash::make($validated['password']),
    ]);

    // 🎯 ပြင်ဆင်ချက်: 'status' => 'password-updated' အစား 
    // 'success' => '...' ဆိုပြီး ပြောင်းလဲပေးလိုက်ခြင်း
    return back()->with('success', 'Password updated successfully!');
}
}
