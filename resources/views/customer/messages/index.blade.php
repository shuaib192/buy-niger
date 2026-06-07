{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    View: Customer Messages (Premium v2.0)
--}}
@extends('layouts.app')

@section('title', 'My Messages')
@section('page_title', 'Messages')

@section('sidebar')
    @include('customer.partials.sidebar')
@endsection

@section('content')
<div class="cmsg-page">
    {{-- Page Header --}}
    <div class="cmsg-header">
        <div>
            <h1 class="cmsg-title">Messages</h1>
            <p class="cmsg-sub">Conversations with sellers about your orders</p>
        </div>
        @php $totalUnread = $conversations->sum('unread_count_for_user'); @endphp
        @if($totalUnread > 0)
        <div class="cmsg-unread-badge">
            <i class="fas fa-bell"></i>
            {{ $totalUnread }} unread
        </div>
        @endif
    </div>

    <div class="cmsg-card">
        @if($conversations->count() > 0)
            <div class="cmsg-list">
                @foreach($conversations as $conversation)
                @php $isUnread = ($conversation->unread_count_for_user ?? 0) > 0; @endphp
                <a href="{{ route('customer.messages.show', $conversation->id) }}" 
                   class="cmsg-row {{ $isUnread ? 'is-unread' : '' }}">
                    {{-- Vendor Avatar --}}
                    <div class="cmsg-avatar">
                        {{ substr($conversation->vendor->store_name ?? 'V', 0, 1) }}
                        @if($isUnread)<span class="cmsg-dot"></span>@endif
                    </div>

                    {{-- Content --}}
                    <div class="cmsg-content">
                        <div class="cmsg-top">
                            <span class="cmsg-name">{{ $conversation->vendor->store_name ?? 'Vendor' }}</span>
                            <span class="cmsg-time">{{ $conversation->last_message_at ? $conversation->last_message_at->diffForHumans() : '' }}</span>
                        </div>
                        @if($conversation->subject)
                        <div class="cmsg-subject">{{ $conversation->subject }}</div>
                        @endif
                        <div class="cmsg-preview {{ $isUnread ? 'bold' : '' }}">
                            {{ Str::limit($conversation->latestMessage->body ?? 'No messages yet', 90) }}
                        </div>
                    </div>

                    {{-- Right --}}
                    <div class="cmsg-right">
                        @if($isUnread)
                        <span class="cmsg-count-pill">{{ $conversation->unread_count_for_user }}</span>
                        @endif
                        <i class="fas fa-chevron-right cmsg-arrow"></i>
                    </div>
                </a>
                @endforeach
            </div>

            @if($conversations->hasPages())
            <div class="cmsg-pagination">{{ $conversations->links() }}</div>
            @endif
        @else
            <div class="cmsg-empty">
                <div class="cmsg-empty-icon">
                    <i class="fas fa-comments"></i>
                </div>
                <h3>No messages yet</h3>
                <p>When you contact sellers about products or orders, your conversations will appear here.</p>
                <a href="{{ route('shop') }}" class="cmsg-browse-btn">
                    <i class="fas fa-shopping-bag"></i> Browse Products
                </a>
            </div>
        @endif
    </div>
</div>

<style>
.cmsg-page { animation: cmsgFade 0.35s ease; max-width: 860px; }
@keyframes cmsgFade { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

/* Header */
.cmsg-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 22px; flex-wrap: wrap; gap: 12px; }
.cmsg-title { font-size: 22px; font-weight: 900; color: #0f172a; margin: 0 0 2px; letter-spacing: -0.02em; }
.cmsg-sub { font-size: 13px; color: #94a3b8; margin: 0; font-weight: 500; }
.cmsg-unread-badge { display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px; background: linear-gradient(135deg, #fee2e2, #fecaca); color: #dc2626; border-radius: 20px; font-size: 13px; font-weight: 800; border: 1px solid #fca5a5; }

/* Card */
.cmsg-card { background: white; border: 1px solid #f1f5f9; border-radius: 22px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.04); }

/* Row */
.cmsg-row {
    display: flex; align-items: center; gap: 16px;
    padding: 18px 24px; border-bottom: 1px solid #f8fafc;
    text-decoration: none; color: inherit;
    transition: all 0.2s; cursor: pointer;
    position: relative;
}
.cmsg-row:last-child { border-bottom: none; }
.cmsg-row:hover { background: #fafbfc; }
.cmsg-row.is-unread { background: linear-gradient(135deg, #f0f7ff, #fafbff); }
.cmsg-row.is-unread:hover { background: #e8f0ff; }

/* Avatar */
.cmsg-avatar {
    width: 52px; height: 52px; border-radius: 16px;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: white; display: flex; align-items: center; justify-content: center;
    font-weight: 900; font-size: 18px; flex-shrink: 0;
    position: relative;
    box-shadow: 0 4px 10px rgba(99,102,241,0.2);
}
.cmsg-dot {
    position: absolute; top: -3px; right: -3px;
    width: 14px; height: 14px; background: #ef4444;
    border: 3px solid white; border-radius: 50%;
}

/* Content */
.cmsg-content { flex: 1; min-width: 0; }
.cmsg-top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2px; }
.cmsg-name { font-size: 15px; font-weight: 700; color: #0f172a; }
.cmsg-time { font-size: 11px; color: #94a3b8; font-weight: 500; white-space: nowrap; }
.cmsg-subject { font-size: 12px; color: #6366f1; font-weight: 700; margin-bottom: 2px; }
.cmsg-preview { font-size: 13px; color: #64748b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; line-height: 1.4; }
.cmsg-preview.bold { color: #0f172a; font-weight: 600; }

/* Right */
.cmsg-right { display: flex; align-items: center; gap: 8px; flex-shrink: 0; }
.cmsg-count-pill { background: #0066FF; color: white; font-size: 10px; font-weight: 800; padding: 2px 8px; border-radius: 20px; min-width: 20px; text-align: center; }
.cmsg-arrow { color: #cbd5e1; font-size: 12px; transition: transform 0.2s; }
.cmsg-row:hover .cmsg-arrow { transform: translateX(3px); color: #94a3b8; }

/* Empty */
.cmsg-empty { text-align: center; padding: 70px 20px; }
.cmsg-empty-icon { width: 90px; height: 90px; background: linear-gradient(135deg, #eef2ff, #f0f0ff); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 18px; font-size: 36px; color: #6366f1; }
.cmsg-empty h3 { font-size: 18px; font-weight: 800; color: #0f172a; margin: 0 0 8px; }
.cmsg-empty p { font-size: 14px; color: #94a3b8; max-width: 340px; margin: 0 auto 24px; line-height: 1.6; }
.cmsg-browse-btn { display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; background: linear-gradient(135deg, #6366f1, #8b5cf6); color: white; border-radius: 14px; font-weight: 700; text-decoration: none; transition: all 0.2s; box-shadow: 0 4px 14px rgba(99,102,241,0.3); }
.cmsg-browse-btn:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(99,102,241,0.4); }

/* Pagination */
.cmsg-pagination { padding: 16px 24px; border-top: 1px solid #f1f5f9; }

@media (max-width: 640px) { .cmsg-row { padding: 14px 16px; gap: 12px; } .cmsg-avatar { width: 44px; height: 44px; font-size: 16px; } .cmsg-preview { display: none; } }
</style>
@endsection
