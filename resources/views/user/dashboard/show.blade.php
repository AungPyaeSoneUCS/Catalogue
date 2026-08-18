@extends('user.layout.master')

@section('content')
<div class="py-3 mt-5 py-md-5 container-fluid bg-secondary-subtle min-vh-100">
    <div class="container px-2 px-md-3">

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="mb-4 border-0 shadow-sm alert alert-success alert-dismissible fade show rounded-3" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                    <div><strong>{{ __('success_msg') }}</strong> {{ session('success') }}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 border-0 shadow-sm alert alert-danger alert-dismissible fade show rounded-3" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                    <div><strong>{{ __('warning_msg') }}</strong> {{ session('error') }}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Header Section --}}
        <div class="gap-3 mb-4 d-flex flex-column flex-md-row align-items-md-center justify-content-between text-nowrap">
            <div class="d-flex align-items-center">
                <a href="{{ route('user#home') }}" class="px-3 shadow-sm btn btn-outline-secondary btn-sm rounded-pill me-3">
                    <i class="fas fa-arrow-left me-1"></i> {{ __('back_to_list') }}
                </a>
                <h4 class="mb-0 fw-bold text-dark">{{ __('book_details') }}</h4>
            </div>
            <div>
                @if ($book->available_qty > 0)
                    <span class="px-3 py-2 border badge bg-success-subtle text-success rounded-pill fw-bold border-success-subtle">
                        <i class="fas fa-check-circle me-1"></i> {{ __('Available') }}
                    </span>
                @else
                    <span class="px-3 py-2 border badge bg-danger-subtle text-danger rounded-pill fw-bold border-danger-subtle">
                        <i class="fas fa-times-circle me-1"></i> {{ __('Out of Stock') }}
                    </span>
                @endif
            </div>
        </div>

        {{-- Book Info Section --}}
        <div class="row g-4">
            {{-- Cover Image --}}
            <div class="col-12 col-md-5 col-xl-4">
                <div class="overflow-hidden bg-white border-0 shadow-sm card rounded-4 h-100">
                    <div class="w-100 position-relative" style="aspect-ratio: 2/3;">
                        @if ($book->cover_image)
                            <img src="{{ asset('storage/books/' . $book->cover_image) }}" 
                                 class="shadow-sm img-fluid w-100 h-100" style="object-fit: cover;" alt="{{ $book->title }}">
                        @else
                            <div class="bg-light d-flex flex-column align-items-center justify-content-center text-muted w-100 h-100">
                                <i class="mb-3 opacity-25 fas fa-book display-4"></i>
                                <span class="small fw-bold text-uppercase">{{ __('no_cover_image') }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Specifications --}}
            <div class="col-12 col-md-7 col-xl-8">
                <div class="p-4 bg-white border-0 shadow-sm card rounded-4 h-100">
                    <h5 class="pb-2 mb-4 fw-bold text-dark border-bottom">
                        <i class="fas fa-info-circle text-primary me-2"></i>{{ __('specifications') }}
                    </h5>
                    
                    <div class="row g-3">
                        @php
                            $specs = [
                                ['label' => __('book_title'), 'value' => $book->title],
                                ['label' => __('author_name'), 'value' => $book->author ?? __('unknown_author')],
                                ['label' => __('book_code'), 'value' => $book->code ?? '-'],
                                ['label' => __('press_publisher'), 'value' => $book->press ?? '-'],
                                ['label' => __('Subject Details'), 'value' => $book->category ? $book->category->name : __('general')]
                            ];
                        @endphp

                        @foreach($specs as $spec)
                        <div class="col-md-6">
                            <div class="p-3 border-0 bg-light rounded-3">
                                <label class="mb-1 fw-bold d-block text-uppercase text-muted" style="font-size: 0.75rem;">{{ $spec['label'] }}</label>
                                <span class="text-dark fw-bold">{{ $spec['value'] }}</span>
                            </div>
                        </div>
                        @endforeach

                        <div class="col-12">
                            <div class="p-3 border-4 border-start border-primary bg-primary-subtle rounded-3">
                                <label class="mb-1 text-primary fw-bold d-block text-uppercase" style="font-size: 0.75rem;"><i class="fas fa-layer-group me-1"></i> {{ __('available_quantity') }}</label>
                                <span class="fs-2 fw-bold text-dark">{{ $book->available_qty ?? 0 }}</span>
                                <small class="text-muted fw-semibold ms-1">{{ __('copies_available') }}</small>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 mt-auto border-top text-end">
                        @if ($book->available_qty > 0)
                            <form action="{{ route('user#requestBooking', $book->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-4 py-2 shadow-sm btn btn-primary fw-bold rounded-pill">
                                    <i class="bi bi-bookmark-fill me-1"></i> {{ __('book_now') }}
                                </button>
                            </form>
                        @else
                            <button class="px-4 py-2 btn btn-outline-secondary rounded-pill fw-bold" disabled>
                                <i class="fas fa-exclamation-triangle me-2"></i> {{ __('temporarily_unavailable') }}
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Abstract & Contents --}}
        <div class="mt-4 row g-4">
            <div class="col-lg-6">
                <div class="p-4 bg-white border shadow-sm card rounded-4">
                    <h5 class="pb-2 mb-3 fw-bold text-dark border-bottom"><i class="fas fa-align-left text-warning me-2"></i>{{ __('book_abstract_title') }}</h5>
                    <div style="line-height: 1.7; text-align: justify; white-space: pre-line;">{{ $book->abstract ?? __('no_abstract') }}</div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="p-4 bg-white border shadow-sm card rounded-4">
                    <h5 class="pb-2 mb-3 fw-bold text-dark border-bottom"><i class="fas fa-list-ol text-danger me-2"></i>{{ __('table_of_contents_title') }}</h5>
                    <div style="line-height: 1.7; text-align: justify; white-space: pre-line;">{{ $book->content ?? __('no_contents') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection