<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-100">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Digital Catalogue System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@7.0.0/css/flag-icons.min.css" />
    <style>
        /* Ensures the navbar doesn't get too cramped */
        .navbar-nav .nav-link {
            margin: 0 4px;
        }
    </style>
</head>

<body class="bg-light d-flex flex-column h-100">

    <nav class="py-1 shadow-sm navbar navbar-expand-lg navbar-dark bg-gradient fixed-top"
        style="z-index: 2000; background-color: #1A237E; ">
        <div class="container">
            <a class="gap-2 text-white navbar-brand fw-bold d-flex align-items-center" href="{{ route('user#home') }}">
                <img src="{{ asset('image/catalog-logo.jpg') }}" alt="Logo" width="50" height="50"
                    class="bg-white rounded-circle">
                <span class="d-none d-sm-inline">Digital Catalogue System</span>
                <span class="d-inline d-sm-none fs-6">Digital Catalogue System</span>
            </a>

            <button class="border-0 navbar-toggler text-warning" type="button" data-bs-toggle="collapse"
                data-bs-target="#navMenu">
                {{-- <span class="navbar-toggler-icon"></span> --}}
                <span class="navbar-toggler-icon"
                    style="background-image: url(&quot;data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(255, 193, 7, 1)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e&quot;);">
                </span>
            </button>

            <div class="collapse navbar-collapse" id="navMenu">
                <ul class="navbar-nav ms-auto align-items-lg-center">

                    @foreach ([['label' => 'home', 'route' => 'user#home', 'tooltip' => 'Go to home'], ['label' => 'borrow_list', 'route' => 'user#currentBorrows', 'tooltip' => 'View your borrows'], ['label' => 'booking_list', 'route' => 'user#bookingHistory', 'tooltip' => 'Check booking history']] as $item)
                        <li class="nav-item">
                            <a href="{{ route($item['route']) }}"
                                class="nav-link px-3 {{ request()->routeIs($item['route']) ? 'active fw-bold text-white' : 'style:color:#616161' }}"
                                data-bs-toggle="tooltip" data-bs-placement="bottom"
                                data-bs-title="{{ __($item['tooltip']) }}">
                                {{ __($item['label']) }}
                            </a>
                        </li>
                    @endforeach

                    <li class="nav-item">
                        <a href="{{ route('chat.start') }}"
                            class="nav-link px-3 {{ request()->routeIs('chat.index') ? 'active fw-bold text-white' : 'style:color:#616161' }}"
                            data-bs-toggle="tooltip" data-bs-placement="bottom"
                            data-bs-title="{{ __('send message') }}">
                            {{ __('contact') }}
                            @if (isset($unreadCount) && $unreadCount > 0)
                                <span class="badge rounded-pill bg-danger">{{ $unreadCount }}</span>
                            @endif
                        </a>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-white-50" href="#" data-bs-toggle="dropdown">
                            <span class="fi fi-{{ app()->getLocale() == 'mm' ? 'mm' : 'gb' }} me-1"></span>
                        </a>
                        <ul class="mt-2 border-0 shadow dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('lang.switch', 'en') }}"><span
                                        class="fi fi-gb me-2"></span> English</a></li>
                            <li><a class="dropdown-item" href="{{ route('lang.switch', 'mm') }}"><span
                                        class="fi fi-mm me-2"></span> Myanmar</a></li>
                        </ul>
                    </li>

                    <li class="nav-item dropdown ms-lg-3">
                        <button class="p-0 border-2 border-white shadow-sm btn btn-light rounded-circle"
                            style="width: 36px; height: 36px;" data-bs-toggle="dropdown">
                            @if (auth()->check() && auth()->user()->profile)
                                <img src="{{ asset('userProfile/' . auth()->user()->profile) }}" alt="Profile"
                                    class="rounded-circle w-100 h-100" style="object-fit: cover;">
                            @else
                                <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'User') }}&background=0dcaf0&color=fff"
                                    alt="Profile" class="rounded-circle w-100 h-100">
                            @endif
                        </button>
                        <ul class="mt-2 border-0 shadow dropdown-menu dropdown-menu-end">
                            <li class="px-3 py-2 small fw-bold text-muted">{{ Auth::user()->name }}</li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i
                                        class="bi bi-person me-2"></i>{{ __('profile') }}</a></li>
                            <li><a href="{{ asset('document/ucsh_user_guide.pdf') }}" target="_blank"
                                class="dropdown-item">
                                <i class="bi bi-file-earmark-pdf me-2"></i> {{ __('Read User Guide') }}
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('rule') }}"><i
                                        class="bi bi-info-circle me-2"></i>{{ __('title_rule') }}</a></li>
                            <li class="px-2 py-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-sm w-100 fw-bold"><i
                                            class="bi bi-box-arrow-right me-1"></i> {{ __('logout') }}</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="flex-shrink-0 mt-3 flex-grow-1">
        @yield('content')
    </main>

    <footer class="mt-auto text-white" style="background-color: #1A237E;">
        <div class="container py-5">
            <div class="row g-4">
                <div class="col-lg-3 col-md-6">
                    <h5 class="mb-3 fw-bold text-warning">Digital Catalogue System</h5>
                    <p class="text-white-50 small">
                        {{ __('welcome_footer') }}
                    </p>
                    <div class="gap-3 d-flex">
                        <a href="#" class="text-white fs-5 hover-warning"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="text-white fs-5 hover-warning"><i class="bi bi-telegram"></i></a>
                        <a href="#" class="text-white fs-5 hover-warning"><i
                                class="bi bi-envelope-fill"></i></a>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <h5 class="mb-3 fw-bold text-warning">Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('user#home') }}"
                                class="text-white-50 text-decoration-none hover-link">{{ __('home') }}</a></li>
                        <li><a href="{{ route('rule') }}"
                                class="text-white-50 text-decoration-none hover-link">{{ __('title_rule') }}</a></li>
                        <li><a href="{{ route('about') }}"
                                class="text-white-50 text-decoration-none hover-link">{{ __('About Us') }}</a></li>
                        <li><a href="{{ route('faq') }}"
                                class="text-white-50 text-decoration-none hover-link">{{ __('FAQs') }}</a></li>
                        <li><a href="{{ asset('document/ucsh_user_guide.pdf') }}" target="_blank"
                                class="text-white-50 text-decoration-none hover-link">
                                 {{ __('Read User Guide') }}
                            </a></li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-6">
                    <h5 class="mb-3 fw-bold text-warning">Services</h5>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('user#currentBorrows') }}"
                                class="text-white-50 text-decoration-none hover-link">{{ __('borrow_list') }}</a></li>
                        <li><a href="{{ route('user#bookingHistory') }}"
                                class="text-white-50 text-decoration-none hover-link">{{ __('booking_list') }}</a>
                        </li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-6">
                    <h5 class="mb-3 fw-bold text-warning">Contact Us</h5>
                    <p class="mb-2 text-white-50 small"><i class="bi bi-geo-alt-fill me-2"></i>No.28, Kayin Kyaung
                        Street, TarNgar Se (South)Quarter, Hinthada Township, Ayeyarwaddy Region, Myanmar. Postcode –
                        100601</p>
                    <p class="mb-2 text-white-50 small"><i class="bi bi-telephone-fill me-2"></i>+95 044 22725,
                        09783543901</p>
                    <p class="mb-2 text-white-50 small"><i class="bi bi-envelope-fill me-2"></i>cu.hinthada@gmail.com
                    </p>
                    <a href="{{ route('chat.start') }}" class="mt-2 btn btn-outline-warning btn-sm">
                        <i class="bi bi-chat-dots"></i> Send Message
                    </a>
                </div>
            </div>

            <hr class="mt-4 border-secondary">

            <div class="text-center text-white-50 small">
                &copy; {{ date('Y') }} Digital Catalogue System. All rights reserved.
                <small class="d-block">{{ __('footer_credit') }}</small>
            </div>
            
        </div>
    </footer>

    <style>
        .hover-link:hover {
            color: #ffc107 !important;
            transition: 0.3s;
        }

        .hover-warning:hover {
            color: #ffc107 !important;
            transition: 0.3s;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <a href="#" id="backToTop" class="text-white shadow-lg btn btn-info rounded-circle d-none"
        style="position: fixed; bottom: 30px; right: 30px; z-index: 9999; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; transition: 0.3s;">
        <i class="bi bi-arrow-up fs-4"></i>
    </a>

    <script>
        const backToTopButton = document.getElementById("backToTop");

        window.addEventListener("scroll", () => {
            if (window.scrollY > 300) {
                backToTopButton.classList.remove("d-none");
            } else {
                backToTopButton.classList.add("d-none");
            }
        });

        backToTopButton.addEventListener("click", (e) => {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })
        });
    </script>
</body>

</html>
