@extends('admin.layout.master')

@section('content')
    <div class="py-3 container-fluid">
        <div class="border-0 shadow-sm card">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            {{-- @if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Error:</strong> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif --}}
            <div class="card-header bg-warning-subtle text-dark fw-bold ">
                <div class="row">
                    <div class="col-12 col-md-6 col-lg-6">
                        <span><i class="bi bi-journal-text me-2"></i> {{ __('Booking Request List') }}</span>
                    </div>
                    <div class="col-12 col-md-6 col-lg-6">
                        <form action="{{ route('admin#bookingList') }}" method="GET">
                            <div class="input-group input-group-sm">
                                <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                                    placeholder="{{ __('Search user or book...') }}">

                                <button type="submit" class="btn btn-outline-secondary">
                                    <i class="bi bi-search"></i>
                                </button>

                                
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="p-0 card-body table-responsive" style="max-height: 75vh; overflow-y: auto;">
                <table class="table mb-0 align-middle table-hover" id="bookingTable" style="min-width: 800px;">
                    <thead class="table-light text-nowrap">
                        <tr>
                            <th class="ps-4">{{ __('User Details') }}</th>
                            <th>{{ __('Book Details') }}</th>
                            <th>{{ __('Expiration') }}</th>
                            <th class="text-end pe-4">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody class="text-nowrap">
                        @forelse($requests as $req)
                            <tr class="search-row">
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <img src="{{ asset('userProfile/' . $req->user->profile) }}"
                                            class="border rounded-circle me-2" width="45" height="45" alt="User"
                                            onerror="this.src='{{ asset('images/default-user.png') }}'">
                                        <div>
                                            <div class="fw-bold username">{{ $req->user->name }}</div>
                                            <small class="text-muted d-block">{{ $req->user->email }}</small>
                                            <small class="text-dark roll_number">{{ $req->user->roll_number }}</small>
                                            <small class="text-dark academic_year">|
                                                {{ $req->user->year->academic_year ?? 'N/A' }}</small>
                                            <small class="text-muted d-block">{{ $req->user->phone }}</small>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ asset('storage/books/' . $req->book->cover_image) }}"
                                            class="rounded shadow-sm me-3" width="50" height="65" alt="Book"
                                            onerror="this.src='{{ asset('images/default-book.png') }}'">
                                        <div>
                                            <div class="fw-bold booktitle">{{ $req->book->title }}</div>
                                            <small class="text-primary fw-bold bookcode">{{ $req->book->code }}</small>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <div class="countdown fw-bold text-danger"
                                        data-expiry="{{ \Carbon\Carbon::parse($req->booking_at)->addHours(24)->setTimezone('Asia/Yangon')->toIso8601String() }}">
                                        {{ __('Calculating...') }}
                                    </div>
                                </td>

                                {{-- <td class="text-end pe-4">
                                    <button type="button" class="px-3 btn btn-success btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#issueModal{{ $req->id }}">
                                        <i class="bi bi-check2-square me-1"></i> {{ __('Issue Book') }}
                                    </button>
                                </td> --}}
                                <td class="text-end pe-4">
    @if ($req->status == 'canceled')
        {{-- Status cancelled ဖြစ်ရင် Delete Button ပြမယ် --}}
        <button type="button" class="px-3 btn btn-danger btn-sm" data-bs-toggle="modal"
            data-bs-target="#deleteModal{{ $req->id }}">
            <i class="bi bi-trash me-1"></i> {{ __('Delete') }}
        </button>
    @else
        {{-- တခြား Status တွေဆိုရင် Issue Button ပြမယ် --}}
        <button type="button" class="px-3 btn btn-success btn-sm" data-bs-toggle="modal"
            data-bs-target="#issueModal{{ $req->id }}">
            <i class="bi bi-check2-square me-1"></i> {{ __('Issue Book') }}
        </button>
    @endif
</td>
                            </tr>
                            <div class="modal fade" id="issueModal{{ $req->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">{{ __('Issue Confirmation') }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body text-start">
                                            {{ __('Are you sure you want to issue') }}
                                            <strong>{{ $req->book->title }}</strong> {{ __('to') }}
                                            <strong>{{ $req->user->name }}</strong>?
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                                            <form action="{{ route('admin#acceptBooking', $req->id) }}" method="POST">
                                                @csrf
                                                <button type="submit"
                                                    class="btn btn-success">{{ __('Confirm Issue') }}</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- Delete Modal --}}
<div class="modal fade" id="deleteModal{{ $req->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Delete Confirmation') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-start">
                {{ __('Are you sure you want to delete this cancelled booking request?') }}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <form action="{{ route('admin#deleteBooking', $req->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">{{ __('Confirm Delete') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
                        @empty
                            <tr>
                                <td colspan="4" class="py-4 text-center text-muted">
                                    {{ __('No pending bookings available.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Countdown Timer
        function updateCountdowns() {
            document.querySelectorAll('.countdown').forEach(el => {
                const expiry = new Date(el.dataset.expiry).getTime();
                const now = new Date().getTime();
                const diff = expiry - now;

                if (diff <= 0) {
                    el.innerHTML = '<span class="text-secondary">{{ __('Expired') }}</span>';
                } else {
                    const hours = Math.floor(diff / (1000 * 60 * 60));
                    const mins = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                    const secs = Math.floor((diff % (1000 * 60)) / 1000);
                    el.innerHTML = `${hours}h ${mins}m ${secs}s`;
                }
            });
        }
        setInterval(updateCountdowns, 1000);
        updateCountdowns();
    </script>
@endsection
