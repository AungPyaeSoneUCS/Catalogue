@extends('user.layout.master')

@section('content')
<div class="container py-5 mt-3">
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    

    <div class="row g-4">
        <div class="col-12 col-lg-5 offset-lg-1">
            <div class="p-4 border-0 shadow-lg card h-100 rounded-3 bg-dark-subtle">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="col-12 col-lg-5">
            <div class="row g-4">
                <div class="col-12">
                    <div class="p-4 border-0 shadow-lg card rounded-3 bg-dark-subtle">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>
                {{-- @if (Auth::user()->isAdmin())
                    <div class="col-12">
                    <div class="p-4 border-0 shadow-lg card rounded-3 bg-dark-subtle">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
                @endif --}}
            </div>
        </div>
        
    </div>
</div>
@endsection