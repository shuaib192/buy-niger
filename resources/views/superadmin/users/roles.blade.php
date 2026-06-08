@extends('layouts.app')

@section('title', 'Roles & Permissions')
@section('page_title', 'Roles & Permissions')

@section('sidebar')
    @include('superadmin.partials.sidebar')
@endsection

@section('content')
<style>
    /* ── Roles & Permissions: zero-overflow layout ── */
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
    .btn-xs-primary { border-color: #3b82f6; color: #fff; background: #3b82f6; }
    .btn-xs-primary:hover { background: #2563eb; }
    .btn-xs-sec   { border-color: #e2e8f0; color: #475569; background: #f8fafc; }
    .btn-xs-sec:hover   { background: #e2e8f0; }
</style>

<div class="admin-page-card">
    {{-- Header --}}
    <div class="admin-page-header">
        <div class="admin-page-header-left">
            <h2><i class="fas fa-shield-alt" style="color:#3b82f6;"></i> User Roles</h2>
        </div>
        <button class="btn-xs btn-xs-primary" disabled>
            <i class="fas fa-plus"></i> Add New Role
        </button>
    </div>

    {{-- Table --}}
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Role Name</th>
                    <th>Description</th>
                    <th>Users Count</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><span class="badge bg-danger">Super Admin</span></td>
                    <td>Full system access</td>
                    <td>1</td>
                    <td><button class="btn-xs btn-xs-sec" disabled>Edit</button></td>
                </tr>
                <tr>
                    <td><span class="badge bg-primary">Admin</span></td>
                    <td>Platform moderator</td>
                    <td>2</td>
                    <td><button class="btn-xs btn-xs-sec" disabled>Edit</button></td>
                </tr>
                <tr>
                    <td><span class="badge bg-success">Vendor</span></td>
                    <td>Store owner</td>
                    <td>15</td>
                    <td><button class="btn-xs btn-xs-sec" disabled>Edit</button></td>
                </tr>
                <tr>
                    <td><span class="badge bg-info">Customer</span></td>
                    <td>Standard user</td>
                    <td>450</td>
                    <td><button class="btn-xs btn-xs-sec" disabled>Edit</button></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
