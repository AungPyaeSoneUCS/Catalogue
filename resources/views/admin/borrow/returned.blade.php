@extends('admin.layout.master')

@section('content')
    <div class="py-3 shadow-lg container-fluid">
        <div class="border-0 shadow-sm card">
            <div
                class="flex-wrap gap-3 py-3 bg-white card-header border-bottom d-flex align-items-center justify-content-between">
                <h5 class="mb-0 fw-bold text-success text-nowrap">
                    <i class="bi bi-check2-square me-2"></i> {{ __('Returned Books List') }}
                </h5>

                <form action="{{ route('admin#returnedList') }}" method="GET" class="d-flex align-items-center">
                    <div class="input-group" style="width: 300px;">
                        <input type="text" name="search" class="form-control" placeholder="{{ __('Search...') }}"
                            value="{{ request('search') }}">
                        <button type="submit" class="bg-white btn btn-outline-secondary"><i
                                class="bi bi-search"></i></button>
                    </div>
                </form>
                <a href="{{ route('admin.exportReturnedList', ['search' => request('search')]) }}" class="btn btn-success" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{ __('Click export excel') }}">
                    <i class="bi bi-file-earmark-excel"></i> {{ __('Export to Excel') }}
                </a>
            </div>

            <div class="p-0 card-body table-responsive" style="max-height: 75vh; overflow-y: auto;">
                <table class="table mb-0 align-middle table-hover" style="min-width: 800px;">
                    <thead class="table-light text-nowrap">
                        <tr>
                            <th class="ps-4">{{ __('User Details') }}</th>
                            <th>{{ __('Book Details') }}</th>
                            <th>{{ __('Returned') }}</th>
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
                                                {{ $req->user->phone }}</small>
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
                                    {{ $req->returned_at ? \Carbon\Carbon::parse($req->returned_at)->format('d-M-Y') : 'N/A' }}
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-4 text-center text-muted">
                                    {{ __('No returned records found.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
