@extends('layouts.app')

@section('title', 'User Management')
@section('page_title', 'User Management')

@section('sidebar')
    @include('superadmin.partials.sidebar')
@endsection

@section('content')
@php
    $prefix = request()->is('admin*') ? 'admin.' : 'superadmin.';
@endphp

<style>
    /* ── Users page: zero-overflow layout ── */
    .admin-page-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 1px 4px rgba(0,0,0,.06);
        overflow: hidden;
        width: 100%;
        box-sizing: border-box;
    }
    .admin-page-header {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 16px 20px;
        border-bottom: 1px solid #f1f5f9;
    }
    .admin-page-header-left {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-shrink: 0;
    }
    .admin-page-header-left h2 {
        font-size: 1rem;
        font-weight: 700;
        margin: 0;
        color: #0f172a;
    }
    .admin-filter-form {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        align-items: center;
    }
    .admin-filter-form select,
    .admin-filter-form input[type="text"] {
        font-size: 0.8125rem;
        padding: 7px 12px;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        background: #f8fafc;
        color: #334155;
        outline: none;
        transition: border-color .2s;
        min-width: 0;
    }
    .admin-filter-form select:focus,
    .admin-filter-form input[type="text"]:focus {
        border-color: #3b82f6;
        background: #fff;
    }
    .admin-filter-form select { min-width: 110px; }
    .admin-filter-form input[type="text"] { min-width: 140px; flex: 1 1 140px; }

    /* ── Responsive table wrapper ── */
    .admin-table-wrap {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    .admin-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 560px; /* triggers scroll on tiny screens only */
    }
    .admin-table thead th {
        padding: 10px 16px;
        font-size: 0.6875rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: #94a3b8;
        background: #f8fafc;
        white-space: nowrap;
        border-bottom: 1px solid #f1f5f9;
    }
    .admin-table tbody td {
        padding: 12px 16px;
        font-size: 0.8125rem;
        color: #334155;
        border-bottom: 1px solid #f8fafc;
        vertical-align: middle;
    }
    .admin-table tbody tr:last-child td { border-bottom: none; }
    .admin-table tbody tr:hover { background: #f8fafc; }

    .user-cell {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .user-avatar {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        object-fit: cover;
        flex-shrink: 0;
    }
    .user-name {
        font-weight: 600;
        color: #0f172a;
        white-space: nowrap;
    }
    .action-group {
        display: flex;
        gap: 6px;
        flex-wrap: nowrap;
    }
    .btn-xs {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 5px 10px;
        font-size: 0.75rem;
        font-weight: 600;
        border-radius: 8px;
        border: 1.5px solid transparent;
        cursor: pointer;
        white-space: nowrap;
        transition: all .15s;
        text-decoration: none;
    }
    .btn-xs-warn  { border-color: #f59e0b; color: #92400e; background: #fef3c7; }
    .btn-xs-warn:hover  { background: #fde68a; }
    .btn-xs-ok    { border-color: #10b981; color: #065f46; background: #d1fae5; }
    .btn-xs-ok:hover    { background: #a7f3d0; }
    .btn-xs-danger{ border-color: #ef4444; color: #991b1b; background: #fee2e2; }
    .btn-xs-danger:hover{ background: #fecaca; }
    .btn-xs-sec   { border-color: #e2e8f0; color: #475569; background: #f8fafc; }
    .btn-xs-sec:hover   { background: #e2e8f0; }

    .pager-wrap {
        padding: 14px 20px;
        border-top: 1px solid #f1f5f9;
        overflow-x: auto;
        width: 100%;
    }
    .empty-state { padding: 48px 20px; text-align: center; color: #94a3b8; }
    .empty-state i { font-size: 2rem; margin-bottom: 10px; display: block; }

    @media (max-width: 640px) {
        .admin-page-header {
            flex-direction: column;
            align-items: stretch;
            gap: 12px;
            padding: 12px 14px;
        }
        .admin-page-header-left {
            justify-content: space-between;
            width: 100%;
        }
        .admin-filter-form {
            width: 100%;
            flex-direction: column;
            align-items: stretch;
            gap: 8px;
        }
        .admin-filter-form select,
        .admin-filter-form input[type="text"],
        .admin-filter-form button {
            width: 100%;
            margin: 0;
        }
    }
</style>

<div class="admin-page-card">
    {{-- Header --}}
    <div class="admin-page-header">
        <div class="admin-page-header-left">
            <h2><i class="fas fa-users" style="color:#3b82f6;"></i> All Users</h2>
            <a href="{{ route($prefix.'users.export', request()->all()) }}"
               class="btn-xs btn-xs-ok">
                <i class="fas fa-file-csv"></i> Export
            </a>
        </div>

        <form action="{{ route($prefix.'users') }}" method="GET"
              class="admin-filter-form">
            <select name="role" onchange="this.form.submit()">
                <option value="">All Roles</option>
                <option value="1" {{ request('role') == '1' ? 'selected' : '' }}>Super Admin</option>
                <option value="2" {{ request('role') == '2' ? 'selected' : '' }}>Admin</option>
                <option value="3" {{ request('role') == '3' ? 'selected' : '' }}>Vendor</option>
                <option value="4" {{ request('role') == '4' ? 'selected' : '' }}>Customer</option>
            </select>
            <input type="text" name="search"
                   placeholder="Search users…"
                   value="{{ request('search') }}">
            <button type="submit" class="btn-xs btn-xs-sec">
                <i class="fas fa-search"></i> Search
            </button>
        </form>
    </div>

    {{-- Table --}}
    <div class="admin-table-wrap">
        <table class="admin-table">
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
                            <div class="user-cell">
                                <img src="{{ $user->avatar_url }}" class="user-avatar" alt="">
                                <span class="user-name">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td>
                            @if($user->role_id == 1)
                                <span class="badge" style="background:#ede9fe;color:#5b21b6;">Super Admin</span>
                            @elseif($user->role_id == 2)
                                <span class="badge" style="background:#dbeafe;color:#1d4ed8;">Admin</span>
                            @elseif($user->role_id == 3)
                                <span class="badge" style="background:#ffedd5;color:#c2410c;">Vendor</span>
                            @else
                                <span class="badge" style="background:#dcfce7;color:#15803d;">Customer</span>
                            @endif
                        </td>
                        <td style="max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                            {{ $user->email }}
                        </td>
                        <td>
                            @if($user->is_active)
                                <span class="badge" style="background:#dcfce7;color:#15803d;">Active</span>
                            @else
                                <span class="badge" style="background:#fee2e2;color:#dc2626;">Banned</span>
                            @endif
                        </td>
                        <td style="white-space:nowrap;">{{ $user->created_at->format('M d, Y') }}</td>
                        <td>
                            <div class="action-group">
                                @if($user->id !== auth()->id())
                                    <form action="{{ route($prefix.'users.ban', $user) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                                class="btn-xs {{ $user->is_active ? 'btn-xs-warn' : 'btn-xs-ok' }}"
                                                onclick="return confirm('This action is immediate.')">
                                            {{ $user->is_active ? 'Ban' : 'Activate' }}
                                        </button>
                                    </form>
                                    <form action="{{ route($prefix.'users.destroy', $user) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn-xs btn-xs-danger"
                                                onclick="return confirm('Delete this user? This cannot be undone.')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                                <a href="{{ route($prefix.'users.show', $user) }}"
                                   class="btn-xs btn-xs-sec" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <i class="fas fa-users-slash"></i>
                                No users found.
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="pager-wrap">
        {{ $users->links() }}
    </div>
</div>
@endsection
