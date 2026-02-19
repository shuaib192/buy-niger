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
<div class="row">
    <div class="col-12">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <div class="d-flex align-items-center gap-3">
                        <h3>All Users</h3>
                        <a href="{{ route($prefix.'users.export', request()->all()) }}" class="btn btn-success btn-sm">
                            <i class="fas fa-file-csv"></i> Export CSV
                        </a>
                    </div>
                    <form action="{{ route($prefix.'users') }}" method="GET" class="d-flex gap-2">
                        <select name="role" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">All Roles</option>
                            <option value="1" {{ request('role') == '1' ? 'selected' : '' }}>Super Admin</option>
                            <option value="2" {{ request('role') == '2' ? 'selected' : '' }}>Admin</option>
                            <option value="3" {{ request('role') == '3' ? 'selected' : '' }}>Vendor</option>
                            <option value="4" {{ request('role') == '4' ? 'selected' : '' }}>Customer</option>
                        </select>
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Search users..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary btn-sm">Search</button>
                    </form>
                </div>
            </div>
            <div class="dashboard-card-body">
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
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="{{ $user->avatar_url }}" class="rounded-circle" width="32" height="32">
                                            <span class="fw-bold">{{ $user->name }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        @if($user->role_id == 1) <span class="badge badge-purple">Super Admin</span>
                                        @elseif($user->role_id == 2) <span class="badge badge-blue">Admin</span>
                                        @elseif($user->role_id == 3) <span class="badge badge-orange">Vendor</span>
                                        @else <span class="badge badge-green">Customer</span>
                                        @endif
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @if($user->is_active)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-danger">Banned</span>
                                        @endif
                                    </td>
                                    <td>{{ $user->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            @if($user->id !== auth()->id())
                                                <form action="{{ route($prefix.'users.ban', $user) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm {{ $user->is_active ? 'btn-outline-danger' : 'btn-outline-success' }}" onclick="return confirm('Note: This action is immediate.')">
                                                        {{ $user->is_active ? 'Ban' : 'Activate' }}
                                                    </button>
                                                </form>
                                            @endif
                                            <a href="{{ route($prefix.'users.show', $user) }}" class="btn btn-sm btn-secondary" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">No users found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
