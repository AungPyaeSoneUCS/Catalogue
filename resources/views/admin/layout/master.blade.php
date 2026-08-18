<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Digital Catalogue System</title>
    <link rel="icon" type="image/png" href="{{ asset('image/catalog-logo.jpg') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@7.0.0/css/flag-icons.min.css" />
@vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body class="bg-light">

    {{-- Top Navbar --}}
    <nav class="shadow navbar navbar-expand-lg navbar-info sticky-top" style="height: 56px; background-color: #1A237E;">
        <div class="container-fluid">
            {{-- <a class="text-white navbar-brand fw-bold px-lg-5" href="#">
                <img src="{{ asset('image/catalog-logo.jpg') }}" alt="Logo" width="35" height="35"
                    class="rounded-circle">
                <span>UCSH Library Catalog</span>
            </a> --}}
            <a class="text-white navbar-brand fw-bold px-lg-3 d-flex align-items-center" href="{{ route('admin#home') }}" style="white-space: nowrap; font-size: clamp(0.85rem, 1.8vw, 1.15rem);">
                <img src="{{ asset('image/catalog-logo.jpg') }}" alt="Logo" width="50" height="50"
                    class="text-white rounded-circle me-2">
                <span class="d-none d-sm-inline">Digital Catalogue System</span>
                <span class="d-inline d-sm-none fs-6">Digital Catalogue System</span>
            </a>
            <!-- Toggle Button -->
            <button class="navbar-toggler text-warning" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu">
                {{-- <span class="navbar-toggler-icon text-warning"></span> --}}
                <span class="navbar-toggler-icon" 
                  style="background-image: url(&quot;data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(255, 193, 7, 1)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e&quot;);">
            </span>
            </button>

            <!-- OFFCANVAS MENU -->
            <div class="offcanvas offcanvas-end" tabindex="-1" id="mobileMenu">
                <div class="text-white offcanvas-header bg-info">
                    <h5 class="offcanvas-title">Menu</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
                </div>

                <div class="p-0 offcanvas-body">

                    {{-- 📱 Mobile Collapse Navbar Menu --}}
                    <ul class="p-2 mt-3 rounded bg-secondary navbar-nav d-lg-none text-nowrap">
                        <li class="nav-item"><a
                                class="nav-link {{ request()->routeIs('admin#home') ? 'active fw-bold text-warning' : 'text-white' }}"
                                href="{{ route('admin#home') }}"><i class="bi bi-grid-fill me-2"></i>
                                {{ __('dashboard') }}</a></li>
                        <li class="nav-item"><a
                                class="nav-link {{ request()->routeIs('list#paymentDetails*') ? 'active fw-bold text-warning' : 'text-white' }}"
                                href="{{ route('list#paymentDetails') }}"><i class="bi bi-credit-card me-2"></i>
                                {{ __('payment') }}</a></li>
                        <li class="nav-item"><a
                                class="nav-link {{ request()->routeIs('list#categoryDetails*') ? 'active fw-bold text-warning' : 'text-white' }}"
                                href="{{ route('list#categoryDetails') }}"><i class="bi bi-tags me-2"></i>
                                {{ __('category') }}</a></li>
                        <li class="nav-item"><a
                                class="nav-link {{ request()->routeIs('list#yearDetails*') ? 'active fw-bold text-warning' : 'text-white' }}"
                                href="{{ route('list#yearDetails') }}"><i class="bi bi-calendar-event me-2"></i>
                                {{ __('year') }}</a></li>
                        <li class="nav-item"><a
                                class="nav-link {{ request()->routeIs('list#bookDetails*') ? 'active fw-bold text-warning' : 'text-white' }}"
                                href="{{ route('list#bookDetails') }}"><i class="bi bi-journal-bookmark me-2"></i>
                                {{ __('book') }}</a></li>
                        <li class="nav-item"><a
                                class="nav-link {{ request()->routeIs('admin#bookingList') ? 'active fw-bold text-warning' : 'text-white' }}"
                                href="{{ route('admin#bookingList') }}"><i class="bi bi-journal-text me-2"></i>
                                {{ __('booking_list') }}</a></li>
                        <li class="nav-item"><a
                                class="nav-link {{ request()->routeIs('admin#borrowList') ? 'active fw-bold text-warning' : 'text-white' }}"
                                href="{{ route('admin#borrowList') }}"><i class="bi bi-arrow-repeat me-2"></i>
                                {{ __('borrow_list') }}</a></li>
                        <li class="nav-item"><a
                                class="nav-link {{ request()->routeIs('admin#overdueList') ? 'active text-warning fw-bold' : 'text-white' }}"
                                href="{{ route('admin#overdueList') }}"><i class="bi bi-clock-history me-2"></i>
                                {{ __('overdue_list') }}</a></li>
                        <li class="nav-item"><a
                                class="nav-link {{ request()->routeIs('admin.returnedFines') ? 'active text-warning fw-bold' : 'text-white' }}"
                                href="{{ route('admin.returnedFines') }}"><i class="bi bi-cash-stack me-2"></i>
                                {{ __('Collected Fines') }}</a></li>
                        <li class="nav-item"><a
                                class="nav-link {{ request()->routeIs('admin#returnedList') ? 'active text-warning text-warning' : 'text-white' }}"
                                href="{{ route('admin#returnedList') }}"><i class="bi bi-check2-square me-2"></i>
                                {{ __('Returned Books') }}</a></li>
                        <li class="nav-item"><a
                                class="nav-link {{ request()->routeIs('admin#lostBooksList') ? 'active text-warning text-warning' : 'text-white' }}"
                                href="{{ route('admin#lostBooksList') }}"><i class="bi bi-book-half me-2"></i>
                                {{ __('Lost Books & Penalties') }}</a></li>
                        <li class="nav-item"><a
                                class="nav-link {{ request()->routeIs('admin#damageBooksList') ? 'active text-warning text-warning' : 'text-white' }}"
                                href="{{ route('admin#damageBooksList') }}"><i class="bi bi-file-earmark-x me-2"></i>
                                {{ __('Damage Books & Penalties') }}</a></li>
                        <li class="nav-item"><a
                                class="nav-link {{ request()->routeIs('request#userDetails*') ? 'active fw-bold text-warning' : 'text-white' }}"
                                href="{{ route('request#userDetails') }}"><i class="bi bi-person-plus me-2"></i>
                                {{ __('request_user') }}</a></li>
                        <li class="nav-item"><a
                                class="nav-link {{ request()->routeIs('list#userDetails*') ? 'active fw-bold text-warning' : 'text-white' }}"
                                href="{{ route('list#userDetails') }}"><i class="bi bi-people me-2"></i>
                                {{ __('user_list') }}</a></li>
                        <li class="nav-item"><a
                                class="nav-link {{ request()->routeIs('list#memberFees*') ? 'active fw-bold text-warning' : 'text-white' }}"
                                href="{{ route('list#memberFees') }}"><i class="bi bi-cash-stack"></i>
                                {{ __('Member Fees') }}</a></li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.contact*') ? 'active fw-bold text-warning' : 'text-white' }}"
                                href="{{ route('admin.contact.list') }}">
                                <span>
                                    <i class="bi bi-chat-dots-fill me-2"></i> {{ __('Contact List') }}
                                </span>

                                @if (isset($unreadCount) && $unreadCount > 0)
                                    <span class="badge rounded-pill bg-danger">
                                        {{ $unreadCount }}
                                    </span>
                                @endif
                            </a>
                        </li>
                        {{-- @if (Auth::user()->role == 'superadmin')
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.create') ? 'active fw-bold text-warning' : 'text-white' }}"
                                        href="{{ route('admin.create') }}">
                                        <i class="bi bi-person-plus me-2"></i> {{ __('Librarian List') }}
                                    </a>
                                </li>
                         @endif --}}
                    </ul>

                    {{-- Language Switcher & Profile --}}
                    <div
                        class="gap-3 pb-3 d-flex flex-column flex-lg-row align-items-start align-items-lg-center ms-auto pb-lg-0" style="background-color: #1A237E">
                        <div class="dropdown ms-lg-3 ms-3">
                            <a class="text-white nav-link dropdown-toggle fw-bold" href="#" id="langDropdown"
                                role="button" data-bs-toggle="dropdown">
                                @if (app()->getLocale() == 'mm')
                                    <span class="fi fi-mm me-1"></span> MM
                                @else
                                    <span class="fi fi-gb me-1"></span> EN 
                                @endif  
                                {{-- style="width: 20px; height: 15px; display: inline-block;" --}}
                            </a>
                            <ul class="border-0 shadow-sm dropdown-menu dropdown-menu-start">
                                <li><a class="dropdown-item {{ app()->getLocale() == 'en' ? 'active bg-info text-white' : '' }}"
                                        href="{{ route('lang.switch', 'en') }}"><span class="fi fi-gb me-2"></span>
                                        English</a></li>
                                <li><a class="dropdown-item {{ app()->getLocale() == 'mm' ? 'active bg-info text-white' : '' }}"
                                        href="{{ route('lang.switch', 'mm') }}"><span class="fi fi-mm me-2"></span>
                                        Myanmar</a></li>
                            </ul>
                        </div>
                        <h6 class="mb-0 text-white fw-bold text-nowrap ms-3">{{ auth()->user()->name ?? 'Account' }}
                        </h6>
                        <div class="dropdown w-100 w-lg-auto">

                            <button
                                class="gap-2 ms-3 btn btn-outline-secondary btn-sm dropdown-toggle d-flex align-items-center"
                                type="button" data-bs-toggle="dropdown">

                                @if (auth()->check() && auth()->user()->profile)
                                    {{-- 🎯 User မှာ ပုံရှိလျှင် (ဝိုင်းဝိုင်းလေးနဲ့ Size ကွက်တိဖြစ်အောင် ပြင်ထားပါတယ်) --}}
                                    <img src="{{ asset('userProfile/' . auth()->user()->profile) }}" alt="Profile"
                                        class="rounded-circle" style="width: 28px; height: 28px; object-fit: cover;">
                                @else
                                    {{-- 🎯 User မှာ ပုံမရှိလျှင် UI-Avatars ကို သပ်သပ်ရပ်ရပ်လေး ပြသမည် --}}
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'User') }}&background=0dcaf0&color=fff&size=128"
                                        alt="Profile" class="rounded-circle"
                                        style="width: 28px; height: 28px; object-fit: cover;">
                                @endif



                            </button>
                            <ul
                                class="mt-2 border-opacity-25 shadow dropdown-menu dropdown-menu-start dropdown-menu-lg-end border-info">
                                <li class="px-3 py-2 mb-2 small fw-bold text-muted border-bottom">
                                    {{ __('my_account') }}</li>
                                <li><a class="dropdown-item" href="{{ route('admin.profile.edit') }}"><i
                                            class="bi bi-person me-2"></i> {{ __('view_profile') }}</a></li>
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('admin#settingPage') ? 'active bg-info text-white' : '' }}"
                                        href="{{ route('admin#settingPage') }}">
                                        <i class="bi bi-gear me-2"></i> {{ __('account_settings') }}
                                    </a>
                                </li>
                                @if (Auth::user()->role == 'superadmin')
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('admin.create') ? 'active bg-info text-white' : '' }}"
                                        href="{{ route('admin.create') }}">
                                        <i class="bi bi-person-plus me-2"></i> {{ __('Librarian List') }}
                                    </a>
                                </li>
                                @endif
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger fw-bold">
                                            <i class="bi bi-box-arrow-right me-2"></i> {{ __('logout') }}
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            </div>
    </nav>

    <div class="container-fluid">
        <div class="row">

            {{-- 💻 Desktop Sidebar Navigation --}}
            <nav class="pt-4 shadow-sm col-lg-2 d-none d-lg-block border-end sticky-top"
                style="top: 56px; height: calc(100vh - 56px); overflow-y: auto; z-index: 1020; background-color: rgba(26, 35, 126, 0.6);">
                <div class="px-3">

                    <p class="mb-3 text-muted small fw-bold text-uppercase">{{ __('management') }}</p>
                    <ul class="gap-1 nav flex-column text-nowrap">
                        <li class="nav-item">
                            <a class="py-2 nav-link {{ request()->routeIs('admin#home') ? 'bg-info bg-opacity-10 text-warning fw-bold' : 'text-white' }} rounded-2"
                                href="{{ route('admin#home') }}">
                                <i class="bi bi-grid-fill me-2"></i> {{ __('dashboard') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="py-2 nav-link {{ request()->routeIs('list#paymentDetails*') ? 'bg-info bg-opacity-10 text-warning fw-bold' : 'text-white' }} rounded-2"
                                href="{{ route('list#paymentDetails') }}">
                                <i class="bi bi-credit-card me-2"></i> {{ __('payment') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="py-2 nav-link {{ request()->routeIs('list#categoryDetails*') ? 'bg-info bg-opacity-10 text-warning fw-bold' : 'text-white' }} rounded-2"
                                href="{{ route('list#categoryDetails') }}">
                                <i class="bi bi-tags me-2"></i> {{ __('category') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="py-2 nav-link {{ request()->routeIs('list#yearDetails*') ? 'bg-info bg-opacity-10 text-warning fw-bold' : 'text-white' }} rounded-2"
                                href="{{ route('list#yearDetails') }}">
                                <i class="bi bi-calendar-event me-2"></i> {{ __('year') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="py-2 nav-link {{ request()->routeIs(['list#bookDetails', 'books*']) ? 'bg-info bg-opacity-10 text-warning fw-bold' : 'text-white' }} rounded-2"
                                href="{{ route('list#bookDetails') }}">
                                <i class="bi bi-journal-bookmark me-2"></i> {{ __('book') }}
                            </a>
                        </li>
                    </ul>

                    <p class="mt-4 mb-3 text-muted small fw-bold text-uppercase">{{ __('records') }}</p>
                    <ul class="gap-1 nav flex-column text-nowrap">
                        <li class="nav-item">
                            <a class="py-2 nav-link {{ request()->routeIs('admin#bookingList') ? 'bg-info bg-opacity-10 text-warning fw-bold' : 'text-white' }} rounded-2"
                                href="{{ route('admin#bookingList') }}">
                                <i class="bi bi-journal-text me-2"></i> {{ __('booking_list') }}
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="py-2 nav-link {{ request()->routeIs('admin#borrowList') ? 'bg-info bg-opacity-10 text-warning fw-bold' : 'text-white' }} rounded-2"
                                href="{{ route('admin#borrowList') }}">
                                <i class="bi bi-arrow-repeat me-2"></i> {{ __('borrow_list') }}
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="py-2 nav-link {{ request()->routeIs('admin#overdueList') ? 'bg-danger bg-opacity-10 text-warning fw-bold' : 'text-white' }} rounded-2"
                                href="{{ route('admin#overdueList') }}">
                                <i class="bi bi-clock-history me-2"></i> {{ __('overdue_list') }}
                            </a>
                        </li>
                        <li class="nav-item text-nowrap">
                            <a class="py-2 nav-link {{ request()->routeIs('admin.returnedFines') ? 'bg-danger bg-opacity-10 text-warning fw-bold' : 'text-white' }} rounded-2"
                                href="{{ route('admin.returnedFines') }}">
                                <i class="bi bi-cash-stack me-2"></i> {{ __('Collected Fines') }}
                            </a>
                        </li>
                        <li class="nav-item text-nowrap">
                            <a class="py-2 nav-link {{ request()->routeIs('admin#returnedList') ? 'bg-success bg-opacity-10 text-warning fw-bold' : 'text-white' }} rounded-2"
                                href="{{ route('admin#returnedList') }}">
                                <i class="bi bi-check2-square me-2"></i> {{ __('Returned Books') }}
                            </a>
                        </li>
                        <li class="nav-item text-nowrap">
                            <a class="py-2 nav-link {{ request()->routeIs('admin#damageBooksList') ? 'bg-success bg-opacity-10 text-warning fw-bold' : 'text-white' }} rounded-2"
                                href="{{ route('admin#damageBooksList') }}">
                                <i class="bi bi-file-earmark-x me-2"></i> {{ __('Damage Books & Penalties') }}
                            </a>
                        </li>
                        <li class="nav-item text-nowrap">
                            <a class="py-2 nav-link {{ request()->routeIs('admin#lostBooksList') ? 'bg-success bg-opacity-10 text-warning fw-bold' : 'text-white' }} rounded-2"
                                href="{{ route('admin#lostBooksList') }}">
                                <i class="bi bi-book-half me-2"></i> {{ __('Lost Books & Penalties') }}
                            </a>
                        </li>
                    </ul>

                    <p class="mt-4 mb-3 text-muted small fw-bold text-uppercase">{{ __('Total Users') }}</p>
                    <ul class="gap-1 nav flex-column text-nowrap">
                        <li class="nav-item ">
                            <a class="py-2 nav-link {{ request()->routeIs('request#userDetails*') ? 'bg-info bg-opacity-10 text-warning fw-bold' : 'text-white' }} rounded-2"
                                href="{{ route('request#userDetails') }}">
                                <i class="bi bi-person-plus me-2"></i> {{ __('request_user') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="py-2 nav-link {{ request()->routeIs('list#userDetails*') ? 'bg-info bg-opacity-10 text-warning fw-bold' : 'text-white' }} rounded-2"
                                href="{{ route('list#userDetails') }}">
                                <i class="bi bi-people me-2"></i> {{ __('user_list') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="py-2 nav-link {{ request()->routeIs('list#memberFees*') ? 'bg-info bg-opacity-10 text-warning fw-bold' : 'text-white' }} rounded-2"
                                href="{{ route('list#memberFees') }}">
                                <i class="bi bi-cash-stack me-2"></i> {{ __('Member Fees') }}
                            </a>
                        </li>
                        <li class="nav-item position-relative">
                            <a class="py-2 nav-link d-flex justify-content-between align-items-center {{ request()->routeIs('admin.contact.list') ? 'bg-info bg-opacity-10 text-warning fw-bold' : 'text-white' }}"
                                href="{{ route('admin.contact.list') }}">

                                <span>
                                    <i class="bi bi-chat-dots-fill me-2"></i> {{ __('Contact List') }}
                                </span>

                                @if (isset($unreadCount) && $unreadCount > 0)
                                    <span class="badge rounded-pill bg-danger">
                                        {{ $unreadCount }}
                                    </span>
                                @endif
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <main class="pt-4 col-12 col-lg-10 ms-auto px-md-4">
                @yield('content')
            </main>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <a href="#" id="backToTop" class="text-white shadow-lg btn btn-info rounded-circle d-none"
        style="position: fixed; bottom: 30px; right: 30px; z-index: 9999; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; transition: 0.3s;">
        <i class="bi bi-arrow-up fs-4"></i>
    </a>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const backToTopButton = document.getElementById("backToTop");
            const tableContainer = document.querySelector('.table-responsive');

            if (!backToTopButton) return;

            function checkScroll() {
                // Window ရော Table Container ရဲ့ scroll ကို စစ်မယ်
                const windowScroll = window.scrollY;
                const tableScroll = tableContainer ? tableContainer.scrollTop : 0;

                if (windowScroll > 300 || tableScroll > 300) {
                    backToTopButton.classList.remove("d-none");
                } else {
                    backToTopButton.classList.add("d-none");
                }
            }

            window.addEventListener('scroll', checkScroll);
            if (tableContainer) {
                tableContainer.addEventListener('scroll', checkScroll);
            }

            backToTopButton.addEventListener("click", (e) => {
                e.preventDefault();
                // Window ကို ပြန်သွား
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
                // Table container ကိုလည်း ပြန်သွား
                if (tableContainer) {
                    tableContainer.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                }
            });
        });
    </script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>
</body>
@stack('scripts')
@yield('script-code')

</html>
