@extends('admin.layout.master')

@section('content')
    <div class="py-4 container-fluid">
        @if (session('bookUpdateSuccess'))
            <div class="border-0 shadow-sm alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('bookUpdateSuccess') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('available_error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="flex-shrink-0 bi bi-exclamation-triangle-fill me-2"></i> {{ session('available_error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
        <div class="mb-4 d-flex align-items-center">
            <a href="{{ route('list#bookDetails') }}" class="px-3 btn btn-outline-secondary btn-sm rounded-pill me-3">
                <i class="fas fa-arrow-left me-1"></i> {{ __('Back to List') }}
            </a>
            <div>
                <h4 class="mb-0 fw-bold text-dark">{{ __('Edit Book Information') }}</h4>
                <span class="text-muted small">{{ __('Update details for') }}: <strong
                        class="text-primary">{{ $book->title }}</strong></span>
            </div>
        </div>

        {{-- @if ($errors->any())
            <div class="mb-4 border-0 shadow-sm alert alert-danger alert-dismissible fade show" role="alert">
                <h6 class="fw-bold"><i class="fas fa-exclamation-triangle me-2"></i>
                    {{ __('Please fix the following errors') }}:</h6>
                <ul class="mb-0 small">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif --}}

        <div class="p-4 bg-white border-0 shadow-sm card rounded-3">
            <form action="{{ route('books.update', $book->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row g-4">
                    <div class="text-center col-lg-3 col-md-4 col-12 border-end">
                        <label
                            class="mb-3 form-label small fw-bold text-secondary d-block">{{ __('Current Book Cover') }}</label>

                        <div class="mb-3 position-relative d-inline-block">
                            @if ($book->cover_image)
                                <img src="{{ asset('storage/books/' . $book->cover_image) }}"
                                    class="border rounded shadow img-fluid" style="max-height: 250px; object-fit: cover;"
                                    id="coverPreview">
                            @else
                                <div class="mx-auto border rounded bg-light d-flex align-items-center justify-content-center text-muted"
                                    style="height: 220px; width: 165px;" id="coverPreviewPlaceholder">
                                    <i class="fas fa-book fs-1"></i>
                                </div>
                            @endif
                        </div>
                        <div class="px-2 mt-2 text-start">
                            <label
                                class="form-label small fw-bold text-secondary">{{ __('Upload New Cover (Optional)') }}</label>
                            <input type="file" name="cover_file" class="form-control form-control-sm" accept="image/*"
                                onchange="previewImage(this)">
                            <div class="form-text xsmall text-muted" style="font-size: 11px;">
                                {{ __('If left empty, the current cover will remain') }}.</div>
                        </div>
                    </div>

                    <div class="col-lg-9 col-md-8 col-12">
                        <h5 class="pb-2 mb-3 fw-bold text-dark border-bottom">
                            <i class="fas fa-edit text-warning me-2"></i>{{ __('Book Specifications') }}
                        </h5>

                        <div class="mb-3 row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-secondary">{{ __('Book Title') }} *</label>
                                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                                    value="{{ old('title', $book->title) }}">
                                @error('title')
                                    <div class="invalid-feedback fw-bold">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-secondary">{{ __('Author Name') }} *</label>
                                <input type="text" name="author" class="form-control @error('author') is-invalid @enderror"
                                    value="{{ old('author', $book->author) }}">
                                @error('author')
                                    <div class="invalid-feedback fw-bold">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3 row g-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-secondary">{{ __('Book Code') }} *</label>
                                <input type="text" name="code" class="form-control @error('code') is-invalid @enderror"
                                    value="{{ old('code', $book->code) }}">
                                @error('code')
                                    <div class="invalid-feedback fw-bold">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-secondary">{{ __('Press / Publisher') }}
                                    (Nullable)</label>
                                <input type="text" name="press" class="form-control"
                                    value="{{ old('press', $book->press) }}">
            
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-secondary">{{ __('Category') }} *</label>
                                <select name="category_id" class="form-select">
                                    <option value="">{{ __('Select Category') }}</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}"
                                            {{ old('category_id', $book->category_id) == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mb-4 row g-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-secondary">{{ __('Total Quantity') }} *</label>
                                <input type="numeric" name="total_qty" class="form-control @error('total_qty') is-invalid @enderror"
                                    value="{{ old('total_qty', $book->total_qty) }}">
                                 @error('total_qty')
                                    <div class="invalid-feedback fw-bold">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-secondary">{{ __('available_quantity') }} *</label>
                                <input type="numeric" name="available_qty" class="form-control @error('available_qty') is-invalid @enderror"
                                    value="{{ old('available_qty', $book->available_qty) }}">
                                 @error('available_qty')
                                    <div class="invalid-feedback fw-bold">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-secondary">{{ __('Book Abstract') }} *</label>
                                <textarea name="abstract" class="form-control" rows="5">{{ old('abstract', $book->abstract) }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-secondary">{{ __('Table of Content') }}
                                    *</label>
                                <textarea name="content" class="form-control" rows="5">{{ old('content', $book->content) }}</textarea>
                            </div>
                        </div>

                        <div class="gap-2 pt-3 mt-4 border-top d-flex justify-content-end">
                            <a href="{{ route('list#bookDetails') }}"
                                class="px-4 btn btn-outline-secondary">{{ __('Cancel') }}</a>
                            <button type="submit" class="px-4 shadow-sm btn btn-primary">
                                <i class="fas fa-save me-1"></i> {{ __('Update Changes') }}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script>
        function previewImage(input) {
            const preview = document.getElementById('coverPreview');
            const placeholder = document.getElementById('coverPreviewPlaceholder');

            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    if (preview) {
                        preview.src = e.target.result;
                    } else if (placeholder) {
                        // If there was no image previously, dynamically replace placeholder with an img tag
                        const newImg = document.createElement('img');
                        newImg.id = 'coverPreview';
                        newImg.className = 'img-fluid rounded shadow border';
                        newImg.style.maxHeight = '250px';
                        newImg.style.objectFit = 'cover';
                        newImg.src = e.target.result;
                        placeholder.replaceWith(newImg);
                    }
                }

                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endsection
