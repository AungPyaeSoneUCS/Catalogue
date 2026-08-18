@extends('user.layout.master')

@section('content')
<div class="container py-3 mt-5" style="top: 100px">
    <div class="mb-4 d-flex align-items-center justify-content-between">
        <h4 class="mb-0 fw-bold text-dark">
            <i class="bi bi-journal-check text-success me-2"></i> {{ __('my_borrowed_books') }}
        </h4>
        <span class="px-3 py-2 badge bg-success rounded-pill">{{ __('active_borrows_fines') }}</span>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="border-0 shadow-sm card rounded-3">
        <div class="p-0 card-body">
            <div class="table-responsive">
                <table class="table mb-0 align-middle table-hover">
                    <thead class="bg-light text-muted small text-uppercase text-nowrap">
                        <tr>
                            <th class="ps-4">{{ __('book_info') }}</th>
                            <th>{{ __('borrowed') }}</th>
                            <th>{{ __('due_date') }}</th>
                            <th>{{ __('fine') }}</th>
                            <th>{{ __('status') }}</th>
                            <th class="pe-4 text-end">{{ __('action') }}</th>
                        </tr>
                    </thead>
                    <tbody class="text-nowrap">
                        @forelse($borrows as $borrow)
                            <tr class="{{ $borrow->status == 'overdue' ? 'table-danger-subtle' : '' }}">
                                <td class="py-3 ps-4">
                                    <div class="d-flex align-items-center">
                                        <img src="{{ asset('storage/books/'.$borrow->book->cover_image) }}" 
                                             class="rounded shadow-sm me-3" width="50" height="70" 
                                             onerror="this.src='{{ asset('images/default-book.png') }}'">
                                        <div>
                                            <h6 class="mb-0 fw-bold text-dark text-truncate" style="width: 150px" title="{{ $borrow->book->title }}">{{ $borrow->book->title }}</h6>
                                            <small class="text-muted d-block text-truncate" style="width: 150px" title="{{ $borrow->book->author}}">{{ __('author') }}: {{ $borrow->book->author ?? 'N/A' }}</small>
                                            <small class="text-primary fw-bold">{{ __('code') }}: {{ $borrow->book->code }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($borrow->borrowed_at)->format('d M, Y') }}</td>
                                <td>
                                    <span class="fw-semibold {{ $borrow->status == 'overdue' ? 'text-danger' : 'text-dark' }}">
                                        {{ \Carbon\Carbon::parse($borrow->due_at)->format('d M, Y') }}
                                    </span>
                                </td>
                                <td>
                                    @if($borrow->status == 'overdue')
                                        <span class="text-danger fw-bold">{{ $borrow->auto_fine ?? 0 }} {{__('MMK')}}</span>
                                    @else
                                        <span class="text-success small">{{ __('None') }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($borrow->status == 'borrowed')
                                        <span class="border badge bg-info-subtle text-info border-info">{{ __('reading') }}</span>
                                    @else
                                        <span class="text-white badge bg-danger animate__animated animate__flash animate__infinite">{{ __('overdue') }}</span>
                                    @endif
                                </td>
                                <td class="pe-4 text-end">
                                    @if($borrow->status == 'borrowed')
                                        <button type="button" 
                                                class="btn btn-outline-primary btn-sm rounded-2" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#extendModal{{ $borrow->id }}">
                                            <i class="bi bi-arrow-repeat me-1"></i> {{ __('extend') }}
                                        </button>
                                    @else
                                        <button class="btn btn-secondary btn-sm rounded-2" disabled>
                                            <i class="bi bi-lock-fill me-1"></i> {{ __('pay_at_counter') }}
                                        </button>
                                    @endif
                                </td>
                            </tr>

                            <div class="modal fade" id="extendModal{{ $borrow->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="border-0 modal-header">
                                            <h5 class="modal-title fw-bold">{{ __('confirm_extend') }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body text-start">
                                            <p>{{ __('book_title_label') }} - <strong>{{ $borrow->book->title }}</strong></p>
                                            <p class="text-muted">
                                                {{ __('extend_confirm_msg') }} 
                                                <span class="text-primary fw-bold">{{ $extendDays }} {{ __('days') }}</span>
                                            </p>                                       
                                        </div>
                                        <div class="border-0 modal-footer">
                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('cancel') }}</button>
                                            <form action="{{ route('user#extendBorrow', $borrow->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-primary">{{ __('confirm_extend_btn') }}</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="6" class="py-5 text-center text-muted">
                                    <i class="mb-2 bi bi-journal-x fs-1 d-block text-secondary"></i>
                                    {{ __('no_borrows_found') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection