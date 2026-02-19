@extends('layouts.app')

@section('title', 'Messages')
@section('page_title', 'Messages')

@section('sidebar')
    @include('vendor.partials.sidebar')
@endsection

@section('content')
<div class="messages-page">
    {{-- Page Header --}}
    <div class="page-header-premium">
        <div>
            <h1 class="page-title">Customer Messages</h1>
            <p class="page-subtitle">Respond to inquiries and keep your customers happy.</p>
        </div>
        <div class="msg-stats">
            <div class="msg-stat">
                <span class="msg-stat-value text-primary">{{ $conversations->sum('unread_count_for_vendor') ?? 0 }}</span>
                <span class="msg-stat-label">Unread</span>
            </div>
            <div class="msg-stat-divider"></div>
            <div class="msg-stat">
                <span class="msg-stat-value">{{ $conversations->total() }}</span>
                <span class="msg-stat-label">Total</span>
            </div>
        </div>
    </div>

    <div class="premium-card">
        @if($conversations->count() > 0)
            <div class="conversation-list">
                @foreach($conversations as $conversation)
                    @php $isUnread = ($conversation->unread_count_for_vendor ?? 0) > 0; @endphp
                    <a href="{{ route('vendor.messages.show', $conversation->id) }}" class="conversation-row {{ $isUnread ? 'unread' : '' }}">
                        <div class="conv-avatar">
                            {{ substr($conversation->user->name ?? 'U', 0, 1) }}
                            @if($isUnread) <span class="unread-indicator"></span> @endif
                        </div>
                        <div class="conv-content">
                            <div class="conv-top">
                                <span class="conv-name">{{ $conversation->user->name ?? 'Unknown' }}</span>
                                <span class="conv-time">{{ $conversation->last_message_at ? $conversation->last_message_at->diffForHumans() : '' }}</span>
                            </div>
                            @if($conversation->subject)
                                <div class="conv-subject">{{ $conversation->subject }}</div>
                            @endif
                            <div class="conv-preview">{{ Str::limit($conversation->latestMessage->body ?? 'No messages yet', 100) }}</div>
                        </div>
                        <div class="conv-arrow"><i class="fas fa-chevron-right"></i></div>
                    </a>
                @endforeach
            </div>
            
            @if($conversations->hasPages())
            <div class="pagination-bar">{{ $conversations->links() }}</div>
            @endif
        @else
            <div class="empty-state-premium">
                <div class="empty-icon"><i class="fas fa-comments"></i></div>
                <h4>No conversations yet</h4>
                <p>When customers contact you about your products, their messages will appear here.</p>
            </div>
        @endif
    </div>
</div>

<style>
    .messages-page { animation: fadeInUp 0.4s ease; max-width: 900px; }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }

    .page-header-premium { display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px; flex-wrap: wrap; gap: 16px; }
    .page-title { font-size: 24px; font-weight: 800; color: #0f172a; margin: 0 0 4px; letter-spacing: -0.02em; }
    .page-subtitle { color: #64748b; font-size: 14px; margin: 0; font-weight: 500; }

    .msg-stats { display: flex; align-items: center; gap: 20px; background: white; border: 1px solid #f1f5f9; border-radius: 16px; padding: 12px 24px; }
    .msg-stat { display: flex; flex-direction: column; align-items: center; }
    .msg-stat-value { font-size: 22px; font-weight: 800; color: #0f172a; line-height: 1; }
    .msg-stat-value.text-primary { color: #0066FF; }
    .msg-stat-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: #94a3b8; margin-top: 2px; }
    .msg-stat-divider { width: 1px; height: 32px; background: #f1f5f9; }

    .premium-card { background: white; border: 1px solid #f1f5f9; border-radius: 20px; overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,0.03); }

    .conversation-row { display: flex; align-items: center; gap: 16px; padding: 18px 24px; border-bottom: 1px solid #f8fafc; text-decoration: none; color: inherit; transition: all 0.2s ease; cursor: pointer; }
    .conversation-row:last-child { border-bottom: none; }
    .conversation-row:hover { background: #fafbfc; }
    .conversation-row.unread { background: #f0f7ff; }
    .conversation-row.unread:hover { background: #e8f0ff; }

    .conv-avatar { width: 52px; height: 52px; border-radius: 16px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 18px; flex-shrink: 0; position: relative; box-shadow: 0 4px 10px rgba(102,126,234,0.2); }
    .unread-indicator { position: absolute; top: -3px; right: -3px; width: 14px; height: 14px; background: #ef4444; border: 3px solid white; border-radius: 50%; }

    .conv-content { flex-grow: 1; min-width: 0; }
    .conv-top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2px; }
    .conv-name { font-size: 15px; font-weight: 700; color: #0f172a; }
    .conv-time { font-size: 12px; color: #94a3b8; font-weight: 500; white-space: nowrap; }
    .conv-subject { font-size: 12px; color: #0066FF; font-weight: 600; margin-bottom: 2px; }
    .conv-preview { font-size: 13px; color: #64748b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; line-height: 1.4; }
    .conversation-row.unread .conv-preview { color: #334155; font-weight: 600; }

    .conv-arrow { color: #cbd5e1; font-size: 12px; transition: transform 0.2s; flex-shrink: 0; }
    .conversation-row:hover .conv-arrow { transform: translateX(3px); color: #94a3b8; }

    .empty-state-premium { text-align: center; padding: 60px 20px; }
    .empty-icon { width: 80px; height: 80px; background: #f1f5f9; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; font-size: 32px; color: #94a3b8; }
    .empty-state-premium h4 { font-weight: 700; color: #0f172a; margin-bottom: 4px; }
    .empty-state-premium p { color: #94a3b8; font-size: 14px; max-width: 360px; margin: 0 auto; }

    .pagination-bar { padding: 16px 24px; border-top: 1px solid #f1f5f9; }

    @media (max-width: 768px) { .page-header-premium { flex-direction: column; align-items: flex-start; } .msg-stats { width: 100%; justify-content: center; } }
</style>
@endsection
