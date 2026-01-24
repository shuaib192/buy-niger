{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    Written by Shuaibu Abdulmumin (08122598372, 07049906420)
    
    View: Admin Dashboard
--}}
@extends('layouts.app')

@section('title', 'Admin Dashboard')
@section('page_title', 'Dashboard')

@section('sidebar')
    <div class="nav-section">
        <div class="nav-section-title">Main</div>
        <a href="{{ route('admin.dashboard') }}" class="nav-link active">
            <i class="fas fa-th-large"></i>
            <span class="nav-label">Dashboard</span>
        </a>
    </div>

    <div class="nav-section">
        <div class="nav-section-title">Management</div>
        <a href="#" class="nav-link">
            <i class="fas fa-store"></i>
            <span class="nav-label">Vendors</span>
            <span class="nav-badge">5</span>
        </a>
        <a href="#" class="nav-link">
            <i class="fas fa-box"></i>
            <span class="nav-label">Products</span>
        </a>
        <a href="#" class="nav-link">
            <i class="fas fa-shopping-cart"></i>
            <span class="nav-label">Orders</span>
        </a>
        <a href="#" class="nav-link">
            <i class="fas fa-users"></i>
            <span class="nav-label">Customers</span>
        </a>
    </div>

    <div class="nav-section">
        <div class="nav-section-title">Content</div>
        <a href="#" class="nav-link">
            <i class="fas fa-tags"></i>
            <span class="nav-label">Categories</span>
        </a>
        <a href="#" class="nav-link">
            <i class="fas fa-star"></i>
            <span class="nav-label">Reviews</span>
        </a>
    </div>
@endsection

@section('content')
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon blue">
                <i class="fas fa-store"></i>
            </div>
            <div class="stat-info">
                <h3>56</h3>
                <p>Active Vendors</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon green">
                <i class="fas fa-box"></i>
            </div>
            <div class="stat-info">
                <h3>1,234</h3>
                <p>Products</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon orange">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <div class="stat-info">
                <h3>892</h3>
                <p>Orders</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon purple">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <h3>4,567</h3>
                <p>Customers</p>
            </div>
        </div>
    </div>

    <div class="dashboard-card col-12">
        <div class="dashboard-card-header">
            <h3>Pending Approvals</h3>
        </div>
        <div class="dashboard-card-body">
            <p style="color: var(--secondary-500);">Manage vendor approvals, product moderation, and customer support.</p>
        </div>
    </div>
@endsection
