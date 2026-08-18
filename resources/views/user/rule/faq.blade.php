@extends('user.layout.master')

@section('content')
<div class="container py-5 mt-3">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            {{-- Header --}}
            <div class="mb-5 text-center">
                <h2 class="fw-bold text-primary display-6">{{ __('faq_title') }}</h2>
                <p class="text-muted">{{ __('faq_desc') }}</p>
            </div>

            {{-- Accordion --}}
            <div class="accordion" id="faqAccordion">
                
                {{-- FAQ 1 --}}
                <div class="mb-3 border-0 shadow-sm accordion-item rounded-3">
                    <h2 class="accordion-header">
                        <button class="accordion-button fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#q1">
                            <i class="bi bi-question-circle me-2 text-primary"></i> {{ __('q1') }}
                        </button>
                    </h2>
                    <div id="q1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                        <div class="accordion-body text-muted border-top">{{ __('a1') }}</div>
                    </div>
                </div>

                {{-- FAQ 2 (Dynamic: Limit) --}}
                <div class="mb-3 border-0 shadow-sm accordion-item rounded-3">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#q2">
                            <i class="bi bi-book me-2 text-primary"></i> {{ __('q2') }}
                        </button>
                    </h2>
                    <div id="q2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body text-muted border-top">
                            {{ __('a2', ['limit' => \App\Models\SystemSetting::where('key', 'max_borrow_limit')->value('value') ?? 3]) }}
                        </div>
                    </div>
                </div>

                {{-- FAQ 3 (Dynamic: Fine) --}}
                <div class="mb-3 border-0 shadow-sm accordion-item rounded-3">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#q3">
                            <i class="bi bi-cash-coin me-2 text-primary"></i> {{ __('q3') }}
                        </button>
                    </h2>
                    <div id="q3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body text-muted border-top">
                            {{ __('a3', ['fine' => \App\Models\SystemSetting::where('key', 'daily_fine_rate')->value('value') ?? 100]) }}
                        </div>
                    </div>
                </div>

                {{-- FAQ 4 (Dynamic: Hours) --}}
                <div class="mb-3 border-0 shadow-sm accordion-item rounded-3">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#q4">
                            <i class="bi bi-clock-history me-2 text-primary"></i> {{ __('q4') }}
                        </button>
                    </h2>
                    <div id="q4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body text-muted border-top">
                            {{ __('a4', ['hours' => \App\Models\SystemSetting::where('key', 'booking_expire_hours')->value('value') ?? 24]) }}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<style>
    .accordion-item { overflow: hidden; }
    .accordion-button { padding: 1.2rem; }
    .accordion-button:focus { box-shadow: none; border-color: rgba(0,0,0,.125); }
    .accordion-button:not(.collapsed) {
        background-color: #eef2ff !important;
        color: var(--bs-primary) !important;
    }
    .accordion-body { padding: 1.5rem; line-height: 1.6; }
</style>
@endsection