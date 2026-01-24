@extends('layouts.app')

@section('title', 'Roles & Permissions')
@section('page_title', 'Roles & Permissions')

@section('sidebar')
    @include('superadmin.partials.sidebar')
@endsection

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3>User Roles</h3>
                <button class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Add New Role
                </button>
            </div>
            <div class="dashboard-card-body">
                <table class="table">
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
                            <td><button class="btn btn-sm btn-outline-secondary" disabled>Edit</button></td>
                        </tr>
                        <tr>
                            <td><span class="badge bg-primary">Admin</span></td>
                            <td>Platform moderator</td>
                            <td>2</td>
                            <td><button class="btn btn-sm btn-outline-secondary">Edit</button></td>
                        </tr>
                        <tr>
                            <td><span class="badge bg-success">Vendor</span></td>
                            <td>Store owner</td>
                            <td>15</td>
                            <td><button class="btn btn-sm btn-outline-secondary">Edit</button></td>
                        </tr>
                        <tr>
                            <td><span class="badge bg-info">Customer</span></td>
                            <td>Standard user</td>
                            <td>450</td>
                            <td><button class="btn btn-sm btn-outline-secondary">Edit</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
