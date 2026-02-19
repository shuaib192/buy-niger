@extends('layouts.app')

@section('title', 'Conversation with ' . $conversation->user->name)
@section('page_title', 'Messages')

@section('sidebar')
    @include('vendor.partials.sidebar')
@endsection

@section('content')
<div class="chat-page">
    <div class="chat-wrapper">
        {{-- Chat Header --}}
        <div class="chat-header-premium">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('vendor.messages.index') }}" class="back-btn"><i class="fas fa-arrow-left"></i></a>
                <div class="chat-avatar">{{ substr($conversation->user->name, 0, 1) }}</div>
                <div>
                    <h4 class="chat-user-name">{{ $conversation->user->name }}</h4>
                    <span class="chat-subject">{{ $conversation->subject ?? 'General Inquiry' }}</span>
                </div>
            </div>
        </div>
        
        {{-- Chat Messages --}}
        <div class="chat-messages" id="chatBox">
            @if($conversation->messages->count() == 0)
                <div class="chat-empty">
                    <i class="far fa-paper-plane"></i>
                    <p>Start the conversation by typing a message below.</p>
                </div>
            @endif
            @foreach($conversation->messages as $message)
                <div class="msg-row {{ $message->sender_type == 'vendor' ? 'sent' : 'received' }}">
                    <div class="msg-bubble">
                        <div class="msg-text">{{ $message->body }}</div>
                        <div class="msg-time">{{ $message->created_at->format('g:i A') }}</div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Message Input --}}
        <div class="chat-input-bar">
            <form action="{{ route('vendor.messages.send', $conversation->id) }}" method="POST" id="messageForm" class="chat-form">
                @csrf
                <div class="input-container">
                    <input type="text" name="body" class="msg-input" placeholder="Type your reply..." required autofocus autocomplete="off">
                    <button class="send-btn" type="submit"><i class="fas fa-paper-plane"></i></button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .chat-page { animation: fadeInUp 0.3s ease; max-width: 800px; margin: 0 auto; }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }

    .chat-wrapper { background: white; border: 1px solid #f1f5f9; border-radius: 24px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.04); display: flex; flex-direction: column; height: calc(100vh - 180px); max-height: 750px; }

    /* Header */
    .chat-header-premium { display: flex; justify-content: space-between; align-items: center; padding: 16px 24px; border-bottom: 1px solid #f1f5f9; background: white; flex-shrink: 0; }
    .back-btn { width: 36px; height: 36px; border-radius: 12px; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center; color: #475569; text-decoration: none; transition: all 0.2s; font-size: 14px; }
    .back-btn:hover { background: #f8fafc; color: #0f172a; border-color: #cbd5e1; }
    .chat-avatar { width: 42px; height: 42px; border-radius: 14px; background: linear-gradient(135deg, #667eea, #764ba2); color: white; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 16px; flex-shrink: 0; }
    .chat-user-name { font-size: 15px; font-weight: 700; color: #0f172a; margin: 0; }
    .chat-subject { font-size: 12px; color: #94a3b8; font-weight: 500; }
    .gap-3 { gap: 12px; }

    /* Messages */
    .chat-messages { flex-grow: 1; overflow-y: auto; padding: 24px; background: #f8fafc; scroll-behavior: smooth; }
    .chat-messages::-webkit-scrollbar { width: 5px; }
    .chat-messages::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }

    .chat-empty { text-align: center; padding: 60px 20px; color: #94a3b8; }
    .chat-empty i { font-size: 40px; margin-bottom: 12px; display: block; }
    .chat-empty p { font-size: 14px; }

    .msg-row { display: flex; margin-bottom: 12px; }
    .msg-row.sent { justify-content: flex-end; }
    .msg-row.received { justify-content: flex-start; }

    .msg-bubble { max-width: 70%; padding: 12px 18px; position: relative; }
    .msg-row.sent .msg-bubble {
        background: linear-gradient(135deg, #0066FF 0%, #0052cc 100%);
        color: white;
        border-radius: 18px 18px 6px 18px;
        box-shadow: 0 4px 12px rgba(0,102,255,0.2);
    }
    .msg-row.received .msg-bubble {
        background: white;
        color: #1e293b;
        border-radius: 18px 18px 18px 6px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 4px rgba(0,0,0,0.04);
    }
    .msg-text { font-size: 14.5px; line-height: 1.6; word-break: break-word; }
    .msg-time { font-size: 10px; margin-top: 6px; opacity: 0.6; font-weight: 500; }
    .msg-row.sent .msg-time { text-align: right; color: rgba(255,255,255,0.7); }
    .msg-row.received .msg-time { color: #94a3b8; }

    /* Input */
    .chat-input-bar { padding: 16px 24px; border-top: 1px solid #f1f5f9; background: white; flex-shrink: 0; }
    .chat-form { width: 100%; }
    .input-container { display: flex; align-items: center; background: #f1f5f9; padding: 6px 6px 6px 20px; border-radius: 30px; border: 1px solid #e2e8f0; transition: all 0.3s; }
    .input-container:focus-within { background: white; border-color: #0066FF; box-shadow: 0 0 0 4px rgba(0,102,255,0.1); }
    .msg-input { flex-grow: 1; border: none; background: transparent; font-size: 15px; color: #0f172a; outline: none; padding: 8px 0; }
    .msg-input::placeholder { color: #94a3b8; }
    .send-btn { width: 44px; height: 44px; border-radius: 50%; background: #0066FF; color: white; border: none; display: flex; align-items: center; justify-content: center; cursor: pointer; flex-shrink: 0; transition: all 0.2s; box-shadow: 0 4px 12px rgba(0,102,255,0.25); font-size: 15px; }
    .send-btn:hover { background: #0052cc; transform: scale(1.05); box-shadow: 0 6px 18px rgba(0,102,255,0.35); }

    @media (max-width: 768px) {
        .chat-wrapper { border-radius: 16px; height: calc(100vh - 140px); }
        .msg-bubble { max-width: 85%; }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const chatBox = document.getElementById('chatBox');
        if (chatBox) chatBox.scrollTop = chatBox.scrollHeight;
    });
</script>
@endsection
