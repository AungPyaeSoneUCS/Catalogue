@extends('user.layout.master')

@section('content')
<div class="container py-5 mt-3">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="overflow-hidden border-0 shadow-lg card">
                <div class="py-4 bg-opacity-25 border-0 card-header bg-primary">
                    <h4 class="mb-0 text-center text-primary fw-bold">
                        <i class="bi bi-info-circle me-2"></i> {{ __('title_rule') }}
                    </h4>
                </div>
                
                <div class="p-4 card-body p-md-5">
                    <p class="mb-5 text-center text-muted px-md-5">
                        {{ __('description') }}
                    </p>

                    <div class="row g-4">
                        {{-- စာအုပ်ငှားရမ်းခြင်း --}}
                        <div class="col-md-6">
                            <div class="p-3 transition border h-100 rounded-3 hover-shadow d-flex align-items-start">
                                <div class="p-3 bg-primary bg-opacity-10 text-primary rounded-circle me-3">
                                    <i class="bi bi-book-half fs-4"></i>
                                </div>
                                <div>
                                    <strong class="mb-1 text-dark d-block">{{ __('borrowing_title') }}</strong>
                                    <p class="mb-0 text-muted small">
                                        {{ __('borrowing_desc_1') }} <strong>{{ \App\Models\SystemSetting::where('key', 'max_borrow_limit')->value('value') ?? 3 }}</strong> {{ __('borrowing_desc_2') }}
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
                                        {{ __('duration_desc_1') }} <strong>{{ \App\Models\SystemSetting::where('key', 'borrow_duration_days')->value('value') ?? 7 }}</strong> {{ __('duration_desc_2') }}
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
                                        {{ __('fine_desc') }} <strong>{{ \App\Models\SystemSetting::where('key', 'daily_fine_rate')->value('value') ?? 100 }}</strong> {{ __('currency') }}
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
                                        {{ __('booking_desc_1') }} <strong>{{ \App\Models\SystemSetting::where('key', 'booking_expire_hours')->value('value') ?? 24 }}</strong> {{ __('booking_desc_2') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 border-0 alert alert-warning rounded-3 d-flex align-items-center">
                        <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
                        <small class="mb-0">
                            <strong>{{ __('note_title') }}</strong> {{ __('note_desc') }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .transition { transition: all 0.3s ease; }
    .hover-shadow:hover { 
        box-shadow: 0 .5rem 1rem rgba(9, 17, 238, 0.08)!important;
        border-color: rgba(var(--bs-primary-rgb), 0.7) !important;
        transform: translateY(-3px);
    }
    .rounded-4 { border-radius: 1rem !important; }
</style>
@endsection