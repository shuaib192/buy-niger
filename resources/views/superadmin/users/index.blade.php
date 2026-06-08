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

{{-- CSS classes in dashboard.css (admin-page-card, admin-table, btn-xs, etc.) --}}

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
