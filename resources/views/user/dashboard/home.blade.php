@extends('user.layout.master')

<style>.custom-bg1 {
    background-image: url('{{ asset("image/hb1.jpg") }}');
    background-size: cover;
    background-position: center center;
    background-repeat: no-repeat;
    background-attachment: fixed;
}

/* Screen width 768px အောက် (Mobile ဖုန်းများ) အတွက် ပုံ */
@media (max-width: 768px) {
    .custom-bg1 {
        background-image: url('{{ asset("image/hb2.jpg") }}');
    }
}</style>
@section('content')
    <div class="min-vh-100" style="background: #f4f6f9;"> 
        
        {{-- HERO SECTION (Background Image သီးသန့်) --}}
        {{-- <div class="py-5 text-white shadow-sm position-relative" style="background: url('{{ asset('image/hb1.jpg') }}'); background-size: cover; background-position: center center; background-repeat: no-repeat; background-attachment: fixed; height: 500px;"> --}}
            <div class="py-5 text-white custom-bg1">
            <div class="container py-4 text-center">
                {{-- Main Title & Subtitle --}}
                <h1 class="mb-2 italic fw-bold display-1 " style="letter-spacing: 0.5px;margin-top:100px">Library Digital Catalogue System</h1>
                <p class="mb-4 text-white-50 fs-5">{{__('Search_')}} <span class="mx-2">•</span> {{__('Discover')}} <span class="mx-2">•</span> {{__('Read')}}</p>
            </div>
        </div>

        {{-- STICKY SEARCH BAR SECTION (Hero အပြင်ဘက်သို့ ထုတ်လိုက်ပါပြီ) --}}
        <div class="py-3 shadow-sm sticky-top" style="top: 56px; z-index: 1020; background-color: rgba(244, 246, 249, 0.95); backdrop-filter: blur(5px);">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-12 col-md-8 col-lg-7">
                        <form action="{{ url()->current() }}" method="GET" 
                              class="p-1 bg-white border shadow rounded-pill d-flex align-items-center" 
                              id="searchForm">
                            
                            <input type="hidden" name="category_id" id="selected_category_id" value="{{ request('category_id') }}">
                            
                            {{-- Category Dropdown --}}
                            <div class="flex-shrink-0 dropdown">
                                <button class="px-3 border-0 btn btn-light dropdown-toggle rounded-pill text-dark fw-medium text-truncate" 
                                        style="max-width: 150px; background: transparent;" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-grid-3x3-gap-fill text-primary me-1"></i>
                                    <span id="categoryLabel" class="d-none d-sm-inline">
                                        {{ request('category_id') && isset($categories) && $categories->firstWhere('id', request('category_id')) 
                                           ? $categories->firstWhere('id', request('category_id'))->name 
                                           : __('all_categories') }}
                                    </span>
                                </button>
                                <ul class="mt-2 border-0 shadow-sm dropdown-menu rounded-4">
                                    <li><a class="dropdown-item" href="#" onclick="selectCategory(event, '', '{{ __('all_categories') }}')">
                                        <i class="bi bi-bookmark-fill me-2 text-primary"></i> {{ __('all_categories') }}
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    @foreach ($categories as $cat)
                                        <li><a class="dropdown-item" href="#" onclick="selectCategory(event, '{{ $cat->id }}', '{{ $cat->name }}')">
                                            <i class="bi bi-folder2-open me-2 text-secondary"></i> {{ $cat->name }}
                                        </a></li>
                                    @endforeach
                                </ul>
                            </div>

                            {{-- Search Input --}}
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   class="px-3 bg-transparent border-0 shadow-none form-control rounded-pill text-dark" 
                                   placeholder="{{ __('search_placeholder') ?? 'Search books, authors, or categories...' }}">

                            {{-- Submit Button --}}
                            <button type="submit" class="flex-shrink-0 px-4 py-2 shadow-sm btn btn-warning rounded-pill fw-bold text-dark" 
                                    style="background-color: #f0ad4e; border: none;">
                                <span class="d-none d-sm-inline me-1">{{__('Search')}}</span> <i class="bi bi-search"></i> 
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- SUB-BAR: TOTAL COUNTER SECTION --}}
        <div class="container mt-4">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="mb-0 fw-bold text-dark fs-6">
                    <i class="bi bi-journal-bookmark-fill text-primary me-2"></i> {{ __('Featured Books') }}
                </h3>
                <span class="px-3 py-2 bg-white border shadow-sm badge text-primary rounded-pill fw-semibold">
                    {{ __('Books Total') }} {{ $totalBooks }} {{ __('Books') }}
                </span>
            </div>
        </div>

        {{-- BOOKS GRID SECTION --}}
        {{-- <div class="container mt-3 mb-5">
            <div class="row g-4">
                @if (isset($books) && $books->count() > 0)
                    @foreach ($books as $book)
                        <div class="col-6 col-md-4 col-lg-3 col-xl-2">
                            <div class="overflow-hidden bg-white border-0 shadow-sm card h-100 rounded-4">
                                <div class="position-relative" style="aspect-ratio: 2/3;">
                                    <span class="position-absolute top-0 start-0 m-2 badge {{ $book->available_qty > 0 ? 'bg-success' : 'bg-danger' }}">
                                        {{ $book->available_qty > 0 ? __('available') : __('out_of_stock') }}
                                    </span>
                                    @if ($book->cover_image)
                                        <img src="{{ asset('storage/books/' . $book->cover_image) }}" class="w-100 h-100 object-fit-cover" alt="{{ $book->title }}">
                                    @else
                                        <div class="w-100 h-100 bg-secondary-subtle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-book text-muted fs-1"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="p-3 text-center d-flex flex-column">
                                    <h6 class="mb-1 fw-bold text-truncate" title="{{ $book->title }}">{{ $book->title }}</h6>
                                    <small class="mb-3 text-muted d-block text-truncate">{{ $book->category->name ?? __('general') }}</small>
                                    <div class="mt-auto">
                                        <a href="{{ route('user#bookShow', $book->id) }}" class="btn btn-outline-primary btn-sm w-100 rounded-pill fw-bold fs-6">
                                            <i class="bi bi-book me-1"></i> {{ __('detail_borrow') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="py-5 text-center bg-white shadow-sm col-12 rounded-4">
                        <i class="mb-3 bi bi-search fs-1 text-muted d-block"></i>
                        <p class="text-dark fs-4 fw-bold">{{ __('no_books_found') }}</p>
                    </div>
                @endif
            </div>
            
            
            <div class="mt-4 d-flex justify-content-center">
                {{ $books->links() }}
            </div>
        </div> --}}
        {{-- BOOKS GRID SECTION --}}
<div class="px-4 mt-3 mb-5 container-fluid">
    <style>
        /* Custom CSS to make exactly 8 cards fit in a single row on desktop screens */
        @media (min-width: 992px) {
            .col-lg-8-custom {
                flex: 0 0 12.5%;
                max-width: 12.5%;
            }
        }
    </style>

    <div class="row g-2">
        @if (isset($books) && $books->count() > 0)
            @foreach ($books as $book)
                <div class="col-6 col-sm-4 col-md-3 col-lg-8-custom">
                    <div class="overflow-hidden bg-white border-0 shadow-sm card h-100 rounded-3 position-relative" style="aspect-ratio: 2/3;">
                        
                        {{-- Full Bright Cover Image --}}
                        @if ($book->cover_image)
                            <img src="{{ asset('storage/books/' . $book->cover_image) }}" class="top-0 w-100 h-100 object-fit-cover position-absolute start-0" alt="{{ $book->title }}">
                        @else
                            <div class="top-0 w-100 h-100 bg-secondary-subtle position-absolute start-0 d-flex align-items-center justify-content-center">
                                <i class="bi bi-book text-muted fs-5"></i>
                            </div>
                        @endif

                        {{-- Top Availability Badge --}}
                        <div class="top-0 m-1 position-absolute start-0 z-1">
                            <span class="badge shadow-sm {{ $book->available_qty > 0 ? 'bg-success' : 'bg-danger' }}" style="font-size: 0.5rem; padding: 0.2em 0.4em;">
                                {{ $book->available_qty > 0 ? __('available') : __('out_of_stock') }}
                            </span>
                        </div>

                        {{-- Glassmorphism Bottom Overlay --}}
                        <div class="position-absolute bottom-0 start-0 w-100 p-1.5 z-1" style="background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);">
                            <h6 class="mb-1 fw-bold text-dark text-truncate" title="{{ $book->title }}" style="font-size: 0.75rem;">{{ $book->title }}</h6>
                            <small class="mb-1 text-muted d-block text-truncate" style="font-size: 0.65rem;">{{ $book->category->name ?? __('general') }}</small>
                            <a href="{{ route('user#bookShow', $book->id) }}" class="py-0 btn btn-outline-primary btn-sm w-100 rounded-pill fw-bold" style="font-size: 0.65rem;">
                                <i class="bi bi-book me-1"></i> {{ __('detail_borrow') }}
                            </a>
                        </div>

                    </div>
                </div>
            @endforeach
        @else
            <div class="py-5 text-center bg-white shadow-sm col-12 rounded-4">
                <i class="mb-3 bi bi-search fs-1 text-muted d-block"></i>
                <p class="text-dark fs-4 fw-bold">{{ __('no_books_found') }}</p>
            </div>
        @endif
    </div>
    
    {{-- Pagination Links --}}
    <div class="mt-4 d-flex justify-content-center">
        {{ $books->links() }}
    </div>
</div>
    </div>

    {{-- SCRIPTS --}}
    <script>
        function selectCategory(event, id, name) {
            event.preventDefault();
            // Update the hidden input
            document.getElementById('selected_category_id').value = id;
            // Update the display text
            document.getElementById('categoryLabel').innerText = name;
            // Submit the form
            document.getElementById('searchForm').submit();
        }
    </script>
@endsection