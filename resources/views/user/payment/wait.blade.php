@extends('auth.layout.master')
@section('content')
    <div class="container">

        <div class="row">

            <div class="col-12 col-lg-8 offset-lg-2">
                
                @if (session('success'))
                    <div class="mb-4 alert alert-success alert-dismissible fade show" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                            <div>{{ session('success') }}</div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
            </div>

            <div class="mb-4 card col-12 col-lg-8 offset-lg-2" style="height:100%">

                <div class="py-3 text-center card-header">
                    <h4 class="mb-0 text-primary font-weight-bold">{{ __('Payment Status') }}</h4>
                </div>
                <div class="p-2 card-body">
                    
                        @if (Auth::check() && Auth::user()->is_approved == 1)
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        <strong>{{ __('Success!') }}</strong> {{ __('Your account has been approved by the Admin.') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @else
                <div class="alert alert-warning d-flex align-items-center" role="alert">
                        <i class="fas fa-exclamation-triangle me-3"></i>
                        <div>
                            <strong>{{ __('Attention') }}:</strong>
                            {{ __('Your payment information is still being reviewed by the Admin.') }}
                            {{ __('Please wait until it is approved.') }}
                        </div>
                        </div>
                @endif
                    
                    <div class="row">
                        <div class="mb-3 col-12 col-lg-6 bg-dark-subtle rounded-3">
                            <div class="mt-3 row">
                                <div class="col-4 text-nowrap"><strong>{{ __('Name') }}</strong></div>
                                <div class="col-8 text-primary fw-bold">{{ Auth::user()->name }}</div>
                            </div>
                            <div class="mt-2 row">
                                <div class="col-4 "><strong>{{ __('Roll Number') }}</strong></div>
                                <div class="col-8 text-primary fw-bold">{{ Auth::user()->roll_number }}</div>
                            </div>
                            <div class="mt-2 row">
                                <div class="col-4 text-nowrap"><strong>{{ __('Email Address') }}</strong></div>
                                <div class="col-8 text-primary fw-bold">{{ Auth::user()->email }}</div>
                            </div>
                            <div class="mt-2 row">
                                <div class="col-4"><strong>{{ __('Academic Year') }}</strong></div>
                                <div class="col-8 text-primary fw-bold">{{ $academic_year->academic ?? 'N/A' }}</div>
                            </div>
                            <div class="mt-2 mb-4 row">
                                <div class="col-4 text-nowrap"><strong>{{ __('Fee') }}</strong></div>
                                <div class="col-8 text-primary fw-bold">{{ $memberData->fee }} {{ __('MMK') }}</div>
                            </div>



                            <div class="mt-2 mb-3 row">
                                <div class="text-center col-12 ">
                                    <img src="{{ asset('userProfile/' . Auth::user()->profile) }}" alt="Payslip"
                                        class="rounded-circle" style="width: 200px;height:200px">
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <img src="{{ asset('payslipImage/' . $memberData->payslip) }}" alt="Payslip"
                                class="w-100 rounded-3" style="max-height: 630px;">

                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>
@endsection
