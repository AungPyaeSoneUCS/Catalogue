<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Catalog') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body {
            background-color: #f4f7f6;
            font-family: 'Instrument Sans', sans-serif;
        }
        .auth-card {
            border: none;
            border-radius: 12px;
            background: #ffffff;
        }
        .btn-info {
            background-color: #0dcaf0;
            border-color: #0dcaf0;
            color: #fff !important;
        }
        .btn-info:hover {
            background-color: #0baccc;
            border-color: #0baccc;
        }
        .btn-outline-info {
            color: #0dcaf0;
            border-color: #0dcaf0;
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">

    <div class="container mt-4">
        <header class="mb-5 d-flex justify-content-between align-items-center">
            <!-- Language Switcher -->
            <div class="shadow-sm btn-group" role="group">
                <a href="{{ route('lang.switch', 'my') }}" 
                   class="btn btn-sm {{ app()->getLocale() == 'my' ? 'btn-info' : 'btn-outline-info' }}">
                   MY
                </a>
                <a href="{{ route('lang.switch', 'en') }}" 
                   class="btn btn-sm {{ app()->getLocale() == 'en' ? 'btn-info' : 'btn-outline-info' }}">
                   EN
                </a>
            </div>

            <!-- Navigation Links -->
            @if (Route::has('login'))
                <nav class="d-flex align-items-center">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="px-4 shadow-sm btn btn-info btn-sm">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-link text-info text-decoration-none fw-bold me-2 small">
                            {{__('Login')}}
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="px-4 btn btn-outline-info btn-sm">
                                {{ __('Register') }}
                            </a>
                        @endif
                    @endauth
                </nav>
            @endif
        </header>

        <main class="row justify-content-center align-items-center flex-grow-1">
            <div class="col-11 col-sm-8 col-md-6 col-lg-4">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>