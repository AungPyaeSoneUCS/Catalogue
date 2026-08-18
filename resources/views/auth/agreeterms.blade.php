<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>UCSH Digital Catalogue System</title>
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

        /* Mobile တွင် ခလုတ်နှင့် ခေါင်းစဉ်များ အနေအထား သပ်ရပ်စေရန် */
        @media (max-width: 991px) {
            .navbar-nav .nav-item {
                margin-bottom: 0.5rem;
            }

            .dropdown-menu {
                position: absolute !important;
                left: 0 !important;
                right: 0 !important;
                width: 95%;
                margin: 0 auto;
                top: 100% !important;
            }
        }
    </style>
</head>

<body class="d-flex flex-column min-vh-100"
    style="background-image: url('{{ asset('image/cover.jpg') }}'); background-size: cover; background-position: center; background-repeat: no-repeat; background-attachment: fixed;">

    <nav class="shadow-sm navbar navbar-expand-lg navbar-dark sticky-top" style="z-index: 1030;">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="">
                <img src="{{ asset('image/catalog-logo.jpg') }}" alt="Logo" width="45" height="45"
                    class="bg-white rounded-circle me-2">
                <span class="d-none d-sm-inline">Digital Catalogue System</span>
                <span class="d-inline d-sm-none fs-6">Digital Catalogue</span>
            </a>

            <button class="navbar-toggler text-warning" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"
                    style="background-image: url(&quot;data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(255, 193, 7, 1)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e&quot;);">
                </span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
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

    <div class="container my-4">
        <main class="row justify-content-center">
            <div class="col-12 col-lg-10">
                <div class="overflow-hidden border-0 shadow-lg card">
                    
                    <!-- Card Header (Mobile မှာပါ အဆင်ပြေစေရန် Flex ကို wrap သို့မဟုတ် responsive ဖြစ်အောင် ပြင်ထားသည်) -->
                    <div class="gap-2 px-3 py-3 bg-opacity-25 border-0 px-md-4 card-header bg-primary d-flex flex-column flex-md-row align-items-center justify-content-between">
                        
                        <!-- Back Button -->
                        <div class="d-flex align-items-center justify-content-between w-100 w-md-auto">
                            <a href="javascript:history.back()" class="rounded shadow-sm btn btn-sm btn-light text-primary fw-bold d-inline-flex align-items-center">
                                <i class="bi bi-arrow-left me-1"></i> {{__('Back')}}
                            </a>
                        </div>
                        
                        <!-- Title -->
                        <h4 class="mb-0 text-center text-primary fw-bold flex-grow-1 fs-5 fs-md-4">
                            <i class="bi bi-info-circle me-2"></i> {{ __('title_rule') }}
                        </h4>
                        
                        <!-- Spacer for Desktop Balance -->
                        <div style="width: 75px;" class="d-none d-md-block"></div>
                    </div>

                    <div class="p-3 p-md-5 card-body">
                        <p class="mb-4 text-center mb-md-5 text-muted px-md-5">
                            {{ __('description') }}
                        </p>

                        <div class="row g-3 g-md-4">
                            {{-- စာအုပ်ငှားရမ်းခြင်း --}}
                            <div class="col-md-6">
                                <div class="p-3 transition border h-100 rounded-3 hover-shadow d-flex align-items-start">
                                    <div class="p-3 bg-primary bg-opacity-10 text-primary rounded-circle me-3">
                                        <i class="bi bi-book-half fs-4"></i>
                                    </div>
                                    <div>
                                        <strong class="mb-1 text-dark d-block">{{ __('borrowing_title') }}</strong>
                                        <p class="mb-0 text-muted small">
                                            {{ __('borrowing_desc_1') }}
                                            <strong>{{ \App\Models\SystemSetting::where('key', 'max_borrow_limit')->value('value') ?? 3 }}</strong>
                                            {{ __('borrowing_desc_2') }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            {{-- ငှားရမ်း/သက်တမ်းတိုးကာလ --}}
                            <div class="col-md-6">
                                <div class="p-3 transition border h-100 rounded-3 hover-shadow d-flex align-items-start">
                                    <div class="p-3 bg-primary bg-opacity-10 text-primary rounded-circle me-3">
                                        <i class="bi bi-calendar-check fs-4"></i>
                                    </div>
                                    <div>
                                        <strong class="mb-1 text-dark d-block">{{ __('duration_title') }}</strong>
                                        <p class="mb-0 text-muted small">
                                            {{ __('duration_desc_1') }}
                                            <strong>{{ \App\Models\SystemSetting::where('key', 'borrow_duration_days')->value('value') ?? 7 }}</strong>
                                            {{ __('duration_desc_2') }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            {{-- နောက်ကျကြေး --}}
                            <div class="col-md-6">
                                <div class="p-3 transition border h-100 rounded-3 hover-shadow d-flex align-items-start">
                                    <div class="p-3 bg-primary bg-opacity-10 text-primary rounded-circle me-3">
                                        <i class="bi bi-cash-coin fs-4"></i>
                                    </div>
                                    <div>
                                        <strong class="mb-1 text-dark d-block">{{ __('fine_title') }}</strong>
                                        <p class="mb-0 text-muted small">
                                            {{ __('fine_desc') }}
                                            <strong>{{ \App\Models\SystemSetting::where('key', 'daily_fine_rate')->value('value') ?? 100 }}</strong>
                                            {{ __('currency') }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            {{-- ဘွတ်ကင် --}}
                            <div class="col-md-6">
                                <div class="p-3 transition border h-100 rounded-3 hover-shadow d-flex align-items-start">
                                    <div class="p-3 bg-primary bg-opacity-10 text-primary rounded-circle me-3">
                                        <i class="bi bi-clock-history fs-4"></i>
                                    </div>
                                    <div>
                                        <strong class="mb-1 text-dark d-block">{{ __('booking_title') }}</strong>
                                        <p class="mb-0 text-muted small">
                                            {{ __('booking_desc_1') }}
                                            <strong>{{ \App\Models\SystemSetting::where('key', 'booking_expire_hours')->value('value') ?? 24 }}</strong>
                                            {{ __('booking_desc_2') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 border-0 mt-md-5 alert alert-warning rounded-3 d-flex align-items-center">
                            <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
                            <small class="mb-0">
                                **{{ __('note_title') }}** {{ __('note_desc') }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <footer class="py-4 mt-auto text-center text-white" style="background-color:#51816C">
        <div class="container">
            <p class="mb-1">&copy; {{ date('Y') }} Digital Catalogue System. All rights reserved.</p>
            <small>{{ __('footer_credit') }}</small>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>