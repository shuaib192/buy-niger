{{-- AI Chatbot Widget - Compact & Role-Aware --}}
@auth
<div id="chatbot-widget">
    <!-- Toggle Button -->
    <button id="chatbot-toggle" class="chatbot-btn" title="AI Assistant">
        <i class="fas fa-comments"></i>
    </button>

    <!-- Chat Panel - Compact -->
    <div id="chatbot-panel" class="chatbot-panel" style="display: none;">
        <!-- Header -->
        <div class="chatbot-header">
            <div class="chatbot-header-info">
                <div class="chatbot-avatar">
                    <i class="fas fa-robot"></i>
                </div>
                <div class="chatbot-title">
                    <h6>BuyNiger AI</h6>
                    <span class="chatbot-status">
                        @if(Auth::user()->isSuperAdmin())
                            Super Admin Mode
                        @elseif(Auth::user()->isAdmin())
                            Admin Mode
                        @elseif(Auth::user()->isVendor())
                            Vendor Mode
                        @else
                            Shopping Assistant
                        @endif
                    </span>
                </div>
            </div>
            <button id="chatbot-close" class="chatbot-close-btn">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Messages -->
        <div id="chatbot-messages" class="chatbot-messages">
            <div class="chat-bubble assistant">
                <p>👋 Hi <strong>{{ Auth::user()->name }}</strong>!</p>
                @if(Auth::user()->isSuperAdmin())
                    <p>As Super Admin, I can help you manage users, vendors, and platform settings.</p>
                @elseif(Auth::user()->isAdmin())
                    <p>As Admin, I can help you manage users, orders, and platform operations.</p>
                @elseif(Auth::user()->isVendor())
                    <p>I can help you manage your products, orders, and store settings.</p>
                @else
                    <p>I can help you find products, track orders, and answer questions!</p>
                @endif
                <div class="quick-actions">
                    @if(Auth::user()->isVendor())
                        <button class="quick-action" data-message="Show my store stats">📊 Stats</button>
                        <button class="quick-action" data-message="Check pending orders">📦 Orders</button>
                    @elseif(Auth::user()->isSuperAdmin() || Auth::user()->isAdmin())
                        <button class="quick-action" data-message="Platform overview">📊 Overview</button>
                        <button class="quick-action" data-message="Pending vendors">👥 Vendors</button>
                    @else
                        <button class="quick-action" data-message="Check my orders">📦 Orders</button>
                        <button class="quick-action" data-message="Find products">🔍 Search</button>
                    @endif
                    <button class="quick-action" data-message="Help">❓ Help</button>
                </div>
            </div>
        </div>

        <!-- Input -->
        <div class="chatbot-footer">
            <div class="chatbot-input-wrap">
                <input type="text" id="chatbot-input" placeholder="Type your message..." autocomplete="off">
                <button id="chatbot-send"><i class="fas fa-paper-plane"></i></button>
            </div>
            <div class="chatbot-powered"><i class="fas fa-bolt"></i> Powered by Groq AI</div>
        </div>
    </div>
</div>

<style>
#chatbot-widget {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 99999;
    font-family: 'Inter', sans-serif;
}

.chatbot-btn {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    border: none;
    color: white;
    font-size: 22px;
    cursor: pointer;
    box-shadow: 0 4px 20px rgba(99, 102, 241, 0.4);
    transition: all 0.2s;
}

.chatbot-btn:hover {
    transform: scale(1.08);
}

.chatbot-panel {
    position: absolute;
    bottom: 70px;
    right: 0;
    width: 340px;
    height: 420px;
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.15);
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.chatbot-header {
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    padding: 14px 16px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.chatbot-header-info {
    display: flex;
    align-items: center;
    gap: 10px;
}

.chatbot-avatar {
    width: 36px;
    height: 36px;
    background: rgba(255,255,255,0.2);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 16px;
}

.chatbot-title h6 {
    color: white;
    font-size: 14px;
    font-weight: 600;
    margin: 0;
}

.chatbot-status {
    color: rgba(255,255,255,0.7);
    font-size: 11px;
}

.chatbot-close-btn {
    background: rgba(255,255,255,0.15);
    border: none;
    color: white;
    width: 28px;
    height: 28px;
    border-radius: 6px;
    cursor: pointer;
}

.chatbot-messages {
    flex: 1;
    padding: 14px;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 10px;
    background: #f8fafc;
}

.chat-bubble {
    max-width: 90%;
}

.chat-bubble.user {
    align-self: flex-end;
}

.chat-bubble.assistant {
    align-self: flex-start;
}

.chat-bubble p {
    padding: 10px 14px;
    border-radius: 14px;
    font-size: 13px;
    line-height: 1.4;
    margin: 0 0 4px 0;
}

.chat-bubble.user p {
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: white;
    border-bottom-right-radius: 4px;
}

.chat-bubble.assistant p {
    background: white;
    color: #1e293b;
    border: 1px solid #e2e8f0;
    border-bottom-left-radius: 4px;
}

.quick-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-top: 8px;
}

