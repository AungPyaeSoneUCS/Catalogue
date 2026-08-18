@extends('admin.layout.master')

@section('content')
@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
    <div class="pb-5 shadow-lg container-fluid">
        <div class="mb-4 d-flex justify-content-between align-items-center">
            <h2 class="h4 fw-bold text-secondary">
                <i class="bi bi-person-plus me-2"></i>{{ __('User Requests') }}
            </h2>
            <span class="badge bg-info text-dark">{{ __('Total Records') }}: {{ $totalRequests }}</span>
        </div>

        <div class="mb-4 row">
            <div class="col-12 col-md-4 col-lg-5">
                <form action="{{ route('request#userDetails') }}" method="GET">
                    <div class="shadow-sm input-group">
                        <span class="bg-white input-group-text border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0"
                            placeholder="{{ __('Search by name or roll...') }}" value="{{ request('search') }}">
                        <button class="btn btn-info text-dark" type="submit">{{ __('Search') }}</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="border-0 shadow-sm card">
            <div class="p-0 card-body">
                <div class="table-responsive">
                    <table class="table mb-0 align-middle table-hover">
                        <thead class="table-light text-nowrap">
                            <tr>
                                <th class="ps-4">{{ __('Profile') }}</th>
                                <th>{{ __('User Details') }}</th>
                                <th>{{ __('Academic Year') }}</th>
                                <th>{{ __('Contact') }}</th>
                                <th>{{ __('Payslip') }}</th>
                                <th>{{ __('Created At') }}</th>
                                <th class="text-center">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="text-nowrap">
                            @forelse($requests as $request)
                                <tr>
                                    <td class="ps-4">
                                        @if ($request->profile)
                                            <img src="{{ asset('userProfile/' . $request->profile) }}" alt="Profile"
                                                class="border rounded-circle" width="50" height="50"
                                                style="object-fit: cover;">
                                        @else
                                            <img src="{{ asset('image/catalog-logo.jpg') }}" alt="Profile"
                                                class="border rounded-circle" width="50" height="50">
                                        @endif
                                    </td>

                                    <td>
                                        <div class="fw-bold text-dark">{{ $request->name }}</div>
                                        <small class="text-muted">{{ $request->roll_number }}</small>
                                    </td>

                                    <td>
                                        <span class="border badge rounded-pill bg-light text-dark">
                                            {{ $request->year->academic_year ?? 'N/A' }}
                                        </span>
                                    </td>

                                    <td>
                                        <div class="small"><i class="bi bi-envelope me-1"></i> {{ $request->email }}</div>
                                        <div class="small"><i class="bi bi-phone me-1"></i> {{ $request->phone ?? 'N/A' }}
                                        </div>
                                    </td>

                                    <td>
                                        <button class="text-white shadow-sm btn btn-sm btn-info" data-bs-toggle="modal"
                                            data-bs-target="#payslipModal{{ $request->id }}">
                                            <i class="bi bi-image me-1"></i> {{ __('View Slip') }}
                                        </button>
                                    </td>
                                    <td>
                                        <div class="small"><i class="bi bi-envelope me-1"></i>
                                            {{ $request->created_at->format('d-M-Y') }}</div>
                                    </td>
                                    {{-- <td>
                                    <div class="gap-2 d-flex justify-content-center">
                                        <form id="accept-form-{{ $request->id }}" action="{{ route('request#acceptUser', $request->id) }}" method="POST" style="display: none;">
                                            @csrf
                                        </form>
                                        <form id="reject-form-{{ $request->id }}" action="{{ route('request#rejectUser', $request->id) }}" method="POST" style="display: none;">
                                            @csrf
                                        </form>

                                        <button type="button" class="px-3 btn btn-success btn-sm" onclick="acceptUser({{ $request->id }}, '{{ $request->name }}')">
                                            <i class="bi bi-check-lg me-1"></i> {{ __('Accept') }}
                                        </button>
                                        
                                        <button type="button" class="px-3 btn btn-danger btn-sm" onclick="rejectUser({{ $request->id }}, '{{ $request->name }}')">
                                            <i class="bi bi-x-lg me-1"></i> {{ __('Reject') }}
                                        </button>
                                    </div>
                                </td> --}}
                                    <!-- Table Action Column -->
                                    <!-- Table Action Column -->
