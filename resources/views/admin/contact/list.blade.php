@extends('admin.layout.master')

@section('content')
<div class="py-3 container-fluid">
    <div class="bg-white border rounded shadow-lg col-lg-6 offset-lg-3 row g-0" style="height: 80vh; overflow: hidden;">
        
        {{-- လက်ဝဲဘက် - Chat List --}}
        <div class="col-12" style="height: 100%; overflow-y: auto;">
            
            {{-- Header --}}
            <div class="p-3 border-bottom bg-light">
                <h5 class="mb-3 fw-bold text-primary"><i class="bi bi-chat-dots-fill me-2"></i> {{ __('User Messages') }}</h5>
                
                <form action="{{ route('admin.contact.list') }}" method="GET" class="d-flex">
                    <input type="text" name="search" class="form-control me-2" 
                        placeholder="{{ __('Search by name or roll number...') }}" 
                        value="{{ request('search') }}">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i></button>
                    @if(request('search'))
                        <a href="{{ route('admin.contact.list') }}" class="btn btn-secondary ms-1"><i class="bi bi-x"></i></a>
                    @endif
                </form>
            </div>

            {{-- List --}}
            <div class="list-group list-group-flush text-nowrap">
                @forelse($users as $user)
                <a href="{{ route('admin.chat.view', ['receiverId' => $user->id]) }}" 
                   class="list-group-item list-group-item-action d-flex align-items-center py-3 {{ request('receiverId') == $user->id ? 'active' : '' }}">
                    
                    {{-- Profile Image --}}
                    <img src="{{ asset('userProfile/'.$user->profile) }}" 
                         class="border rounded-circle me-3" width="50" height="50" 
                         onerror="this.src='{{ asset('image/default-user.png') }}'">
                    
                    {{-- User Details --}}
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between">
                            <h6 class="mb-0 fw-bold">{{ $user->name }} <span class="text-info">( {{ $user->roll_number }} )</span></h6>
                            @if($user->lastMessage)
                                <small class="opacity-75">{{ $user->lastMessage->created_at->tz('Asia/Yangon')->format('h:i A') }}</small>
                            @endif
                        </div>
                        
                        <p class="mb-0 small text-truncate" style="max-width: 250px;">
                            @if($user->lastMessage)
                                {{ $user->lastMessage->message }}
                            @else
                                <span class="fst-italic text-muted">{{ __('No messages yet') }}</span>
                            @endif
                        </p>
                    </div>

                    {{-- Unread Badge --}}
                    @if($user->unreadMessages->count() > 0)
                        <span class="badge bg-danger rounded-pill ms-2">
                            {{ $user->unreadMessages->count() }}
                        </span>
                    @endif
                </a>
                @empty
                    <div class="p-5 text-center text-muted">
                        <i class="bi bi-person-x display-4"></i>
                        <p>{{ __('No users found.') }}</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection