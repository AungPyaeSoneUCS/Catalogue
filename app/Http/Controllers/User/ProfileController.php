<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function rule(){
        return view('user.rule.rule');
    }
    public function about(){
        return view('user.rule.about');
    }
    public function faq(){
        return view('user.rule.faq');
    }
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
{
    $user = $request->user();

    // Validation ထည့်ပါ
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,'.$user->id,
        'phone' => 'nullable|string',
        'profile' => 'nullable|image|mimes:jpeg,png,jpg',
    ]);

    // Data ဖြည့်ခြင်း
    $user->name = $request->name;
    $user->email = $request->email;
    $user->phone = $request->phone;

    // Image Upload Logic

if ($request->hasFile('profile')) {
    // ၁။ ပုံအဟောင်းကို public/userProfile/ ထဲမှ ဖျက်ခြင်း
    if ($user->profile) {
        $oldImagePath = public_path('userProfile/' . $user->profile);
        if (File::exists($oldImagePath)) {
            File::delete($oldImagePath);
        }
    }

    // ၂။ ပုံအသစ်ကို public/userProfile/ ထဲသို့ သိမ်းခြင်း
    $imageName = time() . '.' . $request->profile->extension();
    $request->profile->move(public_path('userProfile'), $imageName);
    
    // ၃။ Database တွင် နာမည်အသစ်ကို သိမ်းခြင်း
    $user->profile = $imageName;
}

    $user->save();

    return Redirect::route('profile.edit')->with('success', __('Profile updated successfully!'));
}

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}

