<section class="mb-4">
    <div class="gap-4 mb-4 d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <img id="previewImage" 
                            src="{{ Auth::user()->profile ? asset('userProfile/' . Auth::user()->profile) : asset('images/default-admin.png') }}" 
                            class="rounded-circle img-thumbnail" 
                            style="width: 100px; height: 100px; object-fit: cover;">
                    </div>

                    <div>
                        <h5 class="mb-1 fw-bold">{{ __('Profile Information') }}</h5>
                        <p class="mb-0 text-muted small">{{__('Update your account profile details.')}}</p>
                        {{__('Roll Number')}} <span class="text-primary fw-bold">{{ Auth::user()->roll_number }}</span>
                    </div>
                </div>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div class="mb-3">
            <label for="name" class="form-label">{{ __('Name') }}</label>
            <input id="name" name="name" type="text" class="form-control @error('name') is-invalid @enderror" 
                   value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" readonly>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">{{ __('Email') }}</label>
            <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                   value="{{ old('email', $user->email) }}" required autocomplete="username" readonly>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror

            
        </div>

        <div class="mb-3">
            <label for="phone" class="form-label">{{ __('Phone') }}</label>
            <input id="phone" name="phone" type="text" class="form-control @error('phone') is-invalid @enderror" 
                   value="{{ old('phone', Auth::user()->phone) }}">
            @error('phone')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
    <label for="profile" class="form-label">{{ __('Profile Image') }}</label>
            <input id="profile" name="profile" type="file" class="form-control @error('profile') is-invalid @enderror" accept="image/*" onchange="previewFile(event)">
            @error('profile')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <script>
            function previewFile(event) {
                const input = event.target;
                const reader = new FileReader();

                reader.onload = function() {
                    const dataURL = reader.result;
                    const output = document.getElementById('previewImage');
                    output.src = dataURL;
                };

                if (input.files && input.files[0]) {
                    reader.readAsDataURL(input.files[0]);
                }
            }
        </script>

        <div class="gap-3 mt-4 d-flex align-items-center">
            <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
        </div>
    </form>
</section>