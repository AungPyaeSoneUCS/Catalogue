@extends('admin.layout.master')
@section('content')
<div class="shadow-lg container-fluid">
    <div class="mb-4 row align-items-center g-2">
        <div class="col-12 col-md-5">
            <form action="{{ route('list#memberFees') }}" method="GET">
                <div class="input-group">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search') }}" class="form-control">
                    <button class="btn btn-primary" type="submit">{{ __('search_btn') }}</button>
                </div>
            </form>
        </div>

        <div class="col-12 col-md-2">
            <form action="{{ route('admin.exportMembers') }}" method="GET">
                <input type="hidden" name="search" value="{{ request('search') }}">
                <button type="submit" class="w-100 btn btn-success" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{ __('Click export excel') }}">
                    <i class="fas fa-file-excel"></i> {{__('Export to Excel')}}
                </button>
            </form>
        </div>

        <div class="gap-2 col-12 col-md-5 d-flex justify-content-md-end">
            <div class="p-2 px-3 text-white rounded shadow-sm bg-info">
                {{ __('user_count') }}: <span class="fw-bold">{{ $userCount }}</span>
            </div>
            <div class="p-2 px-3 text-white rounded shadow-sm bg-success">
                {{ __('total_fees') }}: <span class="fw-bold">{{ number_format($totalFees) }} MMK</span>
            </div>
        </div>
    </div>

    <div class="p-3 bg-white rounded shadow-lg table-responsive" style="max-height: 75vh; overflow-y: auto;">
        <table class="table align-middle table-hover" style="min-width: 1200px;">
            <thead class="table-light">
                <tr>
                    <th class="text-nowrap">{{ __('date') }}</th>
                    <th class="text-nowrap">{{ __('name') }}</th>
                    <th class="text-nowrap">{{ __('Roll Number') }}</th>
                    <th class="text-nowrap">{{ __('Academic Year') }}</th>
                    <th class="text-nowrap">{{ __('fee') }}</th>
                    <th class="text-nowrap">{{ __('Payment Method') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($approvedMembers as $member)
                <tr>
                    <td class="text-nowrap">{{ $member->created_at->tz('Asia/Yangon')->format('d-m-Y') }}</td>
                    <td class="text-nowrap">{{ $member->user->name ?? 'N/A' }}</td>
                    <td class="text-nowrap">{{ $member->user->roll_number ?? 'N/A' }}</td>
                    <td class="text-nowrap">{{ $member->user->year->academic_year ?? 'N/A' }}</td>
                    <td class="text-nowrap fw-bold">{{ number_format($member->fee) }} {{__('MMK')}}</td>
                    <td class="text-nowrap">
                @if($member->payment)
                    {{ $member->payment->account_name }} ({{ $member->payment->account_number }}){{ $member->payment->account_type }}
                @else
                    <span class="text-muted">N/A</span>
                @endif
            </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection