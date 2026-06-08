@extends('layouts.app')

@section('title', 'Roles & Permissions')
@section('page_title', 'Roles & Permissions')

@section('sidebar')
    @include('superadmin.partials.sidebar')
@endsection

@section('content')
{{-- CSS classes in dashboard.css (admin-page-card, admin-table, btn-xs, etc.) --}}

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
