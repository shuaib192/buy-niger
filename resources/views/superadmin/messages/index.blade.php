{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    Written by Shuaibu Abdulmumin
    View: Admin — Contact Messages — Premium v2.0
--}}
@extends('layouts.app')

@section('title', 'Contact Messages')
@section('page_title', 'Contact Messages')

@section('sidebar')
    @include('superadmin.partials.sidebar')
@endsection

@php
    $prefix = request()->is('admin*') ? 'admin.' : 'superadmin.';
@endphp

@push('styles')
<style>
.msg-header {
    background: linear-gradient(135deg, #0c4a6e 0%, #1e40af 50%, #4f46e5 100%);
    border-radius: 18px;
    padding: 26px 32px;
    margin-bottom: 24px;
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
}
.msg-header::before {
    content: '';
    position: absolute;
    top: -50px; right: -40px;
    width: 180px; height: 180px;
    background: rgba(147,197,253,.08);
    border-radius: 50%;
}
.mh-content { position: relative; z-index: 1; }
.mh-content h2 {
    color: white; font-size: 1.375rem; font-weight: 800;
    font-family: 'Outfit', sans-serif; margin-bottom: 4px;
}
.mh-content p { color: rgba(255,255,255,.6); font-size: .875rem; margin: 0; }
.mh-unread-badge {
    position: relative; z-index: 1;
    display: inline-flex; align-items: center; gap: 7px;
    padding: 8px 16px; border-radius: 20px;
    background: rgba(255,255,255,.15); color: white;
    font-size: .85rem; font-weight: 700; border: 1px solid rgba(255,255,255,.2);
}
.mh-unread-dot { width: 8px; height: 8px; border-radius: 50%; background: #fbbf24; }

.msg-filters {
    display: flex; align-items: center; gap: 8px; flex-wrap: wrap;
    padding: 14px 20px; background: var(--surface); border-bottom: 1px solid var(--border-color);
}
.msg-search-wrap {
    display: flex; gap: 0; border: 1.5px solid var(--border-color);
    border-radius: 10px; overflow: hidden;
}
.msg-search-wrap input { border: none; font-size: .8125rem; padding: 8px 12px; min-width: 220px; }
.msg-search-wrap input:focus { outline: none; }
.msg-search-wrap button { border-radius: 0; padding: 8px 14px; border: none; background: #4f46e5; color: white; cursor: pointer; }

.msg-row { }
.msg-row.unread { background: rgba(79,70,229,.025); }
.msg-sender { display: flex; align-items: center; gap: 10px; }
.msg-sender-avatar {
    width: 34px; height: 34px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    color: white; font-size: .8rem; font-weight: 700; flex-shrink: 0;
}
.msg-sender-avatar.new   { background: linear-gradient(135deg, #4f46e5, #8b5cf6); }
.msg-sender-avatar.read  { background: #94a3b8; }
.msg-sender-name  { font-weight: 700; font-size: .875rem; color: var(--text-primary); }
.msg-sender-email { font-size: .72rem; color: var(--text-muted); margin-top: 1px; }
.msg-subject { font-weight: 600; font-size: .875rem; color: var(--text-primary); }
.msg-snippet { font-size: .8rem; color: var(--text-muted); }
.msg-unread-pill {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 4px 10px; border-radius: 7px;
    background: rgba(79,70,229,.1); color: #4338ca;
    font-size: .73rem; font-weight: 700;
}
.msg-read-pill {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 4px 10px; border-radius: 7px;
    background: rgba(100,116,139,.08); color: #64748b;
    font-size: .73rem; font-weight: 600;
}
.msg-time-date { font-size: .8125rem; font-weight: 600; color: var(--text-primary); }
.msg-time-ago  { font-size: .72rem; color: var(--text-muted); margin-top: 1px; }

.btn-open-msg {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 6px 13px; border-radius: 9px;
    background: rgba(79,70,229,.08); color: #4f46e5;
    border: 1.5px solid rgba(79,70,229,.2);
    font-size: .78rem; font-weight: 700;
    cursor: pointer; transition: all .15s;
}
.btn-open-msg:hover { background: #4f46e5; color: white; }

/* Modal */
.msg-modal-overlay {
    position: fixed; inset: 0; z-index: 9999;
    background: rgba(0,0,0,.5); backdrop-filter: blur(5px);
    display: flex; align-items: center; justify-content: center;
    padding: 20px;
    opacity: 0; pointer-events: none; transition: opacity .2s;
}
.msg-modal-overlay.show { opacity: 1; pointer-events: all; }
.msg-modal-box {
    background: white; border-radius: 20px;
    width: 100%; max-width: 560px;
    box-shadow: 0 24px 80px rgba(0,0,0,.25);
    transform: translateY(20px); transition: transform .25s;
    overflow: hidden;
}
.msg-modal-overlay.show .msg-modal-box { transform: translateY(0); }
.msg-modal-header {
    background: linear-gradient(135deg, #0c4a6e, #1e40af);
    padding: 20px 24px;
    display: flex; align-items: center; justify-content: space-between;
}
.msg-modal-title { color: white; font-weight: 800; font-family: 'Outfit', sans-serif; font-size: 1rem; }
.msg-modal-close {
    width: 30px; height: 30px; border-radius: 8px;
    background: rgba(255,255,255,.15); border: none; cursor: pointer;
    color: white; font-size: .85rem; display: flex; align-items: center; justify-content: center;
    transition: background .15s;
}
.msg-modal-close:hover { background: rgba(255,255,255,.25); }
.msg-modal-body { padding: 24px; }
.msg-modal-sender-card {
    display: flex; align-items: center; gap: 12px;
    padding: 14px 16px;
    background: rgba(79,70,229,.04);
    border: 1px solid rgba(79,70,229,.1);
    border-radius: 12px; margin-bottom: 18px;
}
.msg-modal-avatar {
    width: 42px; height: 42px; border-radius: 11px;
    background: linear-gradient(135deg, #4f46e5, #8b5cf6);
    display: flex; align-items: center; justify-content: center;
    color: white; font-weight: 800; font-size: 1rem; flex-shrink: 0;
}
.msg-modal-meta-label {
    font-size: .72rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: .04em; color: var(--text-muted); margin-bottom: 4px;
}
.msg-modal-body-text {
    background: #f8fafc; border: 1px solid var(--border-color);
    border-radius: 12px; padding: 14px; font-size: .85rem;
    line-height: 1.7; color: var(--text-secondary);
    white-space: pre-wrap; max-height: 220px; overflow-y: auto;
}
.msg-modal-body-text::-webkit-scrollbar { width: 5px; }
.msg-modal-body-text::-webkit-scrollbar-track { background: #f1f5f9; }
.msg-modal-body-text::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
.msg-modal-footer {
    padding: 16px 24px; border-top: 1px solid var(--border-color);
}
.msg-reply-btn {
    display: flex; align-items: center; justify-content: center; gap: 8px;
    width: 100%; padding: 12px; border-radius: 12px;
    background: linear-gradient(135deg, #4f46e5, #7c3aed);
    color: white; font-weight: 700; font-size: .9rem;
    text-decoration: none; transition: all .2s;
}
.msg-reply-btn:hover { transform: translateY(-1px); box-shadow: 0 5px 18px rgba(79,70,229,.3); color: white; }
</style>
@endpush

@section('content')

{{-- ═══ HEADER ═══ --}}
<div class="msg-header">
    <div class="mh-content">
        <h2><i class="fas fa-envelope-open-text" style="margin-right:10px;opacity:.8;"></i>Contact Messages</h2>
        <p>Review and respond to messages submitted via the contact form.</p>
    </div>
    <div class="mh-unread-badge">
        <div class="mh-unread-dot"></div>
        {{ $messages->where('is_read', false)->count() }} Unread
    </div>
</div>

{{-- ═══ TABLE CARD ═══ --}}
<div class="dashboard-card">
    <div class="dashboard-card-header">
        <div>
            <h3><i class="fas fa-inbox" style="color:#4f46e5;margin-right:8px;"></i>All Inquiries</h3>
            <div style="font-size:.8rem;color:var(--text-muted);margin-top:2px;">
                {{ $messages->total() }} message(s) total
            </div>
        </div>
    </div>

    <div class="msg-filters">
        <form action="" method="GET" style="display:flex;gap:0;">
            <div class="msg-search-wrap">
                <input type="text" name="search" placeholder="Search messages, sender..." value="{{ request('search') }}">
                <button type="submit"><i class="fas fa-search"></i></button>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Sender</th>
                    <th>Subject</th>
                    <th>Preview</th>
                    <th>Status</th>
                    <th>Received</th>
                    <th style="text-align:right;padding-right:20px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($messages as $message)
                    <tr class="msg-row {{ !$message->is_read ? 'unread' : '' }}">
                        <td>
                            <div class="msg-sender">
                                <div class="msg-sender-avatar {{ !$message->is_read ? 'new' : 'read' }}">
                                    {{ strtoupper(substr($message->name ?? 'U', 0, 1)) }}
                                </div>
                                <div>
                                    <div class="msg-sender-name">{{ $message->name }}</div>
                                    <div class="msg-sender-email">{{ $message->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="msg-subject">{{ $message->subject }}</div>
                        </td>
                        <td style="max-width:240px;">
                            <div class="msg-snippet">{{ Str::limit($message->message, 60) }}</div>
                        </td>
                        <td>
                            @if($message->is_read)
                                <span class="msg-read-pill"><i class="fas fa-envelope-open" style="font-size:.65rem;"></i> Read</span>
                            @else
                                <span class="msg-unread-pill"><i class="fas fa-envelope" style="font-size:.65rem;"></i> Unread</span>
                            @endif
                        </td>
                        <td>
                            <div class="msg-time-date">{{ $message->created_at->format('d M Y') }}</div>
                            <div class="msg-time-ago">{{ $message->created_at->diffForHumans() }}</div>
                        </td>
                        <td style="text-align:right;padding-right:20px;">
                            <button class="btn-open-msg"
                                onclick="openMsg({{ $message->id }}, '{{ addslashes($message->name) }}', '{{ addslashes($message->email) }}', '{{ addslashes($message->subject) }}', '{{ addslashes(preg_replace('/\r|\n/', ' ', $message->message)) }}', '{{ $message->created_at->format('d M Y H:i') }}')">
                                <i class="fas fa-eye"></i> Open
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <i class="fas fa-envelope-open-text"></i>
                                <p>No messages received yet.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($messages->hasPages())
        <div style="padding:14px 20px;">
            {{ $messages->appends(request()->query())->links() }}
        </div>
    @endif
</div>

{{-- ═══ MESSAGE MODAL ═══ --}}
<div class="msg-modal-overlay" id="msgModalOverlay">
    <div class="msg-modal-box">
        <div class="msg-modal-header">
            <div class="msg-modal-title"><i class="fas fa-envelope-open-text" style="margin-right:8px;opacity:.8;"></i> Message Detail</div>
            <button class="msg-modal-close" onclick="closeMsg()"><i class="fas fa-xmark"></i></button>
        </div>
        <div class="msg-modal-body">
            <div class="msg-modal-sender-card">
                <div class="msg-modal-avatar" id="mAvatar">U</div>
                <div>
                    <div style="font-weight:800;font-size:.95rem;color:var(--text-primary);" id="mName">—</div>
                    <div style="font-size:.8rem;color:var(--text-muted);" id="mEmail">—</div>
                </div>
            </div>
            <div style="margin-bottom:14px;">
                <div class="msg-modal-meta-label">Subject</div>
                <div style="font-weight:700;font-size:.9rem;color:var(--text-primary);" id="mSubject">—</div>
            </div>
            <div style="margin-bottom:14px;">
                <div class="msg-modal-meta-label">Received</div>
                <div style="font-size:.8125rem;color:var(--text-secondary);" id="mDate">—</div>
            </div>
            <div>
                <div class="msg-modal-meta-label">Message</div>
                <div class="msg-modal-body-text" id="mBody">—</div>
            </div>
        </div>
        <div class="msg-modal-footer">
            <a href="#" id="mReplyBtn" class="msg-reply-btn">
                <i class="fas fa-paper-plane"></i> Reply via Email
            </a>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function openMsg(id, name, email, subject, message, date) {
    document.getElementById('mAvatar').textContent = name.charAt(0).toUpperCase();
    document.getElementById('mName').textContent = name;
    document.getElementById('mEmail').textContent = email;
    document.getElementById('mSubject').textContent = subject;
    document.getElementById('mBody').textContent = message;
    document.getElementById('mDate').textContent = date;
    document.getElementById('mReplyBtn').href = 'mailto:' + email + '?subject=Re: ' + encodeURIComponent(subject);
    document.getElementById('msgModalOverlay').classList.add('show');
    document.body.style.overflow = 'hidden';

    // Mark as read
    fetch('{{ url($prefix."messages") }}/' + id + '/read', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' }
    }).catch(() => {});
}
function closeMsg() {
    document.getElementById('msgModalOverlay').classList.remove('show');
    document.body.style.overflow = '';
}
document.getElementById('msgModalOverlay').addEventListener('click', function(e) {
    if (e.target === this) closeMsg();
});
</script>
@endpush
