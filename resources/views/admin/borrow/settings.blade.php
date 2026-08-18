@extends('admin.layout.master')

@section('content')
<div class="py-4 container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            
            {{-- Success Alert --}}
            @if(session('success'))
                <div class="mb-4 border-0 shadow-sm alert alert-success alert-dismissible fade show rounded-3 d-flex align-items-center" role="alert">
                    <i class="bi bi-check-circle-fill me-3 fs-4 text-success"></i>
                    <div>
                        <span class="fw-bold">{{ __('Success!') }}</span> {{ session('success') }}
                    </div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="{{ __('Close') }}"></button>
                </div>
            @endif

            <div class="bg-white border-0 shadow card rounded-3">
                <div class="py-3 text-white card-header bg-dark fw-bold">
                    <i class="bi bi-sliders me-2"></i> {{ __('Library System Settings') }}
                </div>
                <div class="p-4 card-body">
                    <p class="pb-2 text-muted small border-bottom">{{ __('Configure library rules such as borrowing limits, daily fines, and booking expiration times.') }}</p>
                    
                    <form action="{{ route('admin#saveSettings') }}" method="POST" class="mt-3" novalidate>
                        @csrf
                        
                        {{-- Max Borrow Limit --}}
                        <div class="mb-4">
                            <label class="form-label small text-uppercase fw-bold text-secondary">{{ __('Max Borrow Limit') }}</label>
                            <div class="input-group has-validation">
                                <span class="input-group-text bg-light text-muted"><i class="bi bi-book-half"></i></span>
                                <input type="number" name="max_borrow_limit" class="form-control form-control-lg fs-6 @error('max_borrow_limit') is-invalid @enderror" value="{{ old('max_borrow_limit', \App\Models\SystemSetting::where('key', 'max_borrow_limit')->value('value') ?? 3) }}">
                                <span class="input-group-text bg-light fw-medium">{{ __('Books') }}</span>
                                
                                @error('max_borrow_limit')
                                    <div class="invalid-feedback fw-bold">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-text small text-muted">{{ __('The maximum number of books a student can borrow.') }}</div>
                        </div>

                        {{-- Daily Fine Rate --}}
                        <div class="mb-4">
                            <label class="form-label small text-uppercase fw-bold text-secondary">{{ __('Daily Fine Rate') }}</label>
                            <div class="input-group has-validation">
                                <span class="input-group-text bg-light text-muted"><i class="bi bi-cash-coin"></i></span>
                                <input type="number" name="daily_fine_rate" class="form-control form-control-lg fs-6 @error('daily_fine_rate') is-invalid @enderror" value="{{ old('daily_fine_rate', \App\Models\SystemSetting::where('key', 'daily_fine_rate')->value('value') ?? 100) }}">
                                <span class="input-group-text bg-light fw-medium">{{__('MMK')}}</span>
                                
                                @error('daily_fine_rate')
                                    <div class="invalid-feedback fw-bold">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-text small text-muted">{{ __('Fine amount per day for overdue books.') }}</div>
                        </div>

                        {{-- Booking Expire Hours --}}
                        <div class="mb-4">
                            <label class="form-label small text-uppercase fw-bold text-secondary">{{ __('Booking Expire Hours') }}</label>
                            <div class="input-group has-validation">
                                <span class="input-group-text bg-light text-muted"><i class="bi bi-clock-history"></i></span>
                                <input type="number" name="booking_expire_hours" class="form-control form-control-lg fs-6 @error('booking_expire_hours') is-invalid @enderror" value="{{ old('booking_expire_hours', \App\Models\SystemSetting::where('key', 'booking_expire_hours')->value('value') ?? 24) }}" step="any">
                                <span class="input-group-text bg-light fw-medium">{{ __('Hours') }}</span>
                                
                                @error('booking_expire_hours')
                                    <div class="invalid-feedback fw-bold">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-text small text-muted">{{ __('Time limit for students to collect their booked books.') }}</div>
                        </div>

                        {{-- Borrow Duration Days --}}
                        <div class="mb-4">
                            <label class="form-label small text-uppercase fw-bold text-secondary">{{ __('Borrow Duration Days') }}</label>
                            <div class="input-group has-validation">
                                <span class="input-group-text bg-light text-muted"><i class="bi bi-calendar-check"></i></span>
                                <input type="number" name="borrow_duration_days" class="form-control form-control-lg fs-6 @error('borrow_duration_days') is-invalid @enderror" value="{{ old('borrow_duration_days', \App\Models\SystemSetting::where('key', 'borrow_duration_days')->value('value') ?? 7) }}">
                                <span class="input-group-text bg-light fw-medium">{{ __('Days') }}</span>
                                
                                @error('borrow_duration_days')
                                    <div class="invalid-feedback fw-bold">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-text small text-muted">{{ __('Maximum number of days a book can be borrowed.') }}</div>
                        </div>

                        <div class="mt-4 d-grid">
                            <button type="submit" class="py-2 text-white shadow-sm btn btn-info fw-bold">
                                <i class="bi bi-save2-fill me-1"></i> {{ __('Update System Rules') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection