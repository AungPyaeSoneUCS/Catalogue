@extends('admin.layout.master')

@section('content')
    <div class="py-2 container-fluid">

        <!-- Action Notification Alerts Section -->
        @if (session('createSuccess'))
            <div class="mb-4 border-0 shadow-sm alert alert-success alert-dismissible fade show d-flex align-items-center"
                role="alert">
                <i class="bi bi-plus-circle-fill me-2 fs-5"></i>
                <div><strong>{{ __('Created!') }}</strong> {{ session('createSuccess') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('updateSuccess'))
            <div class="mb-4 border-0 shadow-sm alert alert-info alert-dismissible fade show d-flex align-items-center"
                role="alert">
                <i class="bi bi-pencil-square me-2 fs-5"></i>
                <div><strong>{{ __('Updated!') }}</strong> {{ session('updateSuccess') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('deleteSuccess'))
            <div class="mb-4 border-0 shadow-sm alert alert-warning alert-dismissible fade show d-flex align-items-center"
                role="alert">
                <i class="bi bi-trash-fill me-2 fs-5"></i>
                <div><strong>{{ __('Deleted!') }}</strong> {{ session('deleteSuccess') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Header Filter & Trigger Options Card -->
        <div class="mb-4 border-0 shadow-sm card sticky-top" style="z-index: 1000; top: 50px;">
            <div class="card-body">
                <div class="row g-3 align-items-center justify-content-between">

                    <!-- Search Form Grid Box -->
                    <div class="col-12 col-md-6 col-lg-5">
                        <form action="{{ route('list#categoryDetails') }}" method="GET">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control"
                                    placeholder="{{ __('Search category name or date...') }}"
                                    value="{{ request('search') }}">
                                <button class="btn btn-outline-primary" type="submit">
                                    <i class="bi bi-search me-1"></i> {{ __('Search') }}
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Creation Open Control Dialog Button -->
                    <div class="col-12 col-md-auto">
                        <span data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="{{ __('Click create subject') }}">
                            <button type="button" class="px-4 btn btn-primary w-100" data-bs-toggle="modal"
                            data-bs-target="#createCategoryModal">
                            <i class="bi bi-plus-circle me-2"></i>{{ __('Create Category') }}
                        </button>
                        </span>
                    </div>

                </div>
            </div>
        </div>

        <!-- Central Presentation Workspace Area Data Card -->
        <div class="border-0 shadow-sm card">
            <div class="py-3 bg-white border-0 card-header">
                <h5 class="mb-0 text-secondary fw-bold">{{ __('Categories') }}</h5>
            </div>
            <div class="p-0 card-body">
                <div class="table-responsive" style="max-height: 75vh; overflow-y: auto;">
                    <table class="table mb-0 align-middle table-hover text-nowrap" style="min-width: 800px;">
                        <thead class="table-light text-muted">
                            <tr>
                                <th class="ps-4" style="width: 10%;">{{ __('ID') }}</th>
                                <th style="width: 45%;">{{ __('Category Name') }}</th>
                                <th style="width: 25%;">{{ __('Created At') }}</th>
                                <th class="text-end pe-4" style="width: 20%;">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories as  $category)
                                <tr>
                                    <td class="ps-4 fw-medium">{{ $category->id }}</td>
                                    <td class="fw-semibold text-dark">{{ $category->name }}</td>
                                    <td class="text-muted small">
                                        {{ $category->created_at?->tz('Asia/Yangon')->format('d-M-Y') ?? 'N/A' }}
                                    </td>
                                    <td class="text-end pe-4">
                                        <button class="btn btn-sm btn-outline-secondary me-1" data-bs-toggle="modal"
                                            data-bs-target="#editCategoryModal{{ $category->id }}">
                                            <i class="bi bi-pencil-square me-1"></i>{{ __('Edit') }}
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                                            data-bs-target="#deleteCategoryModal{{ $category->id }}">
                                            <i class="bi bi-trash"></i> {{ __('Delete') }}
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-4 text-center text-muted">
                                        {{ __('No categories found matching your query details.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- CREATE CATEGORY CONTAINER DIALOG -->
    <div class="modal fade" id="createCategoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="border-0 shadow modal-content">
                <div class="text-white modal-header bg-primary">
                    <h5 class="modal-title"><i class="bi bi-tags me-2"></i>{{ __('Add New Category') }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form action="{{ route('store#categoryDetails') }}" method="POST">
                    @csrf
                    <div class="p-4 modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary">{{ __('Category Name') }}</label>

                            {{-- Input ထဲတွင် class ထည့်ပေးပြီး error ရှိလျှင် is-invalid ပေါ်လာအောင် လုပ်ထားသည် --}}
                            <input type="text" class="form-control @error('name') is-invalid @enderror" name="name"
                                placeholder="{{ __('Add Category') }}" value="{{ old('name') }}">

                            {{-- Validation error တက်လာလျှင် ဤနေရာတွင် စာသားပေါ်လာမည် --}}
                            @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="px-4 modal-footer bg-light border-top-0">
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="px-4 btn btn-primary">{{ __('Save Category') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- DYNAMIC TARGET LAYER INTERFACE LOOPS FOR EDIT & CONFIRMATION OVERLAYS -->
    @foreach ($categories as $category)
        <!-- EDIT/UPDATE TARGET DIALOGS -->
        <div class="modal fade" id="editCategoryModal{{ $category->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="border-0 shadow modal-content">
                    <div class="text-white modal-header bg-secondary">
                        <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>{{ __('Update Category') }}</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <form action="{{ route('update#categoryDetails', $category->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="p-4 modal-body">
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-secondary">{{ __('Category Name') }}</label>

                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    name="name" value="{{ old('name', $category->name) }}">

                                @error('name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="px-4 modal-footer bg-light border-top-0">
                            <button type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                            <button type="submit"
                                class="px-4 text-white btn btn-success">{{ __('Update Changes') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- RE-VALIDATION ABSOLUTE DELETE CONFIRM MODALS -->
        <div class="modal fade" id="deleteCategoryModal{{ $category->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="border-0 shadow modal-content">
                    <div class="p-4 text-center modal-body">
                        <div class="mb-3 text-danger fs-1"><i class="bi bi-exclamation-circle"></i></div>
                        <h5 class="mb-2 fw-bold text-secondary">{{ __('Are you sure?') }}</h5>
                        <p class="mb-0 text-muted small">
                            {{ __('This configuration deletes this category asset permanently.') }}</p>
                    </div>
                    <div class="pb-4 bg-white border-0 justify-content-center modal-footer">
                        <button type="button" class="px-3 btn btn-sm btn-secondary"
                            data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <form action="{{ route('delete#categoryDetails', $category->id) }}" method="POST"
                            class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3 btn btn-sm btn-danger">{{ __('Confirm Delete') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endsection
<script>
    document.addEventListener("DOMContentLoaded", function() {
        @if ($errors->any())
            var modalType = "{{ session('modal_type') }}";

            if (modalType === 'create') {
                var createModal = new bootstrap.Modal(document.getElementById('createCategoryModal'));
                createModal.show();
            } else if (modalType === 'edit') {
                var categoryId = "{{ session('category_id') }}";
                var editModalEl = document.getElementById('editCategoryModal' + categoryId);
                if (editModalEl) {
                    var editModal = new bootstrap.Modal(editModalEl);
                    editModal.show();
                }
            }
        @endif
    });
</script>
