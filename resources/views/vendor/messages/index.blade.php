@extends('layouts.app')

@section('title', 'Messages')
@section('page_title', 'Messages')

@section('sidebar')
    @include('vendor.partials.sidebar')
@endsection

@section('content')
<div class="messaging-container py-4">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h1 class="h3 font-bold text-secondary-900 mb-1">Customer Messages</h1>
            <p class="text-secondary-500 mb-0">Manage communication with your store visitors and customers.</p>
        </div>
        <div class="message-stats d-flex gap-4">
            <div class="stat-item text-center">
                <div class="h4 font-bold mb-0 text-primary">{{ $conversations->sum('unread_count_for_vendor') }}</div>
                <small class="text-xs uppercase font-bold text-secondary-400">Unread</small>
            </div>
            <div class="stat-item text-center">
                <div class="h4 font-bold mb-0 text-secondary-900">{{ $conversations->total() }}</div>
                <small class="text-xs uppercase font-bold text-secondary-400">Total</small>
            </div>
        </div>
    </div>

    <div class="dashboard-card border-0 shadow-sm overflow-hidden">
        <div class="dashboard-card-body p-0">
            @if($conversations->count() > 0)
                <div class="conversation-list">
                    @foreach($conversations as $conversation)
                        <a href="{{ route('vendor.messages.show', $conversation->id) }}" class="conversation-item {{ $conversation->unread_count_for_vendor > 0 ? 'unread' : '' }}">
                            <div class="item-content d-flex align-items-center p-4">
                                <div class="avatar-container mr-4">
                                    <div class="avatar-circle">
                                        {{ substr($conversation->user->name, 0, 1) }}
                                    </div>
                                    @if($conversation->unread_count_for_vendor > 0)
                                        <div class="unread-dot"></div>
                                    @endif
                                </div>
                                
                                <div class="flex-grow-1 mr-4">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <h5 class="customer-name mb-0">{{ $conversation->user->name }}</h5>
                                        <span class="message-time">{{ $conversation->last_message_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="latest-preview mb-0">{{ Str::limit($conversation->latestMessage->body, 120) }}</p>
                                </div>

                                <div class="message-actions">
                                    <i class="fas fa-chevron-right text-secondary-300"></i>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
                
                @if($conversations->hasPages())
                <div class="pagination-footer p-4 bg-white border-top">
                    {{ $conversations->links() }}
                </div>
                @endif
            @else
                <div class="text-center py-5 empty-state">
                    <div class="empty-icon-box mb-4">
                        <i class="fas fa-comments"></i>
                    </div>
                    <h3 class="font-bold text-secondary-900 mb-2">No conversations found</h3>
                    <p class="text-secondary-500 max-w-sm mx-auto">When customers message you about your products, their inquiries will appear here for you to respond.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .messaging-container { max-width: 1000px; margin: 0 auto; }
    .font-bold { font-weight: 700; }
    .letter-spacing-1 { letter-spacing: 0.05em; }
    
    .conversation-item {
        display: block;
        text-decoration: none;
        color: inherit;
        border-bottom: 1px solid #f1f5f9;
        transition: all 0.2s ease;
        background: white;
    }
    
    .conversation-item:last-child { border-bottom: none; }
    .conversation-item:hover { background: #f8fafc; }
    
    .conversation-item.unread { background: #f0f7ff; }
    .conversation-item.unread:hover { background: #eaf1ff; }
    
    .avatar-container { position: relative; }
    .avatar-circle {
        width: 56px;
        height: 56px;
        background: linear-gradient(135deg, #0066FF 0%, #004ecc 100%);
        color: white;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 20px;
        box-shadow: 0 4px 10px rgba(0, 102, 255, 0.2);
    }
    
    .unread-dot {
        position: absolute;
        top: -4px;
        right: -4px;
        width: 14px;
        height: 14px;
        background: #ef4444;
        border: 3px solid white;
        border-radius: 50%;
    }
    
    .customer-name { font-size: 16px; font-weight: 700; color: #1e293b; transition: color 0.2s ease; }
    .conversation-item:hover .customer-name { color: #0066FF; }
    
    .message-time { font-size: 12px; color: #94a3b8; font-weight: 500; }
    
    .latest-preview {
        font-size: 14px;
        color: #64748b;
        line-height: 1.5;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .conversation-item.unread .latest-preview { color: #334155; font-weight: 600; }
    
    .empty-icon-box {
        width: 100px;
        height: 100px;
        background: #f1f5f9;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        color: #94a3b8;
        font-size: 40px;
    }
    
    .max-w-sm { max-width: 24rem; }
    .gap-4 { gap: 2rem; }
</style>
@endsection
