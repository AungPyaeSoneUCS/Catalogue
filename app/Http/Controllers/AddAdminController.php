<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Year;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request; // 👈 မှန်ကန်သော Request Class ကို Import လုပ်ရန်
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AddAdminController extends Controller
{
    public function addAdmin(): View
    {   $librarians = User::where('role', 'admin')->orderBy('id', 'desc')->get();
        $years = Year::select('id', 'academic_year')->orderBy('id', 'desc')->get();
        return view('admin.addAdmin.addAdmin', compact('years','librarians'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email:rfc,dns', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'min:8'],
            'profile_image' => ['required', 'image', 'mimes:jpeg,png,jpg'],
            'password_confirmation' => ['required', 'same:password'],
        ], [
            'name.required' => __('Name Error'),
            'email.required' => __('Email Field Error'),
            'email.email' => __('The email field must be a valid email address.'),
            'email.unique' => __('The email has already been taken.'),
            'profile_image.required' => __('Profile Image Error'),
            'password.required' => __('Password Field Error'),
            'password.min' => __('Password Min Error'),
            'password_confirmation.same' => __('Password Same Error'),
        ]);

        // ✅ Image upload
        $imageName = null;

        if ($request->hasFile('profile_image')) {
            $imageName = uniqid() . '_' . $request->file('profile_image')->getClientOriginalName();

            $request->file('profile_image')->move(
                public_path('userProfile/'),
                $imageName
            );
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'roll_number' => 'ADMIN-' . rand(1000, 9999), // 👈 Database သတ်မှတ်ချက်အရ ယာယီထည့်ပေးခြင်း
            'year_id' => 11, // သင့် Database ပုံစံအပေါ်မူတည်ပြီး Admin အတွက် သတ်မှတ်ထားသော Year ID ထည့်ပါ
            'phone' => '09768466475',
            'role' => 'admin', // Admin (သို့) Superadmin
            'profile' => $imageName, 
            'password' => Hash::make($request->password),
            'is_approved' => true, // Admin ဖြစ်므로 အလိုအလျောက် ဖွင့်ပေးမည်
        ]);

        event(new Registered($user));

        // ⚠️ Admin အသစ်ထည့်တာဖြစ်လို့ အဓိက Admin အကောင့်ကနေ ထွက်သွားမှာစိုးလို့ 
        // Auth::login($user); ကို ဖြုတ်ထားပေးလိုက်ပါတယ် (လိုအပ်မှသာ ပြန်ထည့်ပါ)

        return to_route('admin.create')
            ->with('addSuccess', __('Librarian added successfully.'));
    }
    public function destroy($id): RedirectResponse
{
    $librarian = User::findOrFail($id);

    // Profile ပုံရှိပါက public/userProfile/ မှပါ ဖျက်မည်
    if ($librarian->profile && file_exists(public_path('userProfile/' . $librarian->profile))) {
        unlink(public_path('userProfile/' . $librarian->profile));
    }

    $librarian->delete();

    return back()->with('deleteSuccess', __('Librarian deleted successfully.'));
}
}