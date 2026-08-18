@extends('admin.layout.master') {{-- သင့် User Layout Master အား ခေါ်ယူခြင်း --}}

@section('content')
    {{-- 🎯 တစ်မျက်နှာလုံး သန့်ရှင်းသပ်ရပ်စေရန် ပေါ့ပါးသော Gray Background နှင့် ကောင်းမွန်သော Padding သုံးထားပါသည် --}}
    <div class="py-5 container-fluid bg-secondary-subtle" style="min-height: 100vh;">
        <div class="container">

            {{-- ၁။ Header Section (အပေါ်ဘား၊ ခေါင်းစဉ်နှင့် အခြေအနေပြ Badge) --}}
            <div class="flex-wrap gap-3 mb-4 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <a href="{{ route('list#bookDetails') }}"
                        class="px-3 shadow-sm btn btn-outline-secondary btn-sm rounded-pill me-3">
                        <i class="fas fa-arrow-left me-1"></i> {{ __('back_to_list') }}
                    </a>
                    <div>
                        <h4 class="mb-0 fw-bold text-dark" style="letter-spacing: -0.5px;">{{ __('book_details') }}</h4>
                    </div>
                </div>
            </div>

            {{-- ၂။ Main Details Content Row --}}
            <div class="row g-4">

                {{-- ဘယ်ဘက်ခြမ်း: စာအုပ်မျက်နှာဖုံးပုံ (Book Cover Card) --}}
                <div class="col-xl-5 col-lg-5 col-md-5 col-12">
                    <div class="p-3 overflow-hidden border-0 shadow-sm bg-secondary-subtle card rounded-4 h-100 d-flex flex-column justify-content-center align-items-center">
                        <div class="text-center w-100">
                            @if ($book->cover_image)
                                <img src="{{ asset('storage/books/' . $book->cover_image) }}"
                                    class="bg-white shadow-sm img-fluid w-100 rounded-3"
                                    style="height: 500px; object-fit: contain;">
                            @else
                                <div class="shadow-sm bg-light d-flex flex-column align-items-center justify-content-center text-muted w-100 rounded-3"
                                    style="height: 500px;">
                                    <i class="mb-3 opacity-25 fas fa-book text-secondary display-1"></i>
                                    <span class="tracking-wider small fw-bold text-uppercase text-muted">{{ __('no_cover_image') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- ညာဘက်ခြမ်း: Specifications Box --}}
                <div class="col-xl-7 col-lg-7 col-md-7 col-12">
                    <div class="p-4 border-0 shadow-sm bg-secondary-subtle card rounded-4 h-100 d-flex flex-column justify-content-between">
                        <div>
                            <h5 class="pb-2 mb-4 fw-bold text-dark border-bottom">
                                <i class="fas fa-info-circle text-primary me-2"></i>{{ __('specifications') }}
                            </h5>

                            <div class="row g-3">
                                <div class="col-md-6 col-12">
                                    <div class="p-3 border-0 bg-light rounded-3">
                                        <label class="mb-1 text-muted small fw-bold d-block text-uppercase" style="letter-spacing: 0.5px;">{{ __('book_title') }}</label>
                                        <span class="text-dark fw-bold">{{ $book->title }}</span>
                                    </div>
                                </div>

                                <div class="col-md-6 col-12">
                                    <div class="p-3 border-0 bg-light rounded-3">
                                        <label class="mb-1 text-muted small fw-bold d-block text-uppercase" style="letter-spacing: 0.5px;">{{ __('author_name') }}</label>
                                        <span class="text-dark fw-medium">{{ $book->author ?? __('unknown_author') }}</span>
                                    </div>
                                </div>

                                <div class="col-md-6 col-12">
                                    <div class="p-3 border-0 bg-light rounded-3">
                                        <label class="mb-1 text-muted small fw-bold d-block text-uppercase" style="letter-spacing: 0.5px;">{{ __('book_code') }}</label>
                                        <span class="px-3 py-2 rounded badge bg-secondary-subtle text-secondary-emphasis fw-bold font-monospace fs-6">{{ $book->code ?? '-' }}</span>
                                    </div>
                                </div>

                                <div class="col-md-6 col-12">
                                    <div class="p-3 border-0 bg-light rounded-3">
                                        <label class="mb-1 text-muted small fw-bold d-block text-uppercase" style="letter-spacing: 0.5px;">{{ __('press_publisher') }}</label>
                                        <span class="text-dark fw-medium">{{ $book->press ?? '-' }}</span>
                                    </div>
                                </div>

                                <div class="col-md-6 col-12">
                                    <div class="p-3 border-0 bg-light rounded-3">
                                        <label class="mb-1 text-muted small fw-bold d-block text-uppercase" style="letter-spacing: 0.5px;">{{ __('Subject Details') }}</label>
                                        <span class="px-3 py-2 rounded badge bg-primary-subtle text-primary fw-bold fs-6">
                                            {{ $book->category ? $book->category->name : __('general') }}
                                        </span>
                                    </div>
                                </div>

                                <div class="col-md-6 col-12">
                                    <div class="p-3 border-4 bg-light rounded-3 border-start border-primary">
                                        <label class="mb-1 text-primary small fw-bold d-block text-uppercase" style="letter-spacing: 0.5px;">
                                            <i class="fas fa-layer-group me-1"></i> {{ __('available_quantity') }}
                                        </label>
                                        <span class="fs-2 fw-bold text-dark">{{ $book->available_qty ?? $book->total_qty }}</span>
                                        <small class="text-muted fw-semibold ms-1">{{ __('copies_available') }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- ၃။ Abstract နှင့် Table of Contents Row --}}
            <div class="mt-2 row g-4">
                <div class="col-lg-6 col-12">
                    <div class="p-4 shadow-sm border-3 bg-secondary-subtle card rounded-4 h-100 border-top border-dark-subtle">
                        <h5 class="p-2 fw-bold  border-bottom border-3 ">
                            <i class="fas fa-align-left text-warning me-2"></i>{{ __('book_abstract_title') }}
                        </h5>
                        <div class="text-secondary fw-bold lh-lg" style="white-space: pre-line; font-size: 0.95rem;">
                            {{ $book->abstract ?? __('no_abstract') }}
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 col-12">
                    <div class="p-4 shadow-sm border-3 bg-secondary-subtle card rounded-4 h-100 border-top border-dark-subtle">
                        <h5 class="p-2 fw-bold  border-bottom border-3 ">
                            <i class="fas fa-list-ol text-danger me-2"></i>{{ __('table_of_contents_title') }}
                        </h5>
                        <div class="text-secondary fw-bold lh-lg" style="white-space: pre-line; font-size: 0.95rem;">
                            {{ $book->content ?? __('no_contents') }}
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection