@extends('user.layout.master')

@section('content')
<div class="container py-5 mt-3">
    <div class="mb-5 row align-items-center g-5">
        <div class="order-2 col-lg-6 order-lg-1">
            <div class="pe-lg-4">
                <h1 class="mb-3 display-5 fw-bold text-primary">{{ __('about_title') }}</h1>
                <p class="mb-3 lead text-dark fw-bold">{{ __('welcome_text') }}</p>
                <p class="text-justify text-secondary fs-6 lh-lg">{{ __('intro_desc') }}</p>
            </div>
        </div>
        <div class="order-1 text-center col-lg-6 order-lg-2">
            <div class="position-relative">
                <img src="{{ asset('image/cover.jpg') }}" alt="UCSH Library" class="border border-white shadow-lg rounded-4 img-fluid border-5">
            </div>
        </div>
    </div>

    <div class="mb-5 row g-4">
        @php
            $features = [
                ['title' => __('vision'), 'desc' => __('vision_desc'), 'icon' => 'bi-eye-fill', 'color' => 'text-primary'],
                ['title' => __('services'), 'desc' => __('service_list'), 'icon' => 'bi-gear-wide-connected', 'color' => 'text-warning'],
                ['title' => __('about_contact'), 'desc' => __('location'), 'icon' => 'bi-geo-alt-fill', 'color' => 'text-success']
            ];
        @endphp

        @foreach($features as $feature)
        <div class="col-md-4">
            <div class="p-4 border-0 shadow-sm card h-100 rounded-4 hover-card">
                <div class="{{ $feature['color'] }} mb-3">
                    <i class="bi {{ $feature['icon'] }} fs-1"></i>
                </div>
                <h4 class="mb-3 fw-bold">{{ $feature['title'] }}</h4>
                <p class="text-justify text-muted">{{ $feature['desc'] }}</p>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-5 text-center">
        <hr class="mx-auto opacity-25 w-50">
        <p class="text-muted small fst-italic">{{ __('footer_credit') }}</p>
    </div>
</div>

<style>
    .hover-card {
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
    }
    .hover-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 1rem 3rem rgba(0,0,0,0.175) !important;
    }
    .text-justify { text-align: justify; }
</style>
@endsection