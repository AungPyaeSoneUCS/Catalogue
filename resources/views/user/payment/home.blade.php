@extends('auth.layout.master')

@section('content')
<div class="container my-5">
    <div class="row g-4 justify-content-center">

        
        <!-- LEFT: PAYMENT INFO -->
        <div class="col-lg-4">
            <div class="overflow-hidden border-0 shadow-sm card rounded-4">

                <!-- Header -->
                <div class="py-4 text-center text-white bg-primary">
                    <h4 class="mb-0 fw-bold">{{ __('Payment Accounts') }}</h4>
                    <small class="opacity-75">{{ __('Send money to the correct account') }}</small>
                </div>

                <!-- BODY -->
                <div class="p-0 card-body" style="max-height: 560px; overflow-y:auto;">

                    @foreach ($payments as $item)
                        <!-- KBZ -->
                    <div class="p-4 border-bottom">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0 fw-bold text-primary">{{$item->account_type}}</h6>
                            </div>
                            {{-- <span class="px-3 py-2 badge bg-primary-subtle text-primary rounded-pill">
                                {{ __('Recommended') }}
                            </span> --}}
                        </div>

                        <div class="mt-3">
                            <div class="d-flex justify-content-between small">
                                <span class="text-muted">{{ __('Name') }}</span>
                                <span class="fw-semibold">{{$item->account_name}}</span>
                            </div>

                            <div class="mt-2 d-flex justify-content-between align-items-center">
                                <span class="text-muted small">{{ __('Number') }}</span>
                                <div class="gap-2 d-flex align-items-center">
                                    <span class="fw-bold" id="kbzNumber">{{$item->account_number}}</span>
                                    <button class="btn btn-sm btn-outline-primary" onclick="copyText('kbzNumber', this)">
                                        {{ __('Copy') }}
                                    </button>
                                </div>
                            </div>

                            <div class="pt-2 mt-3 d-flex justify-content-between small border-top">
                                <span class="text-muted">{{ __('Fees') }}</span>
                                <span class="text-danger fw-bold">{{$item->fee}} {{ __('MMK') }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach

                    
                    
                </div>

                <!-- FOOTER -->
                <div class="p-3 bg-light border-top">
                    <small class="text-muted">
                        ⚠️ {{ __('Double-check account before sending money') }}
                    </small>
                </div>

            </div>
        </div>

        <!-- RIGHT: UPLOAD -->
        <div class="col-lg-6">
            <div class="overflow-hidden border-0 shadow-sm card rounded-4">

                <!-- Header -->
                <div class="py-4 text-center text-white bg-dark">
                    <h5 class="mb-0 fw-bold">{{ __('Payment Upload') }}</h5>
                    <small class="opacity-75">{{ __('Select method and upload receipt') }}</small>
                </div>

                <!-- BODY -->
                <div class="card-body d-flex align-items-center">
                    {{-- <form class="w-100" action="{{ route('user#paymentCreate') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="form-label small text-muted">{{ __('Payment Method') }}</label>
                                    <select class="form-select form-select-lg" name="payment_method" >
                                        <option selected disabled>{{ __('Choose method') }}</option>
                                        @foreach ($payments as $item)
                                            <option value="{{ $item->id }}">{{ $item->account_name }}/{{ $item->account_number }}/{{ $item->account_type }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="form-label small text-muted">{{ __('Payment Amount') }}</label>
                                    <input type="number"  class="form-control form-control-lg" name="payment_amount" value="{{ $item->fee }}" readonly>
                                </div>
                            </div>
                        </div>

                        <!-- UPLOAD -->
                        <div class="mb-4">
                            <label class="form-label small text-muted">{{ __('Upload Receipt') }}</label>
                            <div class="p-4 text-center border rounded-3 bg-light">
                                <i class="bi bi-cloud-arrow-up fs-2 text-secondary"></i>
                                <img src="" id="output" alt="">
                                <p class="mt-2 mb-2 small text-muted">
                                    {{ __('Upload payment screenshot here') }}
                                </p>
                                <input type="file" name="receipt" onchange="loadImage(event)" class="form-control" accept="image/*">
                            </div>
                        </div>

                        
                            
                        
                        <!-- BUTTON -->
                        <div class="d-grid">
                            <button type="submit" class="btn btn-success btn-lg">
                                {{ __('Confirm Payment') }}
                            </button>
                        </div>
                    </form> --}}
                    <form class="w-100" action="{{ route('user#paymentCreate') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row">
        <!-- Payment Method -->
        <div class="col-md-6">
            <div class="mb-4">
                <label class="form-label small text-muted">{{ __('Payment Method') }}</label>
                <select class="form-select form-select-lg @error('payment_method') is-invalid @enderror" name="payment_method">
                    <option selected disabled>{{ __('Choose method') }}</option>
                    @foreach ($payments as $item)
                        <option value="{{ $item->id }}" {{ old('payment_method') == $item->id ? 'selected' : '' }}>
                            {{ $item->account_name }}/{{ $item->account_number }}/{{ $item->account_type }}
                        </option>
                    @endforeach
                </select>
                @error('payment_method')
                    <div class="invalid-feedback fw-bold">
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>

        <!-- Payment Amount -->
        <div class="col-md-6">
            <div class="mb-4">
                <label class="form-label small text-muted">{{ __('Payment Amount') }}</label>
                                    <input type="number"  class="form-control form-control-lg" name="payment_amount" value="{{ $item->fee }}" readonly>                
            </div>
        </div>
    </div>

    <!-- UPLOAD -->
    <div class="mb-4">
        <label class="form-label small text-muted">{{ __('Upload Receipt') }}</label>
        <div class="p-4 text-center border rounded-3 bg-light">
            <i class="bi bi-cloud-arrow-up fs-2 text-secondary"></i>
            <img src="" id="output" alt="">
            <p class="mt-2 mb-2 small text-muted">
                {{ __('Upload payment screenshot here') }}
            </p>
            <input type="file" name="receipt" onchange="loadImage(event)" class="form-control @error('receipt') is-invalid @enderror" accept="image/*">
            
            @error('receipt')
                <div class="mt-2 invalid-feedback fw-bold text-start">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>

    <!-- BUTTON -->
    <div class="d-grid">
        <button type="submit" class="btn btn-success btn-lg">
            {{ __('Confirm Payment') }}
        </button>
    </div>
</form>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- COPY SCRIPT -->
<script>
function copyText(id, btn) {
    const text = document.getElementById(id).innerText;
    navigator.clipboard.writeText(text).then(() => {
        const originalText = btn.innerText;
        btn.innerText = '✓'; // Change icon/text to show success
        btn.classList.replace('btn-outline-primary', 'btn-primary');
        
        setTimeout(() => { 
            btn.innerText = originalText; 
            btn.classList.replace('btn-primary', 'btn-outline-primary');
        }, 1500);
    }).catch(err => {
        console.error('Failed to copy: ', err);
    });
}
</script>
@endsection