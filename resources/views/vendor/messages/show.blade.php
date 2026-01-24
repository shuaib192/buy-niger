@section('content')
<div class="chat-container py-4">
    <div class="dashboard-card border-0 shadow-sm overflow-hidden d-flex flex-column" style="height: calc(100vh - 180px); max-height: 800px;">
        <!-- Chat Header -->
        <div class="chat-header p-4 bg-white border-bottom d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <a href="{{ route('vendor.messages.index') }}" class="btn btn-icon btn-light rounded-circle mr-3">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div class="avatar-circle small mr-3">
                    {{ substr($conversation->user->name, 0, 1) }}
                </div>
                <div>
                    <h4 class="h6 font-bold text-secondary-900 mb-0">{{ $conversation->user->name }}</h4>
                    <small class="text-xs text-secondary-500">{{ $conversation->subject }}</small>
                </div>
            </div>
            <div class="chat-actions">
                <button class="btn btn-icon btn-light rounded-circle" title="Archive">
                    <i class="fas fa-archive"></i>
                </button>
            </div>
        </div>
        
        <!-- Chat Messages -->
        <div class="chat-messages-area p-4 flex-grow-1 overflow-y-auto" id="chatBox">
            @foreach($conversation->messages as $message)
                <div class="message-wrapper mb-4 {{ $message->sender_type == 'vendor' ? 'sent' : 'received' }}">
                    <div class="message-bubble shadow-sm">
                        <div class="message-content">
                            {{ $message->body }}
                        </div>
                        <div class="message-meta">
                            {{ $message->created_at->format('g:i A') }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Message Input -->
        <div class="chat-input-area p-4 bg-white border-top">
            <form action="{{ route('vendor.messages.send', $conversation->id) }}" method="POST" id="messageForm">
                @csrf
                <div class="input-group-premium">
                    <input type="text" name="body" class="form-control-premium" placeholder="Type your response..." required autofocus autocomplete="off">
                    <button class="btn btn-primary btn-send shadow-primary-200" type="submit">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .chat-container { max-width: 900px; margin: 0 auto; }
    .font-bold { font-weight: 700; }
    
    .btn-icon { width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; padding: 0; }
    
    .avatar-circle.small { width: 44px; height: 44px; border-radius: 14px; font-size: 16px; background: #eff6ff; color: #0066FF; }
    
    .chat-messages-area { background: #f8fafc; scroll-behavior: smooth; }
    .chat-messages-area::-webkit-scrollbar { width: 6px; }
    .chat-messages-area::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    
    .message-wrapper { display: flex; flex-direction: column; width: 100%; }
    .message-wrapper.sent { align-items: flex-end; }
    .message-wrapper.received { align-items: flex-start; }
    
    .message-bubble {
        max-width: 70%;
        padding: 12px 18px;
        position: relative;
        font-size: 14.5px;
        line-height: 1.6;
    }
    
    .sent .message-bubble {
        background: #0066FF;
        color: white;
        border-radius: 18px 18px 4px 18px;
    }
    
    .received .message-bubble {
        background: white;
        color: #1e293b;
        border-radius: 18px 18px 18px 4px;
        border: 1px solid #e2e8f0;
    }
    
    .message-meta {
        font-size: 10px;
        margin-top: 6px;
        opacity: 0.6;
        font-weight: 500;
    }
    .sent .message-meta { text-align: right; color: white; }
    .received .message-meta { color: #64748b; }
    
    .input-group-premium {
        position: relative;
        display: flex;
        align-items: center;
        background: #f1f5f9;
        padding: 6px;
        border-radius: 30px;
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
    }
    
    .input-group-premium:focus-within {
        background: white;
        border-color: #0066FF;
        box-shadow: 0 0 0 4px rgba(0, 102, 255, 0.1);
    }
    
    .form-control-premium {
        flex-grow: 1;
        background: transparent;
        border: none;
        padding: 10px 20px;
        font-size: 15px;
        outline: none;
        color: #1e293b;
    }
    
    .btn-send {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        flex-shrink: 0;
    }
    
    .shadow-primary-200 { box-shadow: 0 4px 14px 0 rgba(0, 102, 255, 0.3); }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const chatBox = document.getElementById('chatBox');
        chatBox.scrollTop = chatBox.scrollHeight;
    });
</script>
@endsection
