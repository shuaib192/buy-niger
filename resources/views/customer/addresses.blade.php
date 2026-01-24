{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    Written by Shuaibu Abdulmumin (08122598372, 07049906420)
    
    View: Customer Addresses
--}}
@extends('layouts.app')

@section('title', 'My Addresses')
@section('page_title', 'Saved Addresses')

@section('sidebar')
    @include('customer.partials.sidebar')
@endsection

@section('content')
@if(session('success'))
    <div class="alert alert-success mb-4">{{ session('success') }}</div>
@endif

<div class="row">
    <!-- Saved Addresses -->
    <div class="col-lg-8 mb-4">
        <div class="dashboard-card h-100">
            <div class="dashboard-card-header">
                <h3>My Addresses</h3>
            </div>
            <div class="dashboard-card-body">
                @if($addresses->count() > 0)
                    <div class="address-list">
                        @foreach($addresses as $address)
                            <div class="address-item {{ $address->is_default ? 'default' : '' }}">
                                <div class="address-info">
                                    @if($address->is_default)
                                        <span class="default-badge">Default</span>
                                    @endif
                                    <strong>{{ $address->first_name }} {{ $address->last_name }}</strong>
                                    <p>{{ $address->address_line_1 }}</p>
                                    @if($address->address_line_2)
                                        <p>{{ $address->address_line_2 }}</p>
                                    @endif
                                    <p>{{ $address->city }}, {{ $address->state }}</p>
                                    <p><i class="fas fa-phone"></i> {{ $address->phone }}</p>
                                </div>
                                <div class="address-actions">
                                    @if(!$address->is_default)
                                        <form action="{{ route('customer.addresses.default', $address->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-secondary">Set Default</button>
                                        </form>
                                    @endif
                                    <form action="{{ route('customer.addresses.delete', $address->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this address?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <i class="fas fa-map-marker-alt"></i>
                        <p>No saved addresses yet.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Add New Address -->
    <div class="col-lg-4 mb-4">
        <div class="dashboard-card h-100">
            <div class="dashboard-card-header">
                <h3>Add New Address</h3>
            </div>
            <div class="dashboard-card-body">
                <form action="{{ route('customer.addresses.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label>First Name *</label>
                        <input type="text" name="first_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Last Name *</label>
                        <input type="text" name="last_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Phone Number *</label>
                        <input type="text" name="phone" class="form-control" placeholder="08012345678" required>
                    </div>
                    <div class="form-group">
                        <label>Street Address *</label>
                        <input type="text" name="address_line_1" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Apartment, Suite (optional)</label>
                        <input type="text" name="address_line_2" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>City *</label>
                        <input type="text" name="city" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>State *</label>
                        <select name="state" class="form-control" required>
                            <option value="">Select State</option>
                            @foreach(['Abia','Adamawa','Akwa Ibom','Anambra','Bauchi','Bayelsa','Benue','Borno','Cross River','Delta','Ebonyi','Edo','Ekiti','Enugu','FCT','Gombe','Imo','Jigawa','Kaduna','Kano','Katsina','Kebbi','Kogi','Kwara','Lagos','Nasarawa','Niger','Ogun','Ondo','Osun','Oyo','Plateau','Rivers','Sokoto','Taraba','Yobe','Zamfara'] as $state)
                                <option value="{{ $state }}">{{ $state }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-full">
                        <i class="fas fa-plus"></i> Add Address
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .address-list {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }
    .address-item {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: 16px;
        border: 1px solid var(--secondary-100);
        border-radius: 12px;
        background: var(--secondary-50);
    }
    .address-item.default {
        border-color: var(--primary-200);
        background: var(--primary-50);
    }
    .default-badge {
        background: var(--primary-500);
        color: white;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 700;
        margin-bottom: 4px;
        display: inline-block;
    }
    .address-info strong {
        display: block;
        margin-bottom: 4px;
    }
    .address-info p {
        margin: 0;
        font-size: 14px;
        color: var(--secondary-600);
    }
    .address-actions {
        display: flex;
        gap: 8px;
    }
    .inline { display: inline; }
    .btn-danger {
        background: var(--danger);
        color: white;
    }
    .form-group {
        margin-bottom: 12px;
    }
    .form-group label {
        display: block;
        font-weight: 600;
        margin-bottom: 6px;
        font-size: 13px;
    }
    .form-control {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid var(--secondary-200);
        border-radius: 8px;
        font-size: 14px;
    }
    .empty-state {
        text-align: center;
        padding: 40px;
        color: var(--secondary-400);
    }
    .empty-state i {
        font-size: 2rem;
        margin-bottom: 8px;
    }
    .alert-success {
        background: #d1fae5;
        color: #047857;
        padding: 12px 16px;
        border-radius: 10px;
    }
    .mb-4 { margin-bottom: 1.5rem; }
</style>
@endsection
