<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rules\Password;

class AdminProfileController extends Controller
{
    // Profile Edit စာမျက်နှာကို ပြသရန်
    public function edit(Request $request)
    {
        return view('admin.profile.edit', [
            'user' => $request->user(),
        ]);
    }

    // Profile အချက်အလက် (Name/Email/Image) ကို Update လုပ်ရန်
    public function update(Request $request)
    {
        $admin = $request->user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $admin->id],
            'profile' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif'],
        ]);

        // ပုံဖိုင်ကိုင်တွယ်ခြင်း
        if ($request->hasFile('profile')) {
            // ပုံအဟောင်းရှိလျှင် ဖျက်ရန်
            if ($admin->profile && File::exists(public_path('userProfile/' . $admin->profile))) {
                File::delete(public_path('userProfile/' . $admin->profile));
            }

            $imageName = time() . '.' . $request->profile->extension();
            $request->profile->move(public_path('userProfile'), $imageName);
            $admin->profile = $imageName;
        }

        $admin->name = $request->name;
        $admin->email = $request->email;
        $admin->save();

        return redirect()->back()->with('success', __('Profile updated successfully!'));
    }

    // Password ပြောင်းလဲရန်
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', __('Password has been changed successfully.'));
    }
}