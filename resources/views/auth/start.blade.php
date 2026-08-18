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
    
    <!-- Flag Icons CSS -->
    <!-- Bootstrap 5 CSS -->
    
    
    <!-- Bootstrap Icons -->
    
    
    <!-- Flag Icons -->
    
    
    <!-- Font Awesome 6 CSS (Correct CDN) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

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
        /* မူလ Laptop/Desktop အတွက် ပုံ */
.custom-bg {
    background-image: url('{{ asset("image/cc1.jpg") }}');
    background-size: cover;
    background-position: center center;
    background-repeat: no-repeat;
    background-attachment: fixed;
}

/* Screen width 768px အောက် (Mobile ဖုန်းများ) အတွက် ပုံ */
@media (max-width: 768px) {
    .custom-bg {
        background-image: url('{{ asset("image/cc2.jpg") }}');
    }
}

    /* ... မူလ CSS များ ... */

    /* Typewriter Animation Styles */
    .typewriter-container {
        display: inline-block;
    }
    
    .typewriter-text {
        display: inline-block;
        overflow: hidden;
        border-right: .15em solid #ffc107; /* Blinking cursor အရောင် (Warning Yellow) */
        white-space: nowrap;
        margin: 0 auto;
        letter-spacing: .15em;
        animation: 
            typing 3.5s steps(30, end) infinite,
            blink-caret .75s step-end infinite;
    }

    /* စာလုံးတစ်လုံးချင်းစီ ပေါ်လာပြီး ပြန်ပျောက်သွားရန် Keyframes */
    @keyframes typing {
        0% { width: 0; }
        50% { width: 100%; }
        85% { width: 100%; } /* အကုန်ပေါ်ပြီး ခဏရပ်ထားမည် */
        100% { width: 0; }  /* ပြန်ပြတ်သွားမည် (ပြန်ပျောက်မည်) */
    }

    /* Cursor ခုန်နေသည့် ပုံစံ */
    @keyframes blink-caret {
        from, to { border-color: transparent; }
        50% { border-color: #ffc107; }
    }

    </style>
</head>

{{-- <body class="d-flex flex-column min-vh-100" style="background-image: url('{{ asset('image/s4.jpg') }}'); 
             background-size: cover; background-position: center center;background-repeat: no-repeat:background-attachment:fixed;
             background-attachment: fixed;"> --}}
<body class="d-flex flex-column min-vh-100 custom-bg">
    <nav class="mb-5 shadow-sm navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="{{ url('/') }}">
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

            
<div class="col-12">
    <!-- Hero / Main Search Section -->
    <div class="my-4 text-center text-light">
        <h1 class=" fw-bold display-5">Library Digital Catalogue System</h1>
        <p class="text-white fs-5">{{__('Search_')}} <span class="mx-2">•</span> {{__('Discover')}} <span class="mx-2">•</span> {{__('Read')}}</p>
        <p class="fw-semibold">{{ __('Find the book you need in seconds.') }}</p>

        

        <!-- Navigation Quick Icons -->
        <div class="mt-5 text-center row justify-content-center g-4">
            <div class="col-6 col-md-3">
                <p  class="text-white text-decoration-none">
                    <div class="mx-auto bg-white shadow text-dark rounded-circle d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                        <i class="fas fa-book fa-2x text-primary"></i>
                    </div>
                    <h6 class="mt-2 fw-bold">{{ __('Browse Books') }}</h6>
                    <small class="text-white-50">{{ __('Explore our collection') }}</small>
                </p>
            </div>
            <div class="col-6 col-md-3">
                <p  class="text-white text-decoration-none">
                    <div class="mx-auto bg-white shadow text-dark rounded-circle d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                        <i class="fas fa-user fa-2x text-primary"></i>
                    </div>
                    <h6 class="mt-2 fw-bold">{{ __('Author') }}</h6>
                    <small class="text-white-50">{{ __('Find more by authors') }}</small>
                </p>
            </div>
            <div class="col-6 col-md-3">
                <p  class="text-white text-decoration-none">
                    <div class="mx-auto bg-white shadow text-dark rounded-circle d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                        <i class="fas fa-layer-group fa-2x text-primary"></i>
                    </div>
                    <h6 class="mt-2 fw-bold">{{ __('Category Name') }}</h6>
                    <small class="text-white-50">{{ __('Browse by subject') }}</small>
                </p>
            </div>
            <div class="col-6 col-md-3">
                <p  class="text-white text-decoration-none">
                    <div class="mx-auto bg-white shadow text-dark rounded-circle d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                        <i class="fas fa-bookmark fa-2x text-primary"></i>
                    </div>
                    <h6 class="mt-2 fw-bold">{{ __('My Library') }}</h6>
                    <small class="text-white-50">{{ __('Manage your loans') }}</small>
                </p>
            </div>
        </div>
    </div>

    <!-- Welcome Banner with Login Button -->
    <div class="p-4 mt-5 bg-opacity-75 border-0 shadow-sm card rounded-4 bg-light backdrop-blur">
        <div class="row align-items-center">
            <div class="col-md-9 d-flex align-items-center">
                <div class="p-3 text-white bg-primary rounded-3 me-3 d-flex align-items-center justify-content-center" style="width: 55px; height: 55px;">
                    <i class="fas fa-book-open fa-lg"></i>
                </div>
                <div>
                    <h4 class="mb-1 fw-bold text-dark">{{ __('Welcome to Our Library!') }}</h4>
                    <p class="mb-0 text-muted">{{ __('Access thousands of books and resources through our Digital Catalogue System.') }}</p>
                </div>
            </div>
            <div class="mt-3 col-md-3 text-md-end mt-md-0">
                <a href="{{ route('login') }}" class="px-4 shadow-sm btn btn-primary btn-lg rounded-pill fw-bold" data-bs-toggle="tooltip" data-bs-placement="bottom"
                            data-bs-title="{{ __('login') }}">
                    <i class="fas fa-sign-in-alt me-2"></i> {{__('Login')}}
                </a>
            </div>
        </div>
    </div>
</div>

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
