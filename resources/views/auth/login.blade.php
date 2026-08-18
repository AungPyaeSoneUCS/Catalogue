
@extends('auth.layout.master')
@section('content')
    <div class="container fw-bold">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="bg-white border shadow-lg bg-opacity-10 border-primary border-3 text-dark card auth-card" style="backdrop-filter: blur(6.5px); border-radius: 20px;"> 
                {{-- #7DAFA3 --}}
                <div class="p-4 text-white card-body">
                    <h3 class="pb-2 mb-4 text-center border-2 border-bottom fw-bold">{{ __('Login') }}</h3>

                    <!-- Session Status -->
                    @if (session('status'))
                        <div class="mb-4 alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <!-- Email Address -->
                        <div class="mb-3">
                            <label for="email" class="form-label">{{ __('Email Address') }}</label>
                            <input id="email" type="text" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   name="email" value="{{ old('email') }}" 
                                   autofocus autocomplete="username">
                            
                            @error('email')
                                <div class="invalid-feedback fw-bold">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label">{{ __('Password') }}</label>
                            <input id="password" type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   name="password"  autocomplete="current-password">

                            @error('password')
                                <div class="invalid-feedback fw-bold">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        
                        <div class="mt-4 d-flex align-items-center justify-content-between">
                                <a class="text-white border-3 text-decoration-none small fw-bold border-bottom border-primary" href="{{ route('register') }}">
                                    {{ __('Don\'t have an account?') }}
                                </a>

                                <button type="submit" class="px-4 btn btn-outline-light">
                                    {{ __('Login') }}
                                </button>
                            </div>
                        {{-- <div class="mt-4 d-flex justify-content-end">
                            <button type="submit" class="px-4 btn btn-primary">
                                {{ __('Login') }}
                            </button>
                        </div> --}}
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection