@extends('admin.layout.master')

@section('content')
    <div class="py-4 container-fluid">

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
        <div class="mb-4 border-0 shadow-sm card">
            <div class="card-body">
                <div class="row g-3 align-items-center justify-content-between">

                    <!-- Search Form Grid Box -->
                    <div class="col-12 col-md-6 col-lg-6">
                        <form action="{{ route('list#paymentDetails') }}" method="GET">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control"
                                    placeholder="{{ __('Search accounts, type or numbers...') }}"
                                    value="{{ request('search') }}">
                                <button class="btn btn-outline-primary" type="submit">
                                    <i class="bi bi-search me-1"></i> {{ __('Search') }}
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Creation Open Control Dialog Button -->
                    <div class="col-12 col-md-auto">
                        <span data-bs-toggle="tooltip"
                    data-bs-placement="top" data-bs-title="{{ __('Click create payment') }}">
                            <button type="button" class="px-4 btn btn-primary w-100" data-bs-toggle="modal"
                            data-bs-target="#createPaymentModal">
                            <i class="bi bi-plus-circle me-2"></i>{{ __('Create Payment') }}
                        </button>
                        </span>
                    </div>

                </div>
            </div>
        </div>

        <!-- Central Presentation Data Card -->
        <div class="border-0 shadow-sm card">
            <div class="py-3 bg-white border-0 card-header">
                <h5 class="mb-0 text-secondary fw-bold">{{ __('Payment Management') }}</h5>
            </div>
            <div class="p-0 card-body">
                <div class="table-responsive">
                    <table class="table mb-0 align-middle table-hover text-nowrap">
                        <thead class="table-light text-muted">
                            <tr>
                                <th class="ps-4" style="width: 5%;">{{ __('ID') }}</th>
                                <th style="width: 25%;">{{ __('Account Name') }}</th>
                                <th style="width: 20%;">{{ __('Account Number') }}</th>
                                <th style="width: 15%;">{{ __('Type') }}</th>
                                <th style="width: 15%;">{{ __('Fee') }}</th>
                                <th style="width: 15%;">{{ __('Created At') }}</th>
                                <th class="text-end pe-4" style="width: 20%;">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payments as $key => $payment)
                                <tr>
                                    <td class="ps-4 fw-medium">{{ $key + 1 }}</td>
                                    <td class="fw-semibold text-dark">{{ $payment->account_name }}</td>
                                    <td><code class="text-secondary">{{ $payment->account_number }}</code></td>
                                    <td>
                                        <span
                                            class="badge bg-light text-primary border border-primary-subtle px-2.5 py-1.5 rounded">
                                            {{ $payment->account_type }}
                                        </span>
                                    </td>
                                    <td class="fw-bold text-success">{{ number_format($payment->fee) }}
                                        {{ __('MMK') }}</td>
                                    <td class="text-muted small">
                                        {{ $payment->created_at?->tz('Asia/Yangon')->format('d-M-Y') ?? 'N/A' }}
                                    </td>
                                    <td class="text-end pe-4">
                                        <button class="btn btn-sm btn-outline-secondary me-1" data-bs-toggle="modal"
                                            data-bs-target="#editPaymentModal{{ $payment->id }}">
                                            <i class="bi bi-pencil-square me-1"></i>{{ __('Edit') }}
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                                            data-bs-target="#deletePaymentModal{{ $payment->id }}">
                                            <i class="bi bi-trash"></i> {{ __('Delete') }}
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-4 text-center text-muted">
                                        {{ __('No payment configurations found matching your filters.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- CREATE PAYMENT MODAL -->
    <div class="modal fade" id="createPaymentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="border-0 shadow modal-content">
                <div class="text-white modal-header bg-primary">
                    <h5 class="modal-title"><i class="bi bi-credit-card me-2"></i>{{ __('Add New Payment Method') }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form action="{{ route('store#paymentDetails') }}" method="POST">
                    @csrf
                    <div class="p-4 modal-body">
                        {{-- Account Name --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary">{{ __('Account Name') }}</label>
                            <input type="text" class="form-control @error('account_name') is-invalid @enderror"
                                name="account_name" placeholder="e.g., U Aye" value="{{ old('account_name') }}">
                            @error('account_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Account Number --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary">{{ __('Account Number') }}</label>
                            <input type="text" class="form-control @error('account_number') is-invalid @enderror"
                                name="account_number" placeholder="e.g., 09123456789"
                                value="{{ old('account_number') }}">
                            @error('account_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Account Type --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary">{{ __('Account Type') }}</label>
                            <input type="text" class="form-control @error('account_type') is-invalid @enderror"
                                name="account_type" placeholder="e.g., KPay, WaveMoney"
                                value="{{ old('account_type') }}">
                            @error('account_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Transaction Fee --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary">{{ __('Transaction Fee') }} ({{ __('MMK') }})</label>
                            <input type="number" class="form-control @error('fee') is-invalid @enderror" name="fee"
                                placeholder="e.g., 0" min="1" value="{{ old('fee') }}">
                            @error('fee')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="px-4 modal-footer bg-light border-top-0">
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="px-4 btn btn-primary">{{ __('Save Method') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- DYNAMIC LOOPS FOR EDIT & CONFIRMATION MODALS -->
    @foreach ($payments as $payment)
        <!-- EDIT / UPDATE MODAL -->
        <div class="modal fade" id="editPaymentModal{{ $payment->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="border-0 shadow modal-content">
                    <div class="text-white modal-header bg-secondary">
                        <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>{{ __('Update Payment Method') }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <form action="{{ route('update#paymentDetails', $payment->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="p-4 modal-body">
                            {{-- Account Name --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-secondary">{{ __('Account Name') }}</label>
                                <input type="text" class="form-control @error('account_name') is-invalid @enderror"
                                    name="account_name" value="{{ old('account_name' , $payment->account_name ) }}">
                                @error('account_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Account Number --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-secondary">{{ __('Account Number') }}</label>
                                <input type="text" class="form-control @error('account_number') is-invalid @enderror"
                                    name="account_number" value="{{ old('account_number', $payment->account_number) }}">
                                @error('account_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Account Type --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-secondary">{{ __('Account Type') }}</label>
                                <input type="text" class="form-control @error('account_type') is-invalid @enderror"
                                    name="account_type" value="{{ old('account_type' , $payment->account_type) }}">
                                @error('account_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Transaction Fee --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-secondary">{{ __('Transaction Fee') }}
                                    ({{ __('MMK') }})
                                </label>
                                <input type="number" class="form-control @error('fee') is-invalid @enderror"
                                    name="fee" value="{{ old('fee', $payment->fee) }}" min="0">
                                @error('fee')
                                    <div class="invalid-feedback">{{ $message }}</div>
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

        <!-- RE-VALIDATION ABSOLUTE DELETE CONFIRM MODAL -->
        <div class="modal fade" id="deletePaymentModal{{ $payment->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="border-0 shadow modal-content">
                    <div class="p-4 text-center modal-body">
                        <div class="mb-3 text-danger fs-1"><i class="bi bi-exclamation-circle"></i></div>
                        <h5 class="mb-2 fw-bold text-secondary">{{ __('Are you sure?') }}</h5>
                        <p class="mb-0 text-muted small">
                            {{ __('This configuration deletes this payment configuration asset permanently.') }}</p>
                    </div>
                    <div class="pb-4 bg-white border-0 justify-content-center modal-footer">
                        <button type="button" class="px-3 btn btn-sm btn-secondary"
                            data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <form action="{{ route('delete#paymentDetails', $payment->id) }}" method="POST"
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
@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        @if ($errors->any())
            var modalType = "{{ session('modal_type') }}";
            var paymentId = "{{ session('payment_id') }}";

            if (modalType === 'create') {
                var createModalEl = document.getElementById('createPaymentModal');
                if (createModalEl) {
                    var myModal = new bootstrap.Modal(createModalEl);
                    myModal.show();
                }
            } else if (modalType === 'edit' && paymentId) {
                var editModalEl = document.getElementById('editPaymentModal' + paymentId);
                if (editModalEl) {
                    var editModal = new bootstrap.Modal(editModalEl);
                    editModal.show();
                }
            }
        @endif
    });
</script>
@endpush