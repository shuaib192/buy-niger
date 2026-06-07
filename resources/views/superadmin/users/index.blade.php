{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    Written by Shuaibu Abdulmumin
    View: Admin — User Management — Premium v2.0
--}}
@extends('layouts.app')
@section('title', 'User Management')
@section('page_title', 'User Management')
@section('sidebar')
    @include('superadmin.partials.sidebar')
@endsection

@push('styles')
<style>
.filters-bar {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
    padding: 14px 20px;
    background: var(--surface);
    border-bottom: 1px solid var(--border-color);
}
.filters-bar form { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
.filter-tabs { display: flex; gap: 4px; flex-wrap: wrap; margin-right: 8px; }
.filter-tab {
    padding: 6px 14px;
    border-radius: 8px;
    font-size: .8125rem;
    font-weight: 600;
    color: var(--text-secondary);
    background: white;
    border: 1.5px solid var(--border-color);
    text-decoration: none;
    transition: all .15s;
}
.filter-tab:hover { border-color: #4f46e5; color: #4f46e5; }
.filter-tab.active { background: #4f46e5; border-color: #4f46e5; color: white; }
.search-group {
    display: flex;
    gap: 0;
    border: 1.5px solid var(--border-color);
    border-radius: 10px;
    overflow: hidden;
}
.search-group .form-control {
    border: none;
    border-radius: 0;
    font-size: .8125rem;
    min-width: 200px;
}
.search-group .form-control:focus { box-shadow: none; }
.search-group .btn {
    border-radius: 0;
    padding: 9px 14px;
    font-size: .8125rem;
}
.user-row-name {
    display: flex; align-items: center; gap: 10px;
}
.user-row-avatar {
    width: 34px; height: 34px; border-radius: 10px;
    background: linear-gradient(135deg, #4f46e5, #8b5cf6);
    display: flex; align-items: center; justify-content: center;
    color: white; font-size: .8rem; font-weight: 700;
    overflow: hidden; flex-shrink: 0;
}
.user-row-avatar img { width:100%;height:100%;object-fit:cover; }
.user-row-info-name { font-weight: 600; font-size: .875rem; color: var(--text-primary); }
.user-row-info-phone { font-size: .7rem; color: var(--text-muted); margin-top: 1px; }
.action-group { display: flex; gap: 6px; align-items: center; }
</style>
@endpush

@section('content')
@php $prefix = request()->is('admin*') ? 'admin.' : 'superadmin.'; @endphp

<div class="dashboard-card">
    {{-- Card Header --}}
    <div class="dashboard-card-header">
        <div>
            <h3><i class="fas fa-users" style="color:#4f46e5;margin-right:8px;"></i>All Users</h3>
            <div style="font-size:.8rem;color:var(--text-muted);margin-top:2px;">
                Manage platform users, roles, and access.
            </div>
        </div>
        <a href="{{ route($prefix.'users.export', request()->all()) }}" class="btn btn-sm btn-success">
            <i class="fas fa-file-csv"></i> Export CSV
        </a>
    </div>

    {{-- Filters Bar --}}
    <div class="filters-bar">
        {{-- Role Filter --}}
        <div class="filter-tabs">
            <a href="{{ route($prefix.'users') }}"
               class="filter-tab {{ !request('role') ? 'active' : '' }}">All</a>
            <a href="{{ route($prefix.'users', ['role' => 1]) }}"
               class="filter-tab {{ request('role') == 1 ? 'active' : '' }}">Super Admin</a>
            <a href="{{ route($prefix.'users', ['role' => 2]) }}"
               class="filter-tab {{ request('role') == 2 ? 'active' : '' }}">Admin</a>
            <a href="{{ route($prefix.'users', ['role' => 3]) }}"
               class="filter-tab {{ request('role') == 3 ? 'active' : '' }}">Vendors</a>
            <a href="{{ route($prefix.'users', ['role' => 4]) }}"
               class="filter-tab {{ request('role') == 4 ? 'active' : '' }}">Customers</a>
        </div>

        {{-- Search --}}
        <form action="{{ route($prefix.'users') }}" method="GET" style="margin-left:auto;">
            @if(request('role'))<input type="hidden" name="role" value="{{ request('role') }}">@endif
            <div class="search-group">
                <input type="text" name="search" class="form-control"
                       placeholder="Search name, email..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Role</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>
                            <div class="user-row-name">
                                <div class="user-row-avatar">
                                    @php $av = $user->avatar_url ?? null; @endphp
                                    @if($av && !str_contains($av,'ui-avatars'))
                                        <img src="{{ $av }}" alt="">
                                    @else
                                        {{ strtoupper(substr($user->name,0,1)) }}
                                    @endif
                                </div>
                                <div>
                                    <div class="user-row-info-name">{{ $user->name }}</div>
                                    <div class="user-row-info-phone">{{ $user->phone ?? '—' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($user->role_id == 1)
                                <span class="badge badge-primary">Super Admin</span>
                            @elseif($user->role_id == 2)
                                <span class="badge badge-info">Admin</span>
                            @elseif($user->role_id == 3)
                                <span class="badge badge-orange">Vendor</span>
                            @else
                                <span class="badge badge-secondary">Customer</span>
                            @endif
                        </td>
                        <td style="font-size:.8125rem;">{{ $user->email }}</td>
                        <td>
                            @if($user->is_active)
                                <span class="badge badge-success">
                                    <i class="fas fa-circle" style="font-size:.4rem;"></i> Active
                                </span>
                            @else
                                <span class="badge badge-danger">
                                    <i class="fas fa-ban" style="font-size:.65rem;"></i> Banned
                                </span>
                            @endif
                        </td>
                        <td style="font-size:.8125rem;color:var(--text-secondary);">
                            {{ $user->created_at->format('d M Y') }}
                        </td>
                        <td>
                            <div class="action-group">
                                <a href="{{ route($prefix.'users.show', $user) }}"
                                   class="btn btn-sm btn-secondary" title="View Profile">
                                    <i class="fas fa-eye"></i>
                                </a>

                                @if($user->id !== auth()->id())
                                    <form action="{{ route($prefix.'users.ban', $user) }}"
                                          method="POST"
                                          onsubmit="return confirm('{{ $user->is_active ? 'Ban this user?' : 'Re-activate this user?' }}')">
                                        @csrf
                                        <button type="submit"
                                                class="btn btn-sm {{ $user->is_active ? 'btn-danger' : 'btn-success' }}"
                                                title="{{ $user->is_active ? 'Ban User' : 'Activate User' }}">
                                            <i class="fas {{ $user->is_active ? 'fa-ban' : 'fa-check' }}"></i>
                                        </button>
                                    </form>

                                    <form action="{{ route($prefix.'users.delete', $user) }}"
                                          method="POST"
                                          onsubmit="return confirm('Permanently delete {{ $user->name }}? This cannot be undone.')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete User">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <i class="fas fa-users-slash"></i>
                                <p>No users found matching your criteria.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div style="padding:14px 20px;">
        {{ $users->appends(request()->query())->links() }}
    </div>
</div>
@endsection
