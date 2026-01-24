@extends('layouts.app')

@section('title', 'My Messages')
@section('page_title', 'Messages')

@section('sidebar')
    @include('customer.partials.sidebar')
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3>Messages</h3>
            </div>
            <div class="dashboard-card-body p-0">
                @if($conversations->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($conversations as $conversation)
                            <a href="{{ route('customer.messages.show', $conversation->id) }}" class="list-group-item list-group-item-action p-4 border-bottom {{ $conversation->unread_count_for_user > 0 ? 'bg-light' : '' }}" style="border-left: {{ $conversation->unread_count_for_user > 0 ? '4px solid var(--primary-500)' : '4px solid transparent' }};">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <h6 class="mb-0 text-dark font-weight-bold" style="font-weight: 600;">{{ $conversation->vendor->store_name }}</h6>
                                    <small class="text-muted">{{ $conversation->last_message_at->diffForHumans() }}</small>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <p class="mb-0 text-muted" style="color: var(--secondary-600);">{{ Str::limit($conversation->latestMessage->body, 100) }}</p>
                                    @if($conversation->unread_count_for_user > 0)
                                        <span class="badge bg-primary text-white rounded-pill px-3">{{ $conversation->unread_count_for_user }} new</span>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                    <div class="p-3">
                        {{ $conversations->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <div style="width: 80px; height: 80px; background: var(--secondary-50); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                            <i class="fas fa-comments text-secondary" style="font-size: 32px;"></i>
                        </div>
                        <h4 style="font-weight: 600; color: var(--secondary-900); margin-bottom: 8px;">No Messages Yet</h4>
                        <p class="text-muted">Messages from sellers will appear here.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
