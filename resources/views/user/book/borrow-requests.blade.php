@extends('user.layout.master')

@section('content')
<div class="container py-3 mt-5">
    <div class="row">
        <div class="col-12 col-lg-10 offset-lg-1">
            <h4 class="mb-4 fw-bold text-dark text-nowrap">
                <i class="bi bi-journal-bookmark-fill me-2 text-info"></i>{{ __('my_booking_requests') }}
            </h4>

            @if(session('success'))
                <div class="mb-4 border-0 shadow-sm alert alert-success alert-dismissible fade show rounded-3" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="border-0 shadow-lg card rounded-3">
                <div class="p-0 card-body">
                    <div class="rounded table-responsive">
                        <table class="table mb-0 align-middle table-hover">
                            <thead class="text-muted small text-uppercase table-info text-nowrap">
                                <tr>
                                    <th class="py-3 ps-4">{{ __('book_details') }}</th>
                                    <th>{{ __('expiration_limit') }}</th>
                                    <th class="pe-4">{{ __('status') }}</th>
                                    <th class="pe-4 text-end">{{ __('action') }}</th>
                                </tr>
                            </thead>
                            <tbody class="text-nowrap">
                                @forelse($requests as $req)
                                    @php
                                        $bookingTime = \Carbon\Carbon::parse($req->booking_at ?? $req->created_at);
                                        $expireTime = $bookingTime->copy()->addHours($expireHours);
                                    @endphp
                                    <tr>
                                        <td class="py-3 ps-4 text-nowrap">
                                            <div class="d-flex align-items-center">
                                                <img src="{{ asset('storage/books/'.$req->book->cover_image) }}" 
                                                     class="rounded shadow-sm me-3" width="50" height="70" 
                                                     onerror="this.src='{{ asset('images/default-book.png') }}'">
                                                <div>
                                                    <div class="fw-bold text-dark fs-6 text-truncate" style="150px" title="{{ $req->book->title }}">{{ __('title') }}: {{ $req->book->title }}</div>
                                                    <small class="text-secondary d-block text-truncate" style="150px" title="{{ $req->book->author }}">{{ __('author') }}: {{ $req->book->author ?? 'Unknown' }}</small>
                                                    <small class="text-primary fw-bold">{{ __('code') }}: {{ $req->book->code }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($req->status == 'pending')
                                                <div class="text-muted small countdown-clock text-nowrap" 
                                                     data-expire="{{ $expireTime->toIso8601String() }}" 
                                                     data-id="{{ $req->id }}">
                                                    <i class="bi bi-clock-history me-1 text-warning"></i>
                                                    <strong class="text-danger timer-display"></strong> 
                                                </div>
                                            @else
                                                <span class="text-danger small fw-medium">
                                                    <i class="bi bi-exclamation-triangle-fill me-1"></i>{{ __('expired') }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="pe-4">
                                            @if($req->status == 'pending')
                                                <span class="px-3 py-1 border-0 badge bg-warning text-dark rounded-pill fw-medium small status-badge-{{ $req->id }}">{{ __('pending_pickup') }}</span>
                                            @elseif($req->status == 'canceled')
                                                <span class="px-3 py-1 text-white border-0 badge bg-danger rounded-pill fw-medium small">{{ __('canceled_expired') }}</span>
                                            @else
                                                <span class="px-3 py-1 text-white border-0 badge bg-secondary rounded-pill fw-medium small">{{ ucfirst($req->status) }}</span>
                                            @endif
                                        </td>
                                        <td class="pe-4 text-end">
                                            <button type="button" class="px-3 border-0 shadow-sm btn btn-sm btn-outline-danger rounded-3" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $req->id }}">
                                                <i class="bi bi-trash3-fill me-1"></i> {{ __('delete') }}
                                            </button>
                                        </td>
                                    </tr>

                                    <div class="modal fade" id="deleteModal{{ $req->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="border-0 shadow card modal-content rounded-3 text-start">
                                                <div class="py-3 text-white modal-header bg-danger">
                                                    <h5 class="modal-title fw-bold">
                                                        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ __('confirm_cancel') }}
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="p-4 modal-body">
                                                    <p class="fs-6 text-dark">{{ __('cancel_confirm_msg') }}</p>
                                                    <h5 class="my-3 text-center text-danger fw-bold">“ {{ $req->book->title }} ”</h5>
                                                    <div class="my-3 p-3 bg-light rounded-3 text-center modal-timer-{{ $req->id }}">
                                                        {{ __('calculating') }}
                                                    </div>
                                                    <p class="mb-0 text-muted small"><i class="bi bi-info-circle me-1"></i> {{ __('cancel_warning') }}</p>
                                                </div>
                                                <div class="py-3 bg-light modal-footer border-top-0 d-flex justify-content-end">
                                                    <button type="button" class="px-4 border-0 shadow-sm btn btn-light rounded-3" data-bs-dismiss="modal">{{ __('no_button') }}</button>
                                                    <form action="{{ route('user#cancelBooking', $req->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="px-4 shadow-sm btn btn-danger rounded-3 fw-bold">{{ __('yes_button') }}</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-5 text-center text-muted">
                                            <i class="mb-2 bi bi-inbox fs-2 d-block"></i>
                                            {{ __('no_bookings_found') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        function updateClocks() {
            const clocks = document.querySelectorAll('.countdown-clock');
            clocks.forEach(clock => {
                const expireTime = new Date(clock.getAttribute('data-expire')).getTime();
                const now = new Date().getTime();
                const requestId = clock.getAttribute('data-id');
                const displayElement = clock.querySelector('.timer-display');
                const modalTimerElement = document.querySelector(`.modal-timer-${requestId}`);
                const diff = expireTime - now;
                
                if (diff <= 0) {
                    clock.parentElement.innerHTML = `<span class="text-danger small fw-medium"><i class="bi bi-exclamation-triangle-fill me-1"></i>{{ __('expired') }}</span>`;
                    if(modalTimerElement) modalTimerElement.innerHTML = `<span class="text-danger small fw-medium"><i class="bi bi-exclamation-triangle-fill me-1"></i>{{ __('canceled_expired') }}</span>`;
                    const badge = document.querySelector(`.status-badge-${requestId}`);
                    if(badge) {
                        badge.className = "px-3 py-1 text-white border-0 badge bg-danger rounded-pill fw-medium small";
                        badge.textContent = "{{ __('canceled_expired') }}";
                    }
                } else {
                    const hours = Math.floor(diff / (1000 * 60 * 60));
                    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((diff % (1000 * 60)) / 1000);
                    const timeString = `${hours} {{ __('hours') }} ${minutes} {{ __('minutes') }} ${seconds} {{ __('seconds') }}`;
                    if(displayElement) displayElement.textContent = timeString;
                    if(modalTimerElement) modalTimerElement.innerHTML = `<i class="bi bi-clock-history me-1 text-warning"></i> {{ __('time_remaining') }} <strong class="text-warning">${timeString}</strong>`;
                }
            });
        }
        updateClocks();
        setInterval(updateClocks, 1000);
    });
</script>
@endsection