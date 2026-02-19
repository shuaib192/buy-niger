{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    Written by Shuaibu Abdulmumin (08122598372, 07049906420)
    
    Partial: AI Chatbot Widget (Global - All Users)
--}}
@auth
<style>
/* ===== BuyNiger AI Chatbot ===== */
.bn-chat-fab {
    position: fixed;
    bottom: 24px;
    right: 24px;
    z-index: 99999;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #a855f7 100%);
    border: none;
    color: #fff;
    font-size: 24px;
    cursor: pointer;
    box-shadow: 0 6px 30px rgba(99,102,241,0.45), 0 0 0 0 rgba(99,102,241,0.3);
    transition: all 0.3s cubic-bezier(0.4,0,0.2,1);
    display: flex;
    align-items: center;
    justify-content: center;
    animation: bn-pulse 2.5s infinite;
}
.bn-chat-fab:hover {
    transform: scale(1.1);
    box-shadow: 0 8px 40px rgba(99,102,241,0.55);
}
.bn-chat-fab.active {
    animation: none;
    transform: rotate(90deg);
    background: linear-gradient(135deg, #ef4444, #f97316);
}
@keyframes bn-pulse {
    0%, 100% { box-shadow: 0 6px 30px rgba(99,102,241,0.45), 0 0 0 0 rgba(99,102,241,0.3); }
    50% { box-shadow: 0 6px 30px rgba(99,102,241,0.45), 0 0 0 12px rgba(99,102,241,0); }
}

/* Chat Window */
.bn-chat-window {
    position: fixed;
    bottom: 96px;
    right: 24px;
    z-index: 99998;
    width: 380px;
    height: 520px;
    background: #fff;
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.15), 0 0 0 1px rgba(0,0,0,0.05);
    display: none;
    flex-direction: column;
    overflow: hidden;
    transform: translateY(20px) scale(0.95);
    opacity: 0;
    transition: all 0.35s cubic-bezier(0.4,0,0.2,1);
    font-family: 'Inter', system-ui, -apple-system, sans-serif;
}
.bn-chat-window.open {
    display: flex;
    transform: translateY(0) scale(1);
    opacity: 1;
}

/* Header */
.bn-chat-header {
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #a855f7 100%);
    color: #fff;
    padding: 18px 20px;
    display: flex;
    align-items: center;
    gap: 12px;
    position: relative;
    overflow: hidden;
}
.bn-chat-header::after {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 100%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, transparent 70%);
    pointer-events: none;
}
.bn-chat-avatar {
    width: 42px;
    height: 42px;
    border-radius: 12px;
    background: rgba(255,255,255,0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
    backdrop-filter: blur(10px);
}
.bn-chat-header-info h4 {
    margin: 0;
    font-size: 15px;
    font-weight: 700;
    letter-spacing: -0.01em;
}
.bn-chat-header-info p {
    margin: 0;
    font-size: 11px;
    opacity: 0.8;
    display: flex;
    align-items: center;
    gap: 5px;
}
.bn-chat-header-info p .bn-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #34d399;
    display: inline-block;
    animation: bn-dot-pulse 1.5s infinite;
}
@keyframes bn-dot-pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.4; }
}
.bn-chat-close {
    position: absolute;
    top: 14px;
    right: 14px;
    background: rgba(255,255,255,0.15);
    border: none;
    color: #fff;
    width: 30px;
    height: 30px;
    border-radius: 8px;
    cursor: pointer;
    font-size: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.2s;
    z-index: 2;
}
.bn-chat-close:hover {
    background: rgba(255,255,255,0.3);
}

/* Messages */
.bn-chat-messages {
    flex: 1;
    padding: 16px;
    overflow-y: auto;
    background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
    display: flex;
    flex-direction: column;
    gap: 10px;
    scroll-behavior: smooth;
}
.bn-chat-messages::-webkit-scrollbar { width: 4px; }
.bn-chat-messages::-webkit-scrollbar-track { background: transparent; }
.bn-chat-messages::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }

.bn-msg {
    max-width: 82%;
    padding: 12px 16px;
    border-radius: 16px;
    font-size: 13px;
    line-height: 1.55;
    word-wrap: break-word;
    animation: bn-msg-in 0.3s ease-out;
}
@keyframes bn-msg-in {
    from { opacity: 0; transform: translateY(8px); }
    to { opacity: 1; transform: translateY(0); }
}
.bn-msg-bot {
    background: #fff;
    color: #1e293b;
    border-bottom-left-radius: 4px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.06);
    align-self: flex-start;
}
.bn-msg-user {
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: #fff;
    border-bottom-right-radius: 4px;
    align-self: flex-end;
}
.bn-msg-typing {
    display: flex;
    gap: 5px;
    padding: 14px 20px;
}
.bn-msg-typing span {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #94a3b8;
    animation: bn-typing 1.4s infinite;
}
.bn-msg-typing span:nth-child(2) { animation-delay: 0.2s; }
.bn-msg-typing span:nth-child(3) { animation-delay: 0.4s; }
@keyframes bn-typing {
    0%, 60%, 100% { transform: translateY(0); opacity: 0.4; }
    30% { transform: translateY(-6px); opacity: 1; }
}

