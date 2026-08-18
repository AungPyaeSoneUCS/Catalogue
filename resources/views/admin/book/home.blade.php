@extends('admin.layout.master')

@section('content')
    <div class="py-4 container-fluid">

        {{-- Error Flash Messages --}}
        @if (session()->has('excel_validation_errors'))
            <div class="border-0 shadow-sm alert alert-danger alert-dismissible fade show" role="alert">
                <h5 class="fw-bold">
                    <i class="fas fa-times-circle me-2"></i>
                    {{ __('excel_import_failed') }}
                </h5>
                <p class="mb-2 small">{{ __('excel_import_failed_note') }}</p>

                <div style="max-height: 200px; overflow-y: auto;">
                    <ul class="mb-0 small">
                        @foreach (session()->get('excel_validation_errors') as $validation)
                            <li class="mb-1">
                                <strong class="text-danger">{{ __('row_number') }}: {{ $validation->row() }}</strong> -
                                @foreach ($validation->errors() as $e)
                                    <span class="px-1 rounded text-dark bg-light">{{ $e }}</span>
                                @endforeach
                                <span class="text-muted">({{ __('column') }}: {{ $validation->attribute() }})</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('success'))
            <div class="border-0 shadow-sm alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="border-0 shadow-sm alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Header Section --}}
        <div class="mb-4 row g-3 align-items-center">
            <div class="col-12 col-md-7 col-lg-6">
                <div>
                    <h4 class="mb-1 fw-bold text-dark">{{ __('books_management') }}</h4>
                    <div class="flex-wrap gap-2 d-flex align-items-center">
                        <p class="mb-0 text-muted small">{{ __('books_management_desc') }}</p>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-5 col-lg-6 text-md-end">
                <div class="flex-wrap gap-2 d-flex justify-content-start justify-content-md-end">
                    <a href="{{ route('list#bookExport', request()->query()) }}"
                        class="gap-2 px-3 py-2 border-2 shadow-sm btn btn-outline-success d-flex align-items-center small fw-bold"
                        data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="{{ __('Click export excel') }}">
                        <i class="fas fa-file-export"></i> <span>{{ __('export') }}</span>
                    </a>
                    <span data-bs-toggle="tooltip" data-bs-placement="bottom"
                        data-bs-title="{{ __('Click import excel') }}">
                        <button type="button"
                            class="gap-2 px-3 py-2 shadow-sm btn btn-success d-flex align-items-center small fw-bold"
                            data-bs-toggle="modal" data-bs-target="#excelImportModal">
                            <i class="fas fa-file-import"></i> <span>{{ __('import') }}</span>
                        </button>
                    </span>
                    <span data-bs-toggle="tooltip" data-bs-placement="bottom"
                        data-bs-title="{{ __('Click create book') }}">
                        <button type="button"
                            class="gap-2 px-3 py-2 shadow-sm btn btn-primary d-flex align-items-center small fw-bold"
                            data-bs-toggle="modal" data-bs-target="#createBookModal">
                            <i class="fas fa-plus"></i> <span>{{ __('create') }}</span>
                        </button>
                    </span>
                </div>
            </div>
        </div>

        <span class="px-2 py-0.5 rounded-pill badge bg-primary text-white fw-bold small shadow-sm mb-3 d-inline-block"
            style="font-size: 11px;">
            <i class="fas fa-book-reader me-1"></i> {{ $totalBooksCount }} {{ __('books_count_unit') }}
        </span>

        {{-- Filter & Search Section --}}
        <div class="p-3 mb-4 bg-white border-0 shadow-sm card rounded-3">
            <form action="{{ route('list#bookDetails') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-md-6 col-12">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="form-control border-start-0 bg-light" placeholder="{{ __('search') }}">
                    </div>
                </div>
                <div class="col-md-4 col-8">
                    <select name="category_id" class="form-select bg-light" onchange="this.form.submit()">
                        <option value="">{{ __('all_categories') }}</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}"
                                {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 col-4">
                    <button type="submit" class="btn btn-dark w-100">{{ __('filter') }}</button>
                </div>
            </form>
        </div>

        {{-- Table Data Section --}}
        <div class="overflow-hidden border-0 shadow-sm card rounded-3 ">
            <div class="table-responsive" style="max-height: 75vh; overflow-y: auto;">
                <table class="table mb-0 align-middle table-hover" style="min-width: 1200px;">
                    <thead class="table-light text-secondary text-nowrap">
                        <tr>
                            <th class="ps-4" style="width: 60px;">{{ __('id') }}</th>
                            <th style="width: 80px;">{{ __('cover') }}</th>
                            <th>{{ __('book_title') }}</th>
                            <th>{{ __('author') }}</th>
                            <th>{{ __('category') }}</th>
                            <th>{{ __('code') }}</th>
                            <th>{{ __('press') }}</th>
                            <th class="text-center">{{ __('total_qty') }}</th>
                            <th class="text-center">{{ __('available_quantity') }}</th>
                            <th>{{ __('created_at') }}</th>
                            <th class="text-center" style="width: 160px;">{{ __('action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($books as $key=>$book)
                            <tr>
                                <td class="ps-4 text-muted fw-bold">{{ $key + 1 }}</td>
                                <td>
                                    @if ($book->cover_image)
                                        <img src="{{ asset('storage/books/' . $book->cover_image) }}"
                                            class="border rounded shadow-sm"
                                            style="height: 60px; width: 45px; object-fit: cover;">
                                    @else
                                        <img src="{{ asset('image/catalog-logo.jpg') }}" class="border rounded shadow-sm"
                                            style="height: 60px; width: 45px; object-fit: cover;" alt="No Cover">
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-bold text-dark text-truncate" style="max-width: 180px;"
                                        title="{{ $book->title }}">{{ $book->title }}</div>
                                </td>
                                <td><div class="text-muted small text-truncate" style="max-width: 120px;" title="{{ $book->author ?? __('unknown_author') }}">{{ $book->author ?? __('unknown_author') }}</div></td>
                                <td>
                                    <span class="px-2 py-1 rounded badge bg-primary bg-opacity-10 text-primary">
                                        {{ $book->category ? $book->category->name : __('general') }}
                                    </span>
                                </td>
                                <td><span class="border badge bg-light text-dark">{{ $book->code ?? '-' }}</span></td>
                                <td><span class="text-muted small">{{ $book->press ?? '-' }}</span></td>
                                <td class="text-center fw-bold text-secondary">{{ $book->total_qty }}</td>
                                <td class="text-center fw-bold text-secondary">
                                    {{ $book->available_qty ?? $book->total_qty }}</td>
                                <td><span class="text-muted small"
                                        style="font-size: 11px;">{{ $book->created_at ? $book->created_at->tz('Asia/Yangon')->format('d-M-Y') : '-' }}</span>
                                </td>
                                <td class="text-center pe-4">
                                    <div class="gap-2 d-flex" role="group">
                                        <a href="{{ route('books.show', $book->id) }}"
                                            class="btn btn-outline-primary btn-sm" title="{{ __('view_details') }}">
                                            <i class="fas fa-eye me-2"></i>
                                        </a>
                                        <a href="{{ route('books.edit', $book->id) }}"
                                            class="btn btn-outline-warning btn-sm" title="{{ __('edit_book') }}">
                                            <i class="fas fa-edit me-2"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-danger btn-sm rounded-end"
                                            data-bs-toggle="modal" data-bs-target="#deleteModal{{ $book->id }}"
                                            title="{{ __('delete_book') }}">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            {{-- Delete Confirmation Modal --}}
                            <div class="modal fade" id="deleteModal{{ $book->id }}" tabindex="-1"
                                aria-labelledby="deleteModalLabel{{ $book->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="border-0 shadow modal-content">
                                        <div class="py-3 text-white border-0 modal-header bg-danger">
                                            <h5 class="modal-title fw-bold" id="deleteModalLabel{{ $book->id }}">
                                                <i class="fas fa-exclamation-triangle me-2"></i>
                                                {{ __('confirm_delete') }}
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white"
                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="p-4 text-center modal-body">
                                            <div class="mb-3 text-danger">
                                                <i class="fas fa-trash-alt fa-3x"></i>
                                            </div>
                                            <h5 class="fw-bold text-dark">{{ __('are_you_sure') }}</h5>
                                            <p class="mb-0 text-muted small">
                                                {{ __('delete_warning_prefix') }} <strong
                                                    class="text-danger">"{{ $book->title }}"</strong>. <br>
                                                {{ __('delete_warning_suffix') }}
                                            </p>
                                        </div>
                                        <div
                                            class="gap-2 py-3 border-0 modal-footer bg-light d-flex justify-content-center">
                                            <button type="button" class="px-4 btn btn-outline-secondary rounded-pill"
                                                data-bs-dismiss="modal">{{ __('cancel') }}</button>
                                            <form action="{{ route('books.destroy', $book->id) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-4 shadow-sm btn btn-danger rounded-pill">
                                                    <i class="fas fa-check me-1"></i> {{ __('yes_delete') }}
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="11" class="py-5 text-center bg-white">
                                    <i class="mb-3 fas fa-book-open text-muted fs-1"></i>
                                    <p class="mb-0 text-muted">{{ __('no_books_found') }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-4">
                {{ $books->links() }}
            </div>
            </div>
            
        </div>

    </div>

    {{-- Excel Import Modal --}}
    <div class="modal fade" id="excelImportModal" data-bs-backdrop="static" tabindex="-1"
        aria-labelledby="excelImportModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="border-0 shadow modal-content">
                <div class="text-white modal-header bg-success">
                    <h5 class="modal-title fw-bold" id="excelImportModalLabel"><i class="fas fa-file-excel me-2"></i>
                        {{ __('bulk_import') }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form action="{{ route('books.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="p-4 modal-body">
                        <div class="p-2 mb-3 text-center border rounded bg-light text-nowrap">
                            <small class="text-muted">
                                {{ __('need_format_help') }}?
                                <a href="{{ route('books.download-template') }}" class="text-decoration-none fw-bold">
                                    <i class="fas fa-download me-1"></i> {{ __('download_excel_template') }}
                                </a>
                            </small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">{{ __('excel_file') }} (.xlsx)</label>
                            <input type="file" name="excel_file"
                                class="form-control @error('excel_file', 'excel_errors') is-invalid @enderror">
                            @error('excel_file', 'excel_errors')
                                <div class="invalid-feedback fw-bold">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-1">
                            <label class="form-label small fw-bold text-secondary">{{ __('zip_file') }} (.zip)</label>
                            <input type="file" name="cover_zip"
                                class="form-control @error('cover_zip', 'excel_errors') is-invalid @enderror">
                            @error('cover_zip', 'excel_errors')
                                <div class="invalid-feedback fw-bold">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="border-0 modal-footer bg-light">
                        <button type="button" class="px-3 btn btn-outline-secondary btn-sm"
                            data-bs-dismiss="modal">{{ __('close') }}</button>
                        <button type="submit" class="px-4 btn btn-success btn-sm">{{ __('start_import') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Create Book Modal --}}
    <div class="modal fade" id="createBookModal" data-bs-backdrop="static" tabindex="-1"
        aria-labelledby="createBookModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="border-0 shadow modal-content">
                <div class="text-white modal-header bg-primary">
                    <h5 class="modal-title fw-bold" id="createBookModalLabel"><i class="fas fa-book me-2"></i>
                        {{ __('create_single_book') }}</h5>


                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form action="{{ route('books.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="p-4 modal-body">
                        {{-- Preview Image ပေါ်မည့်နေရာ --}}

                        <img id="imagePreview" src="#" alt="Preview"
                            style="display: none; height: 100px; width: auto;" class="border rounded shadow-sm">
                        {{-- Form Row 1: Title & Author --}}
                        <div class="mb-3 row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-secondary">{{ __('book_title') }} *</label>
                                <input type="text" name="title" value="{{ old('title') }}"
                                    class="form-control @error('title') is-invalid @enderror"
                                    placeholder="{{ __('title_placeholder') }}">
                                @error('title')
                                    <div class="invalid-feedback fw-bold">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-secondary">{{ __('author_name') }} *</label>
                                <input type="text" name="author" value="{{ old('author') }}"
                                    class="form-control @error('author') is-invalid @enderror"
                                    placeholder="{{ __('author_placeholder') }}">
                                @error('author')
                                    <div class="invalid-feedback fw-bold">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Form Row 2: Code, Press & Category --}}
                        <div class="mb-3 row g-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-secondary">{{ __('code') }} *</label>
                                <input type="text" name="code" value="{{ old('code') }}"
                                    class="form-control @error('code') is-invalid @enderror"
                                    placeholder="{{ __('code_placeholder') }}">
                                @error('code')
                                    <div class="invalid-feedback fw-bold">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-secondary">{{ __('press') }}</label>
                                <input type="text" name="press" value="{{ old('press') }}"
                                    class="form-control @error('press') is-invalid @enderror"
                                    placeholder="{{ __('press_placeholder') }}">
                                @error('press')
                                    <div class="invalid-feedback fw-bold">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-secondary">{{ __('category') }} *</label>
                                <select name="category_id" class="form-select @error('category_id') is-invalid @enderror">
                                    <option value="">{{ __('select_category') }}</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}"
                                            {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback fw-bold">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Form Row 3: Total Qty & Cover Image --}}
                        <div class="mb-3 row g-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-secondary">{{ __('total_qty') }} *</label>
                                <input type="number" name="total_qty" value="{{ old('total_qty') }}"
                                    class="form-control @error('total_qty') is-invalid @enderror"
                                    placeholder="{{ __('Books Total') }}" min="0">
                                @error('total_qty')
                                    <div class="invalid-feedback fw-bold">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-8">
                                <label class="form-label small fw-bold text-secondary">{{ __('book_cover_image') }}
                                    *</label>
                                {{-- 'onchange' event ကို ထည့်ပေးလိုက်ပါ --}}
                                <input type="file" name="cover_file" id="coverImageInput"
                                    class="form-control @error('cover_file') is-invalid @enderror" accept="image/*">



                                @error('cover_file')
                                    <div class="invalid-feedback fw-bold">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Form Row 4: Abstract & Content --}}
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-secondary">{{ __('book_abstract') }}
                                    *</label>
                                <textarea name="abstract" class="form-control @error('abstract') is-invalid @enderror" rows="3"
                                    placeholder="{{ __('Abstract Details') }}">{{ old('abstract') }}</textarea>
                                @error('abstract')
                                    <div class="invalid-feedback fw-bold">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-secondary">{{ __('table_of_content') }}
                                    *</label>
                                <textarea name="content" class="form-control @error('content') is-invalid @enderror" rows="3"
                                    placeholder="{{ __('Content Detail') }}">{{ old('content') }}</textarea>
                                @error('content')
                                    <div class="invalid-feedback fw-bold">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                    </div>
                    <div class="border-0 modal-footer bg-light">
                        <button type="button" class="px-3 btn btn-outline-secondary btn-sm"
                            data-bs-dismiss="modal">{{ __('cancel') }}</button>
                        <button type="submit" class="px-4 btn btn-primary btn-sm">{{ __('save_book') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @if ($errors->any())
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                // Error ရှိနေရင် createBookModal ကို အလိုအလျောက် ပြန်ဖွင့်ခိုင်းတာဖြစ်ပါတယ်
                var myModal = new bootstrap.Modal(document.getElementById('createBookModal'));
                myModal.show();
            });
        </script>
    @endif
@endsection
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Image Preview Logic
        const imageInput = document.getElementById('coverImageInput');
        const imagePreview = document.getElementById('imagePreview');

        imageInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.setAttribute('src', e.target.result);
                    imagePreview.style.display = 'block'; // ပုံကို ပြသရန်
                }
                reader.readAsDataURL(file);
            }
        });
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Modal Element တွေကို ရွေးချယ်မယ်
        const excelModalEl = document.getElementById('excelImportModal');
        const bookModalEl = document.getElementById('createBookModal');

        // Instance တွေကို အရင် create လုပ်ထားမယ်
        const excelModal = excelModalEl ? bootstrap.Modal.getOrCreateInstance(excelModalEl) : null;
        const bookModal = bookModalEl ? bootstrap.Modal.getOrCreateInstance(bookModalEl) : null;

        // Error ရှိရင် ဘယ် Modal ကို ပြမလဲဆိုတာ ဆုံးဖြတ်မယ်
        @if ($errors->hasBag('excel_errors'))
            if (excelModal) excelModal.show();
        @elseif ($errors->any())
            if (bookModal) bookModal.show();
        @endif
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Modal ပိတ်လိုက်တိုင်း လုပ်ဆောင်မယ့် Logic
        const modals = document.querySelectorAll('.modal');

        modals.forEach(modal => {
            modal.addEventListener('hidden.bs.modal', function() {
                // Backdrop အားလုံးကို ဖယ်ရှားခြင်း
                document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());

                // Body မှ modal-open class ကို ဖယ်ရှားခြင်း
                document.body.classList.remove('modal-open');

                // Inline style များကို ပြန်ဖျက်ခြင်း (Body ရဲ့ scroll ပြန်ရစေရန်)
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';
            });
        });
    });
</script>
