@extends('admin.layout.master')

@section('content')
    <div class="py-4 container-fluid">
        <!-- Dashboard Header & Actions (Search Box & Create Button Only) -->
        <div class="mb-4 border-0 shadow-sm card">
            <div class="card-body">
                <!-- Create Success Alert (Green) -->
                @if (session('createSuccess'))
                    <div class="mb-4 border-0 shadow-sm alert alert-success alert-dismissible fade show d-flex align-items-center"
                        role="alert">
                        <i class="bi bi-plus-circle-fill me-2 fs-5"></i>
                        <div>
                            <strong>{{ __('Created!') }}</strong> {{ session('createSuccess') }}
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Update Success Alert (Blue / Info) -->
                @if (session('updateSuccess'))
                    <div class="mb-4 border-0 shadow-sm alert alert-info alert-dismissible fade show d-flex align-items-center"
                        role="alert">
                        <i class="bi bi-pencil-square me-2 fs-5"></i>
                        <div>
                            <strong>{{ __('Updated!') }}</strong> {{ session('updateSuccess') }}
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Delete Success Alert (Yellow / Warning) -->
                @if (session('deleteSuccess'))
                    <div class="mb-4 border-0 shadow-sm alert alert-warning alert-dismissible fade show d-flex align-items-center"
                        role="alert">
                        <i class="bi bi-trash-fill me-2 fs-5"></i>
                        <div>
                            <strong>{{ __('Deleted!') }}</strong> {{ session('deleteSuccess') }}
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                <div class="row g-3 align-items-center">

                    <!-- Search Box with Click Button -->
                    <div class="col-12 col-md-6 col-lg-5">
                        <form action="{{ route('list#yearDetails') }}" method="GET">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control"
                                    placeholder="{{ __('Search academic year...') }}" value="{{ request('search') }}">
                                <button class="btn btn-outline-primary" type="submit" id="button-search">
                                    <i class="bi bi-search me-1"></i> {{ __('Search') }}
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Create Year Button -->
                    <div class="col-12 col-md-6 col-lg-3 text-md-end ms-auto">
                        <span data-bs-toggle="tooltip" data-bs-placement="top"
                            data-bs-title="{{ __('Click create year') }}">
                            <button type="button" class="px-4 btn btn-primary w-100 w-md-auto" data-bs-toggle="modal"
                            data-bs-target="#createYearModal">
                            <i class="bi bi-plus-circle me-2"></i>{{ __('Create Year') }}
                        </button>
                        </span>
                    </div>

                </div>
            </div>
        </div>

        <!-- Data Presentation Area -->
        <div class="border-0 shadow-sm card">
            <div class="py-3 bg-white border-0 card-header">
                <h5 class="mb-0 text-secondary fw-bold">{{ __('Academic Years') }}</h5>
            </div>
            <div class="p-0 card-body">
                <div class="table-responsive">
                    <table class="table mb-0 align-middle table-hover text-nowrap">
                        <thead class="table-light text-muted">
                            <tr>
                                <th class="ps-4" style="width: 10%;">{{ __('ID') }}</th>
                                <th style="width: 45%;">{{ __('Academic Year') }}</th>
                                <th style="width: 25%;">{{ __('Created At') }}</th>
                                <th class="text-end pe-4" style="width: 20%;">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($academicYears as $key => $year)
                                <tr>
                                    <!-- Display continuous index number count -->
                                    <td class="ps-4 fw-medium">{{ $key + 1 }}</td>
                                    <td>{{ $year->academic_year }}</td>
                                    <td class="text-muted small">
                                        {{ $year->created_at ? $year->created_at->format('d-M-Y') : 'N/A' }}
                                    </td>
                                    <td class="text-end pe-4">
                                        <!-- Edit Modal Trigger Button -->
                                        @if($year->academic_year!='Admin')
                                        <button class="btn btn-sm btn-outline-secondary me-1" data-bs-toggle="modal"
                                            data-bs-target="#editYearModal{{ $year->id }}">
                                            <i class="bi bi-pencil-square me-1"></i>{{ __('Edit') }}
                                        </button>

                                        <!-- Delete Trigger Button -->
                                        
                                        <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                                            data-bs-target="#deleteYearModal{{ $year->id }}">
                                            <i class="bi bi-trash"></i> {{ __('Delete') }}
                                        </button>
                                        @endif
                    
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-4 text-center text-muted">
                                        {{ __('No records found.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- CREATE MODAL -->
    <div class="modal fade" id="createYearModal" tabindex="-1" aria-labelledby="createYearModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm-down-fullscreen">
            <div class="border-0 shadow modal-content">
                <div class="text-white modal-header bg-primary">
                    <h5 class="modal-title" id="createYearModalLabel"><i
                            class="bi bi-calendar-plus me-2"></i>{{ __('Add Academic Year') }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form action="{{ route('store#yearDetails') }}" method="POST">
                    @csrf
                    <div class="p-4 modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary">{{ __('Academic Year') }}</label>
                            <input type="text" class="form-control" name="academic_year"
                                placeholder="{{ __('Create Year') }}">
                        </div>
                    </div>
                    <div class="px-4 modal-footer bg-light border-top-0">
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="px-4 btn btn-primary">{{ __('Save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- DYNAMIC MODALS LOOP (Generates unique update and delete target layers) -->
    @foreach ($academicYears as $year)
        <!-- EDIT / UPDATE MODAL -->
        <div class="modal fade" id="editYearModal{{ $year->id }}" tabindex="-1"
            aria-labelledby="editYearModalLabel{{ $year->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="border-0 shadow modal-content">
                    <div class="text-white modal-header bg-secondary">
                        <h5 class="modal-title" id="editYearModalLabel{{ $year->id }}"><i
                                class="bi bi-pencil-square me-2"></i>{{ __('Update Academic Year') }}</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <form action="{{ route('update#yearDetails', $year->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="p-4 modal-body">
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-secondary">{{ __('Academic Year') }}</label>
                                <input type="text" class="form-control" name="academic_year"
                                    value="{{ $year->academic_year }}" required>
                            </div>
                        </div>
                        <div class="px-4 modal-footer bg-light border-top-0">
                            <button type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                            <button type="submit" class="px-4 text-white btn btn-success">{{ __('Update') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- CONFIRM DELETE MODAL -->
        <div class="modal fade" id="deleteYearModal{{ $year->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="border-0 shadow modal-content">
                    <div class="p-4 text-center modal-body">
                        <div class="mb-3 text-danger fs-1"><i class="bi bi-exclamation-circle"></i></div>
                        <h5 class="mb-2 fw-bold text-secondary">{{ __('Are you sure?') }}</h5>
                        <p class="mb-0 text-muted small">
                            {{ __('This action will permanently delete this academic year record.') }}</p>
                    </div>
                    <div class="pb-4 bg-white border-0 justify-content-center modal-footer">
                        <button type="button" class="px-3 btn btn-sm btn-secondary"
                            data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <form action="{{ route('delete#yearDetails', $year->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3 btn btn-sm btn-danger">{{ __('Delete') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endsection
