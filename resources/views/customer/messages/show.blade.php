@extends('layouts.app')

@section('title', 'Conversation with ' . $conversation->vendor->store_name)
@section('page_title', 'Conversation')

@section('sidebar')
    @include('customer.partials.sidebar')
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="dashboard-card h-100">
            <div class="dashboard-card-header">
                <div style="display: flex; flex-direction: column;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div style="width: 40px; height: 40px; background: var(--primary-100); color: var(--primary-600); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 16px;">
                            {{ substr($conversation->vendor->store_name, 0, 1) }}
                        </div>
                        <div>
                            <h3 style="margin: 0; font-size: 16px;">{{ $conversation->vendor->store_name }}</h3>
                            <small style="color: var(--secondary-500); font-size: 13px;">{{ $conversation->subject }}</small>
                        </div>
                    </div>
                </div>
                <a href="{{ route('customer.messages.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
            
            <div class="dashboard-card-body p-0">
                <div class="chat-box" id="chatBox" style="height: 400px; overflow-y: auto; background-color: #f8fafc; padding: 24px;">
                    @foreach($conversation->messages as $message)
                        <div class="d-flex mb-4 {{ $message->sender_type == 'customer' ? 'justify-content-end' : '' }}">
                            <div style="max-width: 70%; min-width: 200px;">
                                <div class="{{ $message->sender_type == 'customer' ? 'bg-primary text-white' : 'bg-white border' }} p-3 rounded-3 shadow-sm" style="border-radius: 12px; {{ $message->sender_type == 'customer' ? 'border-bottom-right-radius: 4px;' : 'border-bottom-left-radius: 4px;' }}">
                                    <p class="mb-1" style="line-height: 1.5;">{{ $message->body }}</p>
                                </div>
                                <small class="{{ $message->sender_type == 'customer' ? 'text-end d-block text-muted pe-1 mt-1' : 'text-muted ps-1 mt-1' }}" style="font-size: 11px;">
                                    {{ $message->created_at->format('M d, g:i A') }}
                                </small>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="p-4 bg-white border-top">
                    <form action="{{ route('customer.messages.send', $conversation->id) }}" method="POST">
                        @csrf
                        <div style="display: flex; gap: 12px;">
                            <input type="text" name="body" class="form-control" placeholder="Type your message..." required autofocus style="border-radius: 24px; padding-left: 20px;">
                            <button class="btn btn-primary" type="submit" style="border-radius: 50%; width: 48px; height: 48px; padding: 0; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Scroll to bottom of chat
    const chatBox = document.getElementById('chatBox');
    if (chatBox) {
        chatBox.scrollTop = chatBox.scrollHeight;
    }
</script>
@endsection
