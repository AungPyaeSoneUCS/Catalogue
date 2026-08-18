@extends('admin.layout.master')

@section('content')
<div class="container py-5 mt-3">
    {{-- Back Button (Admin သို့မဟုတ် Superadmin ဖြစ်မှ ပြမည်) --}}
    @if (auth()->check() && (auth()->user()->role == 'admin' || auth()->user()->role == 'superadmin'))
        <a href="{{ route('admin.contact.list') }}" class="mb-3 btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> {{ __('Back to List') }}
        </a>
    @endif

    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6">
            <div class="border-0 shadow-lg card" style="height: 70vh; display: flex; flex-direction: column;">
                
                {{-- Header Section --}}
                <div class="py-2 bg-white card-header d-flex align-items-center border-bottom">
                    <img src="{{ asset('userProfile/'.$receiver->profile) }}" 
                         class="border rounded-circle me-3" 
                         width="45" height="45" 
                         style="object-fit: cover;"
                         onerror="this.src='{{ asset('images/default-user.png') }}'">
                    <div>
                        
                        <h6 class="mb-0 fw-bold">{{ $receiver->name }}</h6>
                        @if (auth()->check() && (auth()->user()->role == 'admin' || auth()->user()->role == 'superadmin'))
                            <small class="text-success">( {{ $receiver->roll_number }} )</small>
                        @endif
                    </div>
                </div>
                
                {{-- Chat Body (Scrollable)
                <div class="p-4 card-body bg-light flex-grow-1" id="chat-body" style="overflow-y: auto;">
                    @foreach($messages as $msg)
                        <div class="d-flex mb-3 {{ $msg->sender_id == auth()->id() ? 'justify-content-end' : 'justify-content-start' }}">
                            <div class="p-3 rounded shadow-sm {{ $msg->sender_id == auth()->id() ? 'bg-primary text-white' : 'bg-white' }}" style="max-width: 80%;">
                                <div style="word-wrap: break-word;">{{ $msg->message }}</div>
                                <div class="mt-1 opacity-75 small" style="font-size: 0.7rem;">
                                    {{ $msg->created_at->tz('Asia/Yangon')->format('d M Y h:i A') }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div> --}}
                {{-- Chat Body (Scrollable) --}}
<div class="p-4 card-body bg-light flex-grow-1" id="chat-body" style="overflow-y: auto;">
    @foreach($messages as $msg)
        {{-- ပို့တဲ့သူက admin သို့မဟုတ် superadmin ဟုတ်မဟုတ် စစ်ဆေးခြင်း --}}
        @php
            $senderUser = \App\Models\User::find($msg->sender_id);
            $isAdminSender = $senderUser && in_array($senderUser->role, ['admin', 'superadmin']);
        @endphp

        <div class="d-flex mb-3 {{ $isAdminSender ? 'justify-content-end' : 'justify-content-start' }}">
            <div class="p-3 rounded shadow-sm {{ $isAdminSender ? 'bg-primary text-white' : 'bg-white' }}" style="max-width: 80%;">
                <div style="word-wrap: break-word;">{{ $msg->message }}</div>
                <div class="mt-1 opacity-75 small" style="font-size: 0.7rem;">
                    {{ $msg->created_at->tz('Asia/Yangon')->format('d M Y h:i A') }}
                </div>
            </div>
        </div>
    @endforeach
</div>

                {{-- Footer Form --}}
                <div class="p-3 bg-white card-footer border-top">
                    <form action="{{ (auth()->check() && (auth()->user()->role == 'admin' || auth()->user()->role == 'superadmin')) ? route('admin.chat.store') : route('user.chat.store') }}" method="POST" class="d-flex">
                        @csrf
                        <input type="hidden" name="receiver_id" value="{{ $id }}">
                        <input type="text" name="message" class="form-control me-2" placeholder="{{ __('Write a message...') }}" required autocomplete="off">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Script for Auto Scroll --}}
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var chatBody = document.getElementById('chat-body');
        chatBody.scrollTop = chatBody.scrollHeight;
    });
</script>
@endsection