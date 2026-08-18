@extends('admin.layout.master')
@section('content')
    <div class="py-4 shadow-lg container-fluid">

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Close') }}"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Close') }}"></button>
            </div>
        @endif

        <div class="border-0 shadow-sm card">
            <div
                class="gap-3 py-3 bg-white card-header d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                <div>
                    <h5 class="mb-0 text-primary fw-bold">
                        <i class="bi bi-people-fill me-2"></i>{{ __('User Management') }}
                    </h5>
                    <small class="text-muted">{{ __('Manage system users and export data') }}</small>
                </div>

                <a href="{{ route('list#userExport', request()->query()) }}"
                    class="shadow-sm btn btn-success d-flex align-items-center justify-content-center" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{ __('Click export excel') }}">
                    <i class="bi bi-file-earmark-excel-fill me-2"></i>
                    <span>{{ __('Export to Excel') }}</span>
                </a>
            </div>

            <div class="card-body">
                <form action="{{ route('list#userDetails') }}" method="GET" class="mb-4 row g-3">
                    <div class="col-12 col-lg-6">
                        <div class="input-group">
                            <span class="bg-white input-group-text border-end-0 text-muted">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" name="search" value="{{ request('search') }}"
                                class="form-control border-start-0 ps-0"
                                placeholder="{{ __('Search by name, roll, email...') }}">
                        </div>
                    </div>

                    <div class="col-12 col-md-8 col-lg-4">
                        <select name="year_semester" class="form-select">
                            <option value="" selected>{{ __('Filter by Year & Semester') }}</option>
                            @foreach ($years as $item)
                                <option value="{{ $item->id }}"
                                    {{ request('year_semester') == $item->id ? 'selected' : '' }}>
                                    {{ $item->academic_year }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 col-md-4 col-lg-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-funnel me-2"></i>{{ __('Apply Filter') }}
                        </button>
                    </div>
                </form>

                <div class="table-responsive" style="max-height: 75vh; overflow-y: auto;">
                    <table class="table align-middle table-hover" style="min-width: 800px;">
                        <thead class="table-light border-bottom">
                            <tr class="text-nowrap">
                                <th class="ps-3">{{ __('Profile') }}</th>
                                <th>{{ __('User Details') }}</th>
                                <th>{{ __('Year / Semester') }}</th>
                                <th>{{ __('Contact') }}</th>
                                <th>{{ __('Created At') }}</th>
                                <th class="text-center">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="text-nowrap">
                            @forelse($users as $user)
                                <tr >
                                    <td class="ps-3">
                                        <img src="{{ asset('userProfile/' . $user->profile) }}" alt="Profile"
                                            class="border rounded-circle" width="50" height="50"
                                            style="object-fit: cover;">
                                    </td>
                                    <td>
                                        
                                    <div class="fw-bold username">{{ $user->name }}</div>
                                    <small class="text-muted d-block">{{ $user->email }}</small>
                                    <small class="text-dark roll_number">{{ $user->roll_number }}</small>
                                    <small class="text-dark academic_year">| {{ $user->year->academic_year ?? 'N/A' }}</small>
                                    <small class="text-muted d-block">{{ $user->phone }}</small>
                                
                                    </td>
                                    <td>
                                        <span
                                            class="px-3 py-2 border badge bg-info-subtle text-info border-info-subtle rounded-pill">
                                            {{ $user->year_name ?? __('Unknown Year') }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="small"><i
                                                class="bi bi-envelope me-2 text-muted"></i>{{ $user->email }}</div>
                                        <div class="small"><i
                                                class="bi bi-telephone me-2 text-muted"></i>{{ $user->phone }}</div>
                                    </td>
                                    <td>
                                        <div class="small fw-semibold">{{ $user->created_at->tz('Asia/Yangon')->format('d-M-Y') }}</div>
                                        
                                    </td>
                                    <td>
                                        <div class="gap-2 d-flex justify-content-center">
                                            <button class="btn btn-sm btn-outline-warning"
                                                title="{{ __('Change Password') }}" data-bs-toggle="modal"
                                                data-bs-target="#passwordModal{{ $user->id }}">
                                                <i class="bi bi-key-fill"></i>
                                            </button>

                                            <form id="delete-form-{{ $user->id }}"
                                                action="{{ route('list#userDestroy', $user->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-outline-danger"
                                                    onclick="confirmDelete({{ $user->id }})"
                                                    title="{{ __('Delete User') }}">
                                                    <i class="bi bi-trash3-fill"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-4 text-center text-muted">
                                        {{ __('No users found matching your criteria.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @foreach ($users as $user)
        <div class="modal fade" id="passwordModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form action="{{ route('list#passwordUpdate', $user->id) }}" method="POST"
                    class="border-0 shadow modal-content">
                    @csrf
                    @method('PUT')
                    <div class="border-0 modal-header bg-warning text-dark">
                        <h6 class="modal-title fw-bold">{{ __('Update Password for') }} {{ $user->name }}</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="{{ __('Close') }}"></button>
                    </div>
                    <div class="p-4 modal-body text-start">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-dark">{{ __('New Password') }}</label>
                            <input type="password" name="password" class="form-control"
                                placeholder="{{ __('Enter new password') }}" required>
                        </div>
                        <div class="mb-0">
                            <label class="form-label small fw-bold text-dark">{{ __('Confirm Password') }}</label>
                            <input type="password" name="password_confirmation" class="form-control"
                                placeholder="{{ __('Repeat password') }}" required>
                        </div>
                    </div>
                    <div class="pt-0 border-0 modal-footer">
                        <button type="button" class="btn btn-light"
                            data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="px-4 btn btn-warning">{{ __('Update Now') }}</button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach
@endsection
@section('script-code')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: "{{ __('Are you sure?') }}",
                text: "{{ __('You won\'t be able to revert this request record!') }}",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#6c757d",
                confirmButtonText: "{{ __('Yes, delete it!') }}",
                cancelButtonText: "{{ __('Cancel') }}"
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }
    </script>
@endsection
