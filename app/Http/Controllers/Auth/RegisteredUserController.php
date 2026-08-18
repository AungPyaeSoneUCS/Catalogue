<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Year;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $years=Year::select('id','academic_year')->orderBy('id','desc')->get();
        return view('auth.register',compact('years'));
    }
 
    public function agree(){
        return view('auth.agreeterms');
    }
    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['required', 'string', 'lowercase', 'email:rfc,dns', 'max:255', 'unique:'.User::class],
            'password' => ['required','min:8'],
            'roll_number'=>['required','unique:'.User::class],
            'year'=>['required'],
            'phone'=>['required','max:11'],
            'profile_image'=>['required'],
            'password_confirmation' => ['required','same:password'],
            'terms'=>['required'],
        ],[
            'name.required'=> __('Name Error'),
            'email.required'=>__('Email Field Error'),
            'email.email'=>__('The email field must be a valid email address.'),
             'roll_number.required'=>__('Roll Number Error'),
             'roll_number.unique'=>__('unique_roll'),
             'year.required'=>__('Year Id Error'),
             'phone.required'=>__('Phone Error'),
             'phone.max'=>__('Phone Max Error'),
             'profile_image.required'=>__('Profile Image Error'),
            'password.required'=>__('Password Field Error'),
            'password.min'=>__('Password Min Error'),
            'password_confirmation'=>__('Password Confirm Field Error'),
            'password_confirmation.same'=>__('Password Same Error'),
            'terms.required'=>__('agreeRequire'),
        ]);

        // ✅ Image upload
            $imageName = null;

            if ($request->hasFile('profile_image')) {
                
                $imageName = uniqid().'_'.$request->file('profile_image')->getClientOriginalName();

                $request->file('profile_image')->move(
                    public_path('userProfile/'),
                    $imageName
                );
            }
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'roll_number'=>$request->roll_number,
            'year_id'=>$request->year,
            'phone'=>$request->phone,
            'profile' => $imageName, 
            'password' => Hash::make($request->password),

        ]);

        event(new Registered($user));

        Auth::login($user);

        // return redirect(route('dashboard', absolute: false));
        //return to_route('dashboard');
        if ($request->user()->is_approved == true) {
        return to_route('user#home');
    }
        return to_route('user#paymentWait');
    }
}