<td>
    <div class="gap-2 d-flex justify-content-center">
        <!-- Accept Button -->
        <button type="button" class="px-3 btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#acceptModal{{ $request->id }}">
            <i class="bi bi-check-lg me-1"></i> {{ __('Accept') }}
        </button>
        
        <!-- Reject Button -->
        <button type="button" class="px-3 btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $request->id }}">
            <i class="bi bi-x-lg me-1"></i> {{ __('Reject') }}
        </button>
    </div>

    <!-- Accept Modal -->
    <div class="modal fade" id="acceptModal{{ $request->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Confirm Approval') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{ __('Are you sure you want to approve') }} <strong>{{ $request->name }}</strong>?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <form action="{{ route('request#acceptUser', $request->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success">{{ __('Yes, Approve') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div class="modal fade" id="rejectModal{{ $request->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Confirm Rejection') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{ __('Are you sure you want to reject') }} <strong>{{ $request->name }}</strong>? 
                    <p>{{ __('This action cannot be undone.') }}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <form action="{{ route('request#rejectUser', $request->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger">{{ __('Yes, Reject') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</td>
                                </tr>

                                <div class="modal fade" id="payslipModal{{ $request->id }}" tabindex="-1"
                                    aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">{{ __('Verification') }}: {{ $request->name }}
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="text-center modal-body bg-light">
                                                @if ($request->member && $request->member->payslip)
                                                    <img src="{{ asset('payslipImage/' . $request->member->payslip) }}"
                                                        class="border border-white rounded shadow-sm img-fluid"
                                                        alt="User Payslip Proof">
                                                    <div
                                                        class="flex-wrap gap-2 mt-3 d-flex justify-content-center align-items-center">
                                                        <span class="badge bg-dark fs-6">{{ __('Fee Amount') }}:
                                                            {{ $request->member->fee }}</span>
                                                        <span class="badge bg-primary fs-6">
                                                            {{ __('Method') }}:
                                                            {{ $request->member->payment->account_name ?? 'N/A' }}
                                                            ({{ $request->member->payment->account_number ?? 'N/A' }})
                                                            {{ $request->member->payment->account_type ?? 'N/A' }}
                                                        </span>
                                                        </span>
                                                    </div>
                                                @else
                                                    <div class="py-5 text-muted">
                                                        <i
                                                            class="mb-2 bi bi-file-earmark-x display-2 d-block text-warning"></i>
                                                        <p class="mb-0 small">
                                                            {{ __('No transaction slip uploaded for this record.') }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-4 text-center text-muted">
                                        {{ __('No pending registration requests found.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

{{-- @section('script-code') --}}
    {{-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> --}}

    {{-- @if (Session::has('success'))
        <script>
            Swal.fire({
                title: "{{ __('Approved!') }}",
                //text: "{{ Session::get('success') }}",
                icon: "success",
                timer: 1500,
                showConfirmButton: false
            });
        </script>
    @endif --}}

    {{-- @if (Session::has('error'))
        <script>
            Swal.fire({
                title: "{{ __('Rejected') }}",
                text: "{{ Session::get('error') }}",
                icon: "error",
                timer: 1500,
                showConfirmButton: false
            });
        </script>
    @endif

    <script>
        function acceptUser(id, name) {
            Swal.fire({
                title: "{{ __('Approve') }} " + name + "?",
                text: "{{ __('This user will be granted full access.') }}",
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: "#198754",
                cancelButtonColor: "#6c757d",
                confirmButtonText: "{{ __('Yes, approve!') }}"
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('accept-form-' + id).submit();
                }
            });
        }

        function rejectUser(id, name) {
            Swal.fire({
                title: "{{ __('Reject') }} " + name + "?",
                text: "{{ __('You won\'t be able to revert this request record!') }}",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#dc3545",
                cancelButtonColor: "#6c757d",
                confirmButtonText: "{{ __('Yes, reject it!') }}"
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('reject-form-' + id).submit();
                }
            });
        }
    </script> --}}
{{-- @endsection --}}
