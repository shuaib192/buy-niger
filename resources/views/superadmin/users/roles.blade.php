{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    Written by Shuaibu Abdulmumin
    View: Admin — User Roles & Permissions — Premium v2.0
--}}
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
                <div>
                    <h3 class="mb-1">User Roles & Access Levels</h3>
                    <p class="text-muted small mb-0">Manage platform permissions, system roles, and account privileges.</p>
                </div>
                <button class="btn btn-primary btn-sm rounded-pill px-3">
                    <i class="fas fa-plus me-1"></i> Add Custom Role
                </button>
            </div>
            <div class="dashboard-card-body p-0">
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th class="ps-4">Role Name</th>
                                <th>Permission Summary / Description</th>
                                <th>Assigned Users</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="ps-4">
                                    <span class="badge badge-danger"><i class="fas fa-user-shield me-1"></i> Super Admin</span>
                                </td>
                                <td><span class="text-dark fw-medium">Unrestricted global access to files, settings, payments, and AI systems.</span></td>
                                <td><span class="text-muted small">1 administrator</span></td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-outline-secondary rounded-pill px-3" disabled>Default</button>
                                </td>
                            </tr>
                            <tr>
                                <td class="ps-4">
                                    <span class="badge badge-primary"><i class="fas fa-user-gear me-1"></i> Admin</span>
                                </td>
                                <td><span class="text-dark fw-medium">Access to dashboards, products, user lists, and support chats. Cannot modify payouts.</span></td>
                                <td><span class="text-muted small">2 moderators</span></td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-outline-primary rounded-pill px-3">Edit Rules</button>
                                </td>
                            </tr>
                            <tr>
                                <td class="ps-4">
                                    <span class="badge badge-success"><i class="fas fa-store me-1"></i> Vendor</span>
                                </td>
                                <td><span class="text-dark fw-medium">Access to personal store dashboard, inventory settings, messages, and payout claims.</span></td>
                                <td><span class="text-muted small">15 storefronts</span></td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-outline-primary rounded-pill px-3">Edit Rules</button>
                                </td>
                            </tr>
                            <tr>
                                <td class="ps-4">
                                    <span class="badge badge-info"><i class="fas fa-user me-1"></i> Customer</span>
                                </td>
                                <td><span class="text-dark fw-medium">Standard customer account. Shopping, cart checkout, reviews, and private disputes.</span></td>
                                <td><span class="text-muted small">450 consumers</span></td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-outline-primary rounded-pill px-3">Edit Rules</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