/* Quick Actions */
.bn-quick-actions {
    padding: 8px 16px 4px;
    background: #fff;
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
    border-top: 1px solid #f1f5f9;
}
.bn-quick-btn {
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    border-radius: 20px;
    padding: 5px 12px;
    font-size: 11px;
    color: #6366f1;
    cursor: pointer;
    transition: all 0.2s;
    white-space: nowrap;
    font-weight: 500;
}
.bn-quick-btn:hover {
    background: #e0e7ff;
    border-color: #a5b4fc;
    transform: translateY(-1px);
}

/* Input Area */
.bn-chat-input {
    padding: 12px 16px;
    background: #fff;
    border-top: 1px solid #e5e7eb;
    display: flex;
    gap: 8px;
    align-items: center;
}
.bn-chat-input input {
    flex: 1;
    border: 1.5px solid #e5e7eb;
    border-radius: 12px;
    padding: 12px 16px;
    font-size: 13px;
    outline: none;
    transition: border-color 0.2s, box-shadow 0.2s;
    font-family: inherit;
    background: #f8fafc;
}
.bn-chat-input input:focus {
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99,102,241,0.1);
    background: #fff;
}
.bn-chat-input input::placeholder {
    color: #94a3b8;
}
.bn-chat-send {
    width: 42px;
    height: 42px;
    border-radius: 12px;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    border: none;
    color: #fff;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    transition: all 0.2s;
    flex-shrink: 0;
}
.bn-chat-send:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 15px rgba(99,102,241,0.4);
}
.bn-chat-send:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    transform: none;
}

/* Responsive */
@media (max-width: 480px) {
    .bn-chat-window {
        width: calc(100vw - 32px);
        height: calc(100vh - 140px);
        max-height: 520px;
        right: 16px;
        bottom: 90px;
        border-radius: 16px;
    }
    .bn-chat-fab {
        bottom: 20px;
        right: 16px;
        width: 52px;
        height: 52px;
        font-size: 20px;
    }
}
</style>

{{-- Floating Action Button --}}
<button class="bn-chat-fab" id="bnChatFab" onclick="bnToggleChat()" title="Chat with AI">
    <i class="fas fa-comment-dots" id="bnFabIcon"></i>
</button>

{{-- Chat Window --}}
<div class="bn-chat-window" id="bnChatWindow">
    {{-- Header --}}
    <div class="bn-chat-header">
        <div class="bn-chat-avatar">🤖</div>
        <div class="bn-chat-header-info">
            <h4>BuyNiger AI</h4>
            <p><span class="bn-dot"></span> Online — Powered by AI</p>
        </div>
        <button class="bn-chat-close" onclick="bnToggleChat()">
            <i class="fas fa-times"></i>
        </button>
    </div>

    {{-- Messages --}}
    <div class="bn-chat-messages" id="bnMessages">
        <div class="bn-msg bn-msg-bot">
            👋 Hi <b>{{ Auth::user()->name }}</b>! I'm your BuyNiger AI assistant. I can help with orders, products, account questions, and more. What can I do for you?
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="bn-quick-actions" id="bnQuickActions">
        @if(Auth::user()->role_id == 3 || (Auth::user()->vendor ?? false))
            <button class="bn-quick-btn" onclick="bnQuickSend('Show my products')">📦 My Products</button>
            <button class="bn-quick-btn" onclick="bnQuickSend('What is my balance?')">💰 My Balance</button>
            <button class="bn-quick-btn" onclick="bnQuickSend('Show my recent orders')">📋 Orders</button>
            <button class="bn-quick-btn" onclick="bnQuickSend('How to add a product?')">➕ Add Product</button>
        @else
            <button class="bn-quick-btn" onclick="bnQuickSend('Show my orders')">📋 My Orders</button>
            <button class="bn-quick-btn" onclick="bnQuickSend('Track my order')">🚚 Track Order</button>
            <button class="bn-quick-btn" onclick="bnQuickSend('Help me find a product')">🔍 Find Product</button>
            <button class="bn-quick-btn" onclick="bnQuickSend('How to contact a vendor?')">💬 Contact</button>
        @endif
    </div>

    {{-- Input --}}
    <div class="bn-chat-input">
        <input type="text" id="bnInput" placeholder="Type a message..." autocomplete="off" 
               onkeypress="if(event.key==='Enter' && !event.shiftKey) bnSendMsg()">
        <button class="bn-chat-send" id="bnSendBtn" onclick="bnSendMsg()">
            <i class="fas fa-paper-plane"></i>
        </button>
    </div>
