@extends('admin.layout.master')

@section('content')
    <div class="py-3 shadow-lg container-fluid">
        <div class="border-0 shadow-sm card">
            {{-- Header Section --}}
            <div class="py-3 bg-white card-header border-bottom">
                <div class="row align-items-center g-3">
                    <div class="col-12 col-md-4">
                        <h5 class="mb-0 fw-bold text-danger">
                            <i class="bi bi-x-circle me-2"></i> {{ __('Lost Books & Penalties') }}
                        </h5>
                    </div>

                    <div class="col-12 col-md-8">
                        <div class="flex-wrap gap-2 d-flex align-items-center justify-content-md-end">


                            {{-- Total Lost Fines --}}
                            <div class="px-2 py-2 text-white rounded shadow-sm bg-danger text-nowrap"
                                style="font-size: 0.85rem;">
                                {{ __('Total Lost Fine') }}: <span
                                    class="fw-bold">{{ number_format($lostBooks->sum('lost_fine')) }}
                                    {{ __('MMK') }}</span>
                            </div>
                            <form action="{{ route('admin.exportLostBooks') }}" method="GET" class="m-0">
                                <input type="hidden" name="search" value="{{ request('search') }}">
                                <button type="submit" class="btn btn-success" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{ __('Click export excel') }}">
                                    <i class="bi bi-file-earmark-excel"></i> {{ __('Export to Excel') }}
                                </button>
                            </form>
                            {{-- Search --}}
                            <form action="{{ route('admin#lostBooksList') }}" method="GET" class="flex-grow-1"
                                style="max-width: 250px;">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control"
                                        placeholder="{{ __('Search...') }}" value="{{ request('search') }}">
                                    <button type="submit" class="btn btn-outline-secondary"><i
                                            class="bi bi-search"></i></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-0 card-body table-responsive" style="max-height: 75vh; overflow-y: auto;">
                <table class="table mb-0 align-middle table-hover" style="min-width: 800px;">
                    <thead class="table-light text-nowrap">
                        <tr>
                            <th class="ps-4">{{ __('User Details') }}</th>
                            <th>{{ __('Book Details') }}</th>
                            <th>{{ __('lost date') }}</th>
                            <th>{{ __('Lost Fine Amount') }}</th>
                        </tr>
                    </thead>
                    <tbody class="text-nowrap">
                        @forelse($lostBooks as $req)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <img src="{{ asset('userProfile/' . $req->user->profile) }}"
                                            class="border rounded-circle me-2" width="45" height="45"
                                            onerror="this.src='{{ asset('images/default-user.png') }}'">
                                        <div>
                                            <div class="fw-bold">{{ $req->user->name }}</div>
                                            <small class="text-muted">{{ $req->user->roll_number }}</small> |
                                            <small
                                                class="text-muted">{{ $req->user->year->academic_year ?? 'N/A' }}</small>
                                            <div>{{ $req->user->phone }} | {{ $req->user->email }}</div>
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
                                            <small class="text-primary">{{ $req->book->code }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span
                                        class="text-white badge bg-danger">{{ $req->returned_at->format('d-M-Y') }}</span>
                                </td>
                                <td>
                                    <span
                                        class="badge bg-danger-subtle text-danger fs-6">{{ number_format($req->lost_fine) }}
                                        {{ __('MMK') }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-4 text-center text-muted">{{ __('No lost records found.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
