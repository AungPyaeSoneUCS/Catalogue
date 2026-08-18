<section class="mb-4">
    <header class="mb-4">
        <h2 class="h5 text-dark">
            {{ __('Update Password') }}
        </h2>
        
    </header>

    <form method="post" action="{{ route('password.update') }}">
        @csrf
        @method('put')

        <div class="mb-3">
            <label for="update_password_current_password" class="form-label">{{ __('Current Password') }}</label>
            <input id="update_password_current_password" name="current_password" type="password" 
                   class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" 
                   autocomplete="current-password" />
            @error('current_password', 'updatePassword')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="update_password_password" class="form-label">{{ __('New Password') }}</label>
            <input id="update_password_password" name="password" type="password" 
                   class="form-control @error('password', 'updatePassword') is-invalid @enderror" 
                   autocomplete="new-password" />
            @error('password', 'updatePassword')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="update_password_password_confirmation" class="form-label">{{ __('Confirm Password') }}</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" 
                   class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror" 
                   autocomplete="new-password" />
            @error('password_confirmation', 'updatePassword')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="gap-3 d-flex align-items-center">
            <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>

            @if (session('status') === 'password-updated')
                <div class="text-success small fw-bold">
                    {{ __('Saved.') }}
                </div>
            @endif
        </div>
    </form>
</section>
