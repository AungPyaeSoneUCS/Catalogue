<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Digital Catalogue System</title>
    <link rel="icon" type="image/png" href="{{ asset('image/catalog-logo.jpg') }}">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@7.0.0/css/flag-icons.min.css" />
    <!-- Font Awesome for Flags -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Flag Icons CSS -->
    

    <style>
        body {
            background-color: #94EFEE;
            font-family: 'Instrument Sans', sans-serif;
        }

        .auth-card {
            border: none;
            border-radius: 12px;
            background: #ffffff;
        }

        /* Style for the dropdown items */
        .dropdown-item i {
            width: 20px;
        }

        /* Mobile တွင် ခလုတ်များ အဆင်ပြေစေရန် */
        @media (max-width: 991px) {
            .navbar-nav .nav-item {
                margin-bottom: 0.5rem;
            }

            .btn {
                width: 100%;
            }

            .dropdown-menu {
                position: absolute !important;
                left: 0 !important;
                right: 0 !important;
                width: 95%;
                /* ဖုန်း screen အကျယ်အတိုင်း ပေါ်လာအောင် */
                margin: 0 auto;
                top: 100% !important;
            }
        }
    </style>
</head>

<body class="d-flex flex-column min-vh-100" style="background-image: url('{{ asset('image/cover.jpg') }}'); background-size: cover; background-position: center;background-repeat: no-repeat;
             background-attachment: fixed;">

    <nav class="mb-5 shadow-sm navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center" 
   @auth 
       {{-- Auth ဖြစ်နေရင် (Login ဝင်ထားရင်) tooltip attribute လုံးဝမထည့်ပါ --}}
   @else 
       data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="{{ __('welcome') }}" 
   @endauth 
   href="{{ url('/') }}">
    <img src="{{ asset('image/catalog-logo.jpg') }}" alt="Logo" width="50" height="50"
         class="bg-white rounded-circle me-2">
    <span class="d-none d-sm-inline">Digital Catalogue System</span>
    <span class="d-inline d-sm-none fs-6">Digital Catalogue System</span>
</a>

            <button class="navbar-toggler text-warning" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarNav">
                {{-- <span class="navbar-toggler-icon"></span> --}}
                <span class="navbar-toggler-icon"
                    style="background-image: url(&quot;data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(255, 193, 7, 1)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e&quot;);">
                </span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- ms-auto pushes the following items to the right -->
                <ul class="navbar-nav ms-auto align-items-center">
                    @if (Route::has('login'))
                        @auth
                            @if (Auth::check() && Auth::user()->is_approved == 1)
                                <li class="nav-item">
                                    <a href="{{ route('user#home') }}"
                                        class="px-4 shadow-sm btn btn-outline-light btn-sm text-info fw-bold me-2">{{ __('home') }}</a>
                                </li>
                            @else
                                <li class="nav-item">
                                    <span class="px-4 shadow-sm text-info fw-bold">{{ __('Payment Page') }}</span>
                                </li>
                            @endif
                            <li class="nav-item">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                                        <i class="bi bi-box-arrow-right me-2"></i> {{ __('logout') }}
                                    </button>
                                </form>
                            </li>
                        @else
                            <li class="nav-item">
                                <a href="{{ route('login') }}" class="px-4 btn btn-outline-light btn-sm me-2">
                                    {{ __('Login') }}
                                </a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a href="{{ route('register') }}" class="px-4 btn btn-outline-light btn-sm">
                                        {{ __('Register') }}
                                    </a>
                                </li>
                            @endif
                        @endauth
                    @endif

                    <!-- Language Switcher integrated into the same list for better alignment -->
                    <li class="nav-item dropdown ms-lg-3">
                        <a class="text-white nav-link dropdown-toggle fw-bold" href="" id="langDropdown"
                            role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            @if (app()->getLocale() == 'mm')
                                <span class="fi fi-mm me-1"></span> MM
                            @else
                                <span class="fi fi-gb me-1"></span> EN
                            @endif
                        </a>
                        <ul class="border-0 shadow-sm dropdown-menu dropdown-menu-end" aria-labelledby="langDropdown">
                            <li>
                                <a class="dropdown-item {{ app()->getLocale() == 'en' ? 'active bg-info' : '' }}"
                                    href="{{ route('lang.switch', 'en') }}">
                                    <span class="fi fi-gb me-2"></span> English
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ app()->getLocale() == 'mm' ? 'active bg-info' : '' }}"
                                    href="{{ route('lang.switch', 'mm') }}">
                                    <span class="fi fi-mm me-2"></span> Myanmar
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container">
        <main class="row justify-content-center">

            @yield('content')
        </main>
    </div>
<footer class="py-4 mt-auto text-center text-white " style="background-color:#51816C">
    <div class="container">
        <p class="mb-1">&copy; {{ date('Y') }} Digital Catalogue System. All rights reserved.</p>
        <small>{{ __('footer_credit') }}</small>
    </div>
</footer>
    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
@yield('script-code')

<script>
    function loadImage(event) {
        let output = document.getElementById('output');
        output.style.width = "300px";
        output.style.height = "600px";
        output.style.objectFit = "cover";
        var reader = new FileReader();

        reader.onload = function() {
            output.src = reader.result
        }
        reader.readAsDataURL(event.target.files[0])
    }

    function loadFile(event) {
        let output = document.getElementById('output');
        output.style.width = "100px";
        output.style.height = "100px";
        output.style.objectFit = "cover";
        var reader = new FileReader();

        reader.onload = function() {
            output.src = reader.result
        }
        reader.readAsDataURL(event.target.files[0])
    }
</script>
<script>
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })
        });
    </script>
</html>
