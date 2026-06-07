{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    View: Notifications (Premium v2.0)
--}}
@extends('layouts.app')

@section('title', 'Notifications')
@section('page_title', 'Notifications')

@section('sidebar')
    @if(Auth::user()->role_id == 1 || Auth::user()->role_id == 2)
        @include('superadmin.partials.sidebar')
    @elseif(Auth::user()->role_id == 3)
        @include('vendor.partials.sidebar')
    @else
        @include('customer.partials.sidebar')
    @endif
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="dashboard-card shadow-sm border-0" style="border-radius: 20px; overflow: hidden; animation: fadeInUp 0.4s ease;">
            <div class="dashboard-card-header d-flex justify-content-between align-items-center bg-white border-bottom py-3 px-4">
                <h3 class="mb-0 fw-bold" style="font-family: 'Outfit', sans-serif; font-size: 1.25rem;"><i class="fas fa-bell me-2 text-primary"></i>Your Notifications</h3>
                @if($notifications->count() > 0)
                    <form action="{{ route('notifications.markAllRead') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-primary rounded-pill px-3">Mark All as Read</button>
                    </form>
                @endif
            </div>
            <div class="dashboard-card-body p-0">
                @forelse($notifications as $notification)
                    <a href="{{ route('notifications.read', $notification->id) }}" class="text-decoration-none text-dark d-block">
                        <div class="p-4 border-bottom {{ $notification->read_at ? 'bg-light' : 'bg-white' }} d-flex align-items-start gap-3 notification-row" style="transition: all 0.2s;">
                            <div class="rounded-circle p-2 {{ $notification->read_at ? 'bg-secondary bg-opacity-25 text-secondary' : 'bg-primary bg-opacity-10 text-primary' }} d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="fas fa-bell"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-1 fw-bold {{ $notification->read_at ? 'text-muted' : 'text-dark' }}" style="font-size: 14px;">{{ $notification->title }}</h5>
                                    <small class="text-muted" style="font-size: 11px;">{{ $notification->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="mb-1 text-secondary" style="font-size: 13px; line-height: 1.5;">{{ $notification->message }}</p>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="p-5 text-center text-muted">
                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 70px; height: 70px;">
                            <i class="fas fa-bell-slash fa-2x text-muted"></i>
                        </div>
                        <h4 class="fw-bold text-dark mb-1">No notifications yet</h4>
                        <p class="small text-muted mb-0">We will notify you here when there are updates on your account.</p>
                    </div>
                @endforelse
            </div>
            @if($notifications->hasPages())
                <div class="dashboard-card-footer bg-white border-top py-3 px-4">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(12px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .notification-row:hover {
        background-color: rgba(99, 102, 241, 0.03) !important;
    }
</style>
@endsection
