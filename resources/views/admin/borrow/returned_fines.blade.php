@extends('admin.layout.master')

@section('content')
    <div class="py-3 shadow-lg container-fluid">
        <div class="border-0 shadow-sm card">
            {{-- Header Section --}}
            <div class="py-3 bg-white card-header border-bottom">
                <div class="row align-items-center g-3">
                    {{-- Title --}}
                    <div class="col-12 col-md-4">
                        <h5 class="mb-0 fw-bold text-success text-nowrap">
                            <i class="bi bi-check-circle me-2"></i> {{ __('Returned Books & Collected Fines') }}
                        </h5>
                    </div>

                    {{-- Controls --}}
                    <div class="col-12 col-md-8">
                        <div class="flex-wrap gap-2 d-flex align-items-center justify-content-md-end">
                            {{-- Search --}}
                            <form action="{{ route('admin.returnedFines') }}" method="GET" class="flex-grow-1"
                                style="max-width: 250px;">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control"
                                        placeholder="{{ __('Search...') }}" value="{{ request('search') }}">
                                    <button type="submit" class="btn btn-outline-secondary"><i
                                            class="bi bi-search"></i></button>
                                </div>
                            </form>

                            <div class="gap-2 d-flex align-items-center">
                                {{-- Total --}}
                                <div class="px-2 py-2 text-white rounded shadow-sm bg-success text-nowrap"
                                    style="font-size: 0.85rem;">
                                    {{ __('Total') }}: <span
                                        class="fw-bold">{{ number_format($fines->sum('fine_amount')) }}{{ __('MMK') }}</span>
                                </div>

                                {{-- Export --}}
                                <form action="{{ route('admin.exportReturnedFines') }}" method="GET" class="m-0">
                                    <input type="hidden" name="search" value="{{ request('search') }}">
                                    <button type="submit" class="px-2 py-2 btn btn-success" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{ __('Click export excel') }}">
                                        <i class="bi bi-file-earmark-excel"></i>
                                        <span class=" ms-1">{{ __('Export to Excel') }}</span>
                                    </button>
                                </form>
                            </div>

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
                            <th>{{ __('Date Details') }}</th>
                            <th>{{ __('Fine Amount') }}</th>
                        </tr>
                    </thead>
                    <tbody class="text-nowrap">
                        @forelse($fines as $req)
                            <tr>
                                {{-- User Details --}}
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="d-flex align-items-center">
                                        <img src="{{ asset('userProfile/' . $req->user->profile) }}"
                                             class="border rounded-circle me-2" width="45" height="45"
                                             onerror="this.src='{{ asset('images/default-user.png') }}'">
                                        <div>
                                            <div class="fw-bold">{{ $req->user->name }}</div>
                                            <small class="text-muted">{{ $req->user->roll_number }}</small> |
                                            <small class="text-muted">{{ $req->user->year->academic_year ?? 'N/A' }}</small>
                                            <small class="text-muted d-block">{{ $req->user->phone }}</small>
                                        </div>
                                    </div>
                                    </div>
                                </td>

                                {{-- Book Details --}}
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ asset('storage/books/' . $req->book->cover_image) }}"
                                            class="rounded shadow-sm me-3" width="40" height="55"
                                            onerror="this.src='{{ asset('images/default-book.png') }}'">
                                        <div>
                                            <div class="fw-bold">{{ $req->book->title }}</div>
                                            <small class="text-primary">
                                                {{ $req->book->code }}</small>
                                        </div>
                                    </div>
                                </td>

                                {{-- Date Details --}}
                                <td>
                                    <div>
                                        {{ $req->due_at->startOfDay()->diffInDays($req->returned_at->startOfDay()) }} {{ __('Days') }}
                                    </div>
                                    
                                </td>

                                {{-- Fine Amount --}}
                                <td>
                                    <span
                                        class="badge bg-success-subtle text-success fs-6">{{ number_format($req->fine_amount) }}
                                        {{ __('MMK') }}</span>
                                </td>
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
