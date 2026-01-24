@extends('layouts.app')

@section('title', 'Notifications')
@section('page_title', 'Notifications')

@section('sidebar')
    @include('superadmin.partials.sidebar') <!-- Fallback sidebar, or generic -->
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="dashboard-card">
            <div class="dashboard-card-header d-flex justify-content-between align-items-center">
                <h3>Your Notifications</h3>
                @if($notifications->count() > 0)
                    <form action="{{ route('notifications.markAllRead') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-primary">Mark All as Read</button>
                    </form>
                @endif
            </div>
            <div class="dashboard-card-body p-0">
                @forelse($notifications as $notification)
                    <a href="{{ route('notifications.read', $notification->id) }}" class="text-decoration-none text-dark">
                        <div class="p-4 border-bottom {{ $notification->read_at ? 'bg-light' : 'bg-white' }} d-flex align-items-start gap-3">
                            <div class="rounded-circle p-2 {{ $notification->read_at ? 'bg-secondary' : 'bg-primary' }} text-white">
                                <i class="fas fa-bell"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between">
                                    <h5 class="mb-1 fw-bold {{ $notification->read_at ? 'text-muted' : '' }}">{{ $notification->title }}</h5>
                                    <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="mb-1 text-secondary">{{ $notification->message }}</p>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="p-5 text-center text-muted">
                        <i class="fas fa-bell-slash fa-3x mb-3"></i>
                        <p>You have no notifications at this time.</p>
                    </div>
                @endforelse
            </div>
            <div class="dashboard-card-footer">
                {{ $notifications->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
