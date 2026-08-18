<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => __('Email Field Error'),
            'email.email' => __('Email Valid Error'),
            'password.required' => __('Password Field Error'),
        ];
    }

    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $user = User::where('email', $this->email)->first();

        // ❌ Email not found
        if (! $user) {
            throw ValidationException::withMessages([
                'email' => __('Email Error'),
            ]);
        }

        // ❌ Wrong password
        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {

            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'password' => __('Password Error'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        // ❌ No seconds shown
        throw ValidationException::withMessages([
            'email' => 'Too many login attempts. Please try again later.',
        ]);
    }

    public function throttleKey(): string
    {
        return Str::transliterate(
            Str::lower($this->string('email')).'|'.$this->ip()
        );
    }
}