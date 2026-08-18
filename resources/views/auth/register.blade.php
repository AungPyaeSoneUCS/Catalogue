@extends('auth.layout.master')
@section('content')
    <div class="container mb-2 fw-bold">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="bg-white border shadow-lg border-3 border-primary bg-opacity-10 text-dark card auth-card" style="backdrop-filter: blur(6.5px); border-radius: 20px;">
                    <div class="p-4 text-white card-body">
                        <div class="pb-3 mb-4 border-2 row align-items-center border-bottom border-primary">
                            <!-- Title -->
                            <div class="text-center col-md-6 text-md-start text-nowrap">
                                <h3 class="mb-0 border-2 fw-bold">{{ __('Create Account') }}</h3>
                            </div>

                            <!-- Profile Image Preview -->
                            <div class="mt-1 text-center col-md-6 text-md-end">
                                <img id="output" src="" class="shadow rounded-circle">
                            </div>

                        </div>
                        <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
                            @csrf

                            <div class="row">

                                <!-- Name -->
                                <div class="mb-3 col-md-6">
                                    <label for="name" class="form-label">{{ __('Name') }}</label>
                                    <input id="name" type="text"
                                        class="form-control @error('name') is-invalid @enderror" name="name"
                                        value="{{ old('name') }}" placeholder="Mg Mg">

                                    @error('name')
                                        <div class="invalid-feedback fw-bold">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <!-- Roll Number -->
                                <div class="mb-3 col-md-6">
                                    <label for="roll_number" class="form-label">{{ __('Roll Number') }}</label>
                                    <input id="roll_number" type="text"
                                        class="form-control @error('roll_number') is-invalid @enderror" name="roll_number"
                                        value="{{ old('roll_number') }}" placeholder="5CS-1">

                                    @error('roll_number')
                                        <div class="invalid-feedback fw-bold">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                            </div>

                            <div class="row">

                                <!-- Email -->
                                <div class="mb-3 col-md-6">
                                    <label for="email" class="form-label">{{ __('Email Address') }}</label>
                                    <input id="email" type="text"
                                        class="form-control @error('email') is-invalid @enderror" name="email"
                                        value="{{ old('email') }}" autocomplete="username" placeholder="example@gmail.com">

                                    @error('email')
                                        <div class="invalid-feedback fw-bold">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <!-- Phone -->
                                <div class="mb-3 col-md-6">
                                    <label for="phone" class="form-label">{{ __('Phone Number') }}</label>
                                    <input id="phone" type="text"
                                        class="form-control @error('phone') is-invalid @enderror" name="phone"
                                        value="{{ old('phone') }}" placeholder="09XXXXXXXXX">

                                    @error('phone')
                                        <div class="invalid-feedback fw-bold">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                            </div>

                            <div class="row">

                                <!-- Password -->
                                <div class="mb-3 col-md-6">
                                    <label for="password" class="form-label">{{ __('Password') }}</label>
                                    <input id="password" type="password"
                                        class="form-control @error('password') is-invalid @enderror" name="password"
                                        autocomplete="new-password">

                                    @error('password')
                                        <div class="invalid-feedback fw-bold">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <!-- Confirm Password -->
                                <div class="mb-3 col-md-6">
                                    <label for="password_confirmation"
                                        class="form-label">{{ __('Confirm Password') }}</label>
                                    <input id="password_confirmation" type="password"
                                        class="form-control @error('password_confirmation') is-invalid @enderror"
                                        name="password_confirmation" autocomplete="new-password">

                                    @error('password_confirmation')
                                        <div class="invalid-feedback fw-bold">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                            </div>

                            <div class="row">

                                <!-- Profile Image -->
                                <div class="mb-3 col-md-6">
                                    <label for="profile_image" class="form-label">{{ __('Profile Image') }}</label>
                                    <input id="profile_image" type="file" onchange="loadFile(event)"
                                        class="form-control @error('profile_image') is-invalid @enderror"
                                        name="profile_image" accept="image/*">

                                    @error('profile_image')
                                        <div class="invalid-feedback fw-bold">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>


                                <!-- Year Dropdown -->
                                <div class="mb-3 col-md-6">
                                    <label for="year" class="form-label">{{ __('Academic Year') }}</label>
                                    <select id="year" class="form-select @error('year') is-invalid @enderror"
                                        name="year">

                                        <option value="">{{ __('Select Year') }}</option>

                                        @foreach ($years as $item)
                                            @if ($item->academic_year != 'Admin')
                                                <option value="{{ $item->id }}"
                                                    @if ($item->id == old('year')) selected @endif>
                                                    {{ $item->academic_year }}
                                                </option>
                                            @endif
                                        @endforeach

                                    </select>

                                    @error('year')
                                        <div class="invalid-feedback fw-bold">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                            </div>
                            <!-- Library Guidelines and Terms Checkbox -->
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input @error('terms') is-invalid @enderror" 
                                    id="terms" name="terms" value="1" {{ old('terms') ? 'checked' : '' }}>
                                <label class="text-white form-check-label" for="terms">
                                    
                                    <a href="{{ route('agree#terms') }}"  class="text-primary text-decoration-underline">{{ __('IAgree') }}</a>
                                </label>
                                
                                @error('terms')
                                    <div class="invalid-feedback fw-bold">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="mt-4 d-flex align-items-center justify-content-between">
                                <a class="text-white border-3 text-decoration-none small fw-bold border-bottom border-primary" href="{{ route('login') }}">
                                    {{ __('Already registered?') }}
                                </a>

                                <button type="submit" class="px-4 btn btn-outline-light">
                                    {{ __('Register') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