</div>

<script>
(function() {
    var bnSessionId = null;
    var bnChatOpen = false;
    var bnSending = false;

    window.bnToggleChat = function() {
        var win = document.getElementById('bnChatWindow');
        var fab = document.getElementById('bnChatFab');
        var icon = document.getElementById('bnFabIcon');

        bnChatOpen = !bnChatOpen;

        if (bnChatOpen) {
            win.style.display = 'flex';
            // Force reflow for animation
            win.offsetHeight;
            win.classList.add('open');
            fab.classList.add('active');
            icon.className = 'fas fa-times';
            if (!bnSessionId) bnOpenSession();
            document.getElementById('bnInput').focus();
        } else {
            win.classList.remove('open');
            fab.classList.remove('active');
            icon.className = 'fas fa-comment-dots';
            setTimeout(function() {
                if (!bnChatOpen) win.style.display = 'none';
            }, 350);
        }
    };

    function bnOpenSession() {
        bnAddMsg('Connecting...', 'bot', 'bn-connecting');

        fetch('/chatbot/open', {
            method: 'GET',
            headers: { 
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            var el = document.getElementById('bn-connecting');
            if (el) el.remove();

            if (data.success) {
                bnSessionId = data.session_id;
                if (data.messages && data.messages.length > 0) {
                    var msgs = document.getElementById('bnMessages');
                    msgs.innerHTML = '';
                    data.messages.forEach(function(m) {
                        bnAddMsg(m.content, m.role === 'user' ? 'user' : 'bot');
                    });
                }
            } else {
                bnAddMsg('Could not connect. Please refresh and try again.', 'bot');
            }
        })
        .catch(function(e) {
            var el = document.getElementById('bn-connecting');
            if (el) el.remove();
            console.error('Chat session error:', e);
            bnAddMsg('Connection failed. Please refresh the page.', 'bot');
        });
    }

    window.bnSendMsg = function() {
        var input = document.getElementById('bnInput');
        var text = input.value.trim();
        if (!text || bnSending) return;

        if (!bnSessionId) {
            bnAddMsg('Please wait, connecting...', 'bot');
            bnOpenSession();
            return;
        }

        bnSending = true;
        document.getElementById('bnSendBtn').disabled = true;
        bnAddMsg(text, 'user');
        input.value = '';

        // Hide quick actions after first message
        var qa = document.getElementById('bnQuickActions');
        if (qa) qa.style.display = 'none';

        // Add typing indicator
        bnAddTyping();

        fetch('/chatbot/send', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ session_id: bnSessionId, message: text })
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            bnRemoveTyping();
            if (data.success && data.message) {
                bnAddMsg(data.message.content, 'bot');
            } else if (data.error) {
                bnAddMsg('⚠️ ' + data.error, 'bot');
            } else {
                bnAddMsg('Sorry, something went wrong. Please try again.', 'bot');
            }
        })
        .catch(function(e) {
            bnRemoveTyping();
            console.error('Send error:', e);
            bnAddMsg('Failed to send. Please try again.', 'bot');
        })
        .finally(function() {
            bnSending = false;
            document.getElementById('bnSendBtn').disabled = false;
            document.getElementById('bnInput').focus();
        });
    };

    window.bnQuickSend = function(text) {
        document.getElementById('bnInput').value = text;
        bnSendMsg();
    };

    function bnAddMsg(text, type, id) {
        var msgs = document.getElementById('bnMessages');
        var div = document.createElement('div');
        if (id) div.id = id;
        div.className = 'bn-msg bn-msg-' + type;
        div.innerHTML = text.replace(/\n/g, '<br>');
        msgs.appendChild(div);
        msgs.scrollTop = msgs.scrollHeight;
    }

    function bnAddTyping() {
        var msgs = document.getElementById('bnMessages');
        var div = document.createElement('div');
        div.id = 'bn-typing';
        div.className = 'bn-msg bn-msg-bot bn-msg-typing';
        div.innerHTML = '<span></span><span></span><span></span>';
        msgs.appendChild(div);
        msgs.scrollTop = msgs.scrollHeight;
    }

    function bnRemoveTyping() {
        var el = document.getElementById('bn-typing');
        if (el) el.remove();
    }

    // Add msg helper to window for external calls
    window.bnAddMsg = bnAddMsg;
})();
</script>
@endauth