.quick-action {
    padding: 6px 10px;
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    font-size: 11px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
}

.quick-action:hover {
    background: #6366f1;
    color: white;
    border-color: #6366f1;
}

.typing-indicator {
    display: flex;
    gap: 4px;
    padding: 10px 14px;
    background: white;
    border-radius: 14px;
    border: 1px solid #e2e8f0;
    align-self: flex-start;
}

.typing-indicator span {
    width: 6px;
    height: 6px;
    background: #94a3b8;
    border-radius: 50%;
    animation: bounce 1.4s infinite;
}

.typing-indicator span:nth-child(2) { animation-delay: 0.2s; }
.typing-indicator span:nth-child(3) { animation-delay: 0.4s; }

@keyframes bounce {
    0%, 60%, 100% { transform: translateY(0); }
    30% { transform: translateY(-5px); }
}

.chatbot-footer {
    padding: 12px;
    background: white;
    border-top: 1px solid #f1f5f9;
}

.chatbot-input-wrap {
    display: flex;
    gap: 8px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 4px;
}

#chatbot-input {
    flex: 1;
    border: none;
    background: transparent;
    padding: 8px 12px;
    font-size: 13px;
    outline: none;
}

#chatbot-send {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    border: none;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: white;
    cursor: pointer;
}

.chatbot-powered {
    text-align: center;
    font-size: 10px;
    color: #94a3b8;
    margin-top: 8px;
}

.chatbot-powered i { color: #f59e0b; }

@media (max-width: 480px) {
    .chatbot-panel {
        width: calc(100vw - 24px);
        right: -8px;
        height: 60vh;
    }
}
</style>

<script>
$(document).ready(function() {
    var sessionId = null;
    var $panel = $('#chatbot-panel');
    var $messages = $('#chatbot-messages');
    var $input = $('#chatbot-input');
    
    $('#chatbot-toggle').on('click', function() {
        $panel.is(':visible') ? $panel.slideUp(150) : ($panel.slideDown(150), !sessionId && openSession(), $input.focus());
    });
    
    $('#chatbot-close').on('click', function() { $panel.slideUp(150); });
    
    $(document).on('click', '.quick-action', function() {
        $input.val($(this).data('message'));
        sendMessage();
    });
    
    $('#chatbot-send').on('click', sendMessage);
    $input.on('keypress', function(e) { e.which === 13 && (e.preventDefault(), sendMessage()); });
    
    function openSession() {
        $.get('{{ route("chatbot.open") }}', function(data) {
            sessionId = data.session_id;
            if (data.messages && data.messages.length > 0) {
                $messages.empty();
                data.messages.forEach(function(m) { addMessage(m.content, m.role); });
            }
        });
    }
    
    function sendMessage() {
        var text = $input.val().trim();
        if (!text || !sessionId) return !sessionId && openSession();
        
        addMessage(text, 'user');
        $input.val('');
        
        var $typing = $('<div class="typing-indicator"><span></span><span></span><span></span></div>');
        $messages.append($typing);
        scroll();
        
        $.ajax({
            url: '{{ route("chatbot.send") }}',
            method: 'POST',
            contentType: 'application/json',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            data: JSON.stringify({ session_id: sessionId, message: text }),
            success: function(data) {
                $typing.remove();
                data.success && data.message && addMessage(data.message.content, 'assistant');
            },
            error: function() { $typing.remove(); addMessage('Sorry, try again.', 'assistant'); }
        });
    }
    
    function addMessage(text, role) {
        text = text.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>').replace(/\n/g, '<br>');
        $messages.append('<div class="chat-bubble ' + role + '"><p>' + text + '</p></div>');
        scroll();
    }
    
    function scroll() { $messages.animate({ scrollTop: $messages[0].scrollHeight }, 200); }
});
</script>
@endauth
