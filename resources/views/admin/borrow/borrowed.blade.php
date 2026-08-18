@extends('admin.layout.master')

@section('content')
    <div class="py-3 container-fluid">
        @if(session('successLostFine'))
    <div class="shadow-sm alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>
        {{ session('successLostFine') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if(session('successDamageFine'))
    <div class="shadow-sm alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>
        {{ session('successDamageFine') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
        <div class="border-0 shadow-sm card">
            <div
                class="flex-wrap gap-3 py-3 bg-white card-header border-bottom d-flex align-items-center justify-content-between">
                <h5 class="mb-0 fw-bold text-primary ">
                    <i class="bi bi-arrow-repeat me-2"></i> {{ __('Borrowed List (Active)') }}
                </h5>
                <span class=" badge bg-info fw-bold">{{ __('Books Total') }} {{ $totalBorrow }} {{ __('Books') }}</span>
                <form action="{{ route('admin#borrowList') }}" method="GET"
                    class="flex-wrap gap-2 d-flex align-items-center">
                    <div class="input-group" style="width: 300px;">
                        <input type="text" name="search" class="form-control" placeholder="{{ __('Search...') }}"
                            value="{{ request('search') }}">
                        <button type="submit" class="bg-white btn btn-outline-secondary" title="{{ __('Search') }}">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>

                    <a href="{{ route('admin.exportBorrowedList', ['search' => request('search')]) }}"
                        class="btn btn-success text-nowrap" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{ __('Click export excel') }}">
                        <i class="bi bi-file-earmark-excel"></i> {{ __('Export to Excel') }}
                    </a>
                </form>
            </div>

            <div class="p-0 card-body table-responsive" style="max-height: 75vh; overflow-y: auto;">
                <table class="table mb-0 align-middle table-hover" style="min-width: 800px;">
                    <thead class="table-light text-nowrap">
                        <tr>
                            <th class="ps-4">{{ __('User Details') }}</th>
                            <th>{{ __('Book Details') }}</th>
                            <th>{{ __('Due Date') }}</th>
                            <th class="text-end pe-4">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody class="text-nowrap">
                        @forelse($requests as $req)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <img src="{{ asset('userProfile/' . $req->user->profile) }}"
                                            class="border rounded-circle me-2" width="45" height="45"
                                            onerror="this.src='{{ asset('images/default-user.png') }}'">
                                        <div>
                                            <div class="fw-bold">{{ $req->user->name }}</div>
                                            <small class="text-muted">
                                                {{ $req->user->roll_number }}</small> |
                                            <small class="text-muted">
                                                {{ $req->user->year->academic_year ?? 'N/A' }}</small>
                                            <small class="text-muted d-block">
                                                {{ $req->user->phone }}|{{ $req->user->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ asset('storage/books/' . $req->book->cover_image) }}"
                                            class="rounded shadow-sm me-3" width="40" height="55"
                                            onerror="this.src='{{ asset('images/default-book.png') }}'">
                                        <div>
                                            <div class="fw-bold">{{ $req->book->title }}</div>
                                            <small class="text-primary fw-bold">
                                                {{ $req->book->code }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span
                                        class="badge bg-danger-subtle text-danger">{{ \Carbon\Carbon::parse($req->due_at)->format('d-M-Y') }}</span>
                                </td>
                                {{-- Replace the Action column td with this --}}
                                <td class="text-end pe-4">
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                        data-bs-target="#returnModal{{ $req->id }}">
                                        <i class="bi bi-check-circle"></i> {{ __('Return') }}
                                    </button>
                                    {{-- Lost Button --}}
                                    <button type="button" class="btn btn-sm btn-outline-danger ms-1" data-bs-toggle="modal"
                                        data-bs-target="#lostModal{{ $req->id }}">
                                        <i class="bi bi-x-circle"></i> {{ __('Lost') }}
                                    </button>
                                    {{-- Damage Button --}}
                                    <button type="button" class="btn btn-sm btn-outline-warning ms-1" data-bs-toggle="modal"
                                        data-bs-target="#damageModal{{ $req->id }}">
                                        <i class="bi bi-x-circle"></i> {{ __('Damage') }}
                                    </button>
                                </td>
                                {{-- Lost Modal --}}
                                <div class="modal fade" id="lostModal{{ $req->id }}" tabindex="-1"
                                    aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('admin#lostBook', $req->id) }}" method="POST">
                                                @csrf
                                                <div class="modal-header">
                                                    <h5 class="modal-title">{{ __('Mark as Lost') }}</h5>
                                                </div>
                                                <div class="modal-body text-start">
                                                    <p>{{ __('Confirm book lost:') }}
                                                        <strong>{{ $req->book->title }}</strong>
                                                    </p>
                                                    <div class="mb-3">
                                                        <label
                                                            class="form-label">{{ __('Enter Lost Fine Amount') }}</label>
                                                        <input type="numeric" name="lost_fine" placeholder="{{ __('Enter fine amount') }}"
                                                            class="form-control @error('lost_fine') is-invalid @enderror"
                                                             min="1">

                                                        @error('lost_fine')
                                                            <div class="text-danger small">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                                                    <button type="submit"
                                                        class="btn btn-danger">{{ __('Confirm Lost') }}</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                {{-- Damage Modal --}}
                                <div class="modal fade" id="damageModal{{ $req->id }}" tabindex="-1"
                                    aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('admin#damageBook', $req->id) }}" method="POST">
                                                @csrf
                                                <div class="modal-header">
                                                    <h5 class="modal-title">{{ __('Mark as Damage') }}</h5>
                                                </div>
                                                <div class="modal-body text-start">
                                                    <p>{{ __('Confirm book damage:') }}
                                                        <strong>{{ $req->book->title }}</strong>
                                                    </p>
                                                    <div class="mb-3">
                                                        <label
                                                            class="form-label">{{ __('Enter Damage Fine Amount') }}</label>
                                                        <input type="numeric" name="damage_fine" placeholder="{{ __('Enter damage fine amount') }}"
                                                            class="form-control @error('damage_fine') is-invalid @enderror"
                                                             min="1">

                                                        @error('damage_fine')
                                                            <div class="text-danger small">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                                                    <button type="submit"
                                                        class="btn btn-danger">{{ __('Confirm Damage') }}</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                {{-- Add this Modal after the </tr> row inside the @forelse loop --}}
                                <div class="modal fade" id="returnModal{{ $req->id }}" tabindex="-1"
                                    aria-labelledby="returnModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="returnModalLabel">
                                                    {{ __('Return Confirmation') }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body text-start">
                                                {{ __('Are you sure you want to return') }}
                                                <strong>{{ $req->book->title }}</strong>?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                                                <form action="{{ route('admin#receiveBook', $req->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit"
                                                        class="btn btn-primary">{{ __('Confirm Return') }}</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-4 text-center text-muted">{{ __('No records found.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
<script>
    document.addEventListener("DOMContentLoaded", function() {
        @if($errors->has('lost_fine'))
            // သင့်ရဲ့ Modal ID ကို သုံးပြီး ပြန်ဖွင့်ခိုင်းပါ
            var myModal = new bootstrap.Modal(document.getElementById('lostModal{{ $req->id }}'));
            myModal.show();
        @endif
    });
    document.addEventListener("DOMContentLoaded", function() {
        @if($errors->has('damage_fine'))
            // သင့်ရဲ့ Modal ID ကို သုံးပြီး ပြန်ဖွင့်ခိုင်းပါ
            var myModal = new bootstrap.Modal(document.getElementById('damageModal{{ $req->id }}'));
            myModal.show();
        @endif
    });
</script>