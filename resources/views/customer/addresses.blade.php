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

{{-- CSS classes in dashboard.css (address-list, address-item, default-badge, empty-state, form-control, etc.) --}}
@endsection
