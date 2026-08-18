@extends('admin.layout.master')

@section('content')
<div class="container py-5">
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Close') }}"></button>
        </div>
    @endif

    <div class="mb-4">
        <h2 class="fw-bold">{{ __('Admin Profile Settings') }}</h2>
    </div>

    <div class="row g-4">
        <div class="col-12 col-lg-7">
            <div class="p-4 bg-white border-0 shadow-lg bg-dark-subtle card h-100">
                <div class="gap-4 mb-4 d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <img id="previewImage" 
                            src="{{ Auth::user()->profile ? asset('userProfile/' . Auth::user()->profile) : asset('images/default-admin.png') }}" 
                            class="rounded-circle img-thumbnail" 
                            style="width: 100px; height: 100px; object-fit: cover;">
                    </div>

                    <div>
                        <h5 class="mb-1 fw-bold">{{ __('Profile Information') }}</h5>
                        <p class="mb-0 text-muted small">{{ __('Update your account profile details.') }}</p>
                    </div>
                </div>
                <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('patch')

                    <div class="mb-3">
                        <label class="form-label">{{ __('Profile Image') }}</label>
                        <input type="file" name="profile" class="form-control" onchange="previewFile(event)">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('Name') }}</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', Auth::user()->name) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('Email') }}</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', Auth::user()->email) }}" required>
                    </div>

                    <button type="submit" class="btn btn-primary">{{ __('Save Profile') }}</button>
                </form>
            </div>
        </div>

        <div class="col-12 col-lg-5">
            <div class="p-4 bg-white border-0 shadow-lg bg-dark-subtle card">
                <h5 class="mb-3 fw-bold">{{ __('Update Password') }}</h5>
                <form action="{{ route('admin.password.update') }}" method="POST">
                    @csrf
                    @method('put')

                    <div class="mb-3">
                        <label class="form-label">{{ __('Current Password') }}</label>
                        <input type="password" name="current_password" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('New Password') }}</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('Confirm Password') }}</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-dark">{{ __('Update Password') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function previewFile(event) {
        const reader = new FileReader();
        reader.onload = function() {
            document.getElementById('previewImage').src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    }
</script>
@endsection