@extends('layouts.app')

@section('title', 'Payment Gateways')
@section('page_title', 'Payment Gateways')

@section('sidebar')
    @include('superadmin.partials.sidebar')
@endsection

@php
    $prefix = request()->is('admin*') ? 'admin.' : 'superadmin.';
@endphp

@section('content')
<div class="row g-4">
    <div class="col-6">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-money-bill-wave text-primary"></i>
                    <h3>Paystack</h3>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" checked>
                </div>
            </div>
            <div class="dashboard-card-body">
                <form action="{{ route($prefix.'settings.update') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Public Key</label>
                        <input type="text" name="settings[paystack_public]" class="form-control" value="pk_test_...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Secret Key</label>
                        <input type="password" name="settings[paystack_secret]" class="form-control" value="sk_test_...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Merchant Email</label>
                        <input type="email" name="settings[paystack_email]" class="form-control" value="merchant@buyniger.com">
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm">Save Paystack</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-6">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-globe-africa text-warning"></i>
                    <h3>Flutterwave</h3>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" checked>
                </div>
            </div>
            <div class="dashboard-card-body">
                <form action="{{ route($prefix.'settings.update') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Public Key</label>
                        <input type="text" name="settings[flutterwave_public]" class="form-control" value="FLWPUBK_TEST-...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Secret Key</label>
                        <input type="password" name="settings[flutterwave_secret]" class="form-control" value="FLWSECK_TEST-...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Encryption Key</label>
                        <input type="password" name="settings[flutterwave_enc]" class="form-control" value="...">
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm">Save Flutterwave</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3>Commission & Payout Settings</h3>
            </div>
            <div class="dashboard-card-body">
                <form action="{{ route($prefix.'settings.update') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Platform Commission (%)</label>
                            <input type="number" name="settings[commission_rate]" class="form-control" value="5" min="0" max="100">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Minimum Payout Amount</label>
                            <div class="input-group">
                                <span class="input-group-text">₦</span>
                                <input type="number" name="settings[min_payout]" class="form-control" value="5000">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Payout Schedule</label>
                            <select name="settings[payout_schedule]" class="form-select">
                                <option value="manual">Manual Request</option>
                                <option value="weekly">Weekly (Fridays)</option>
                                <option value="monthly">Monthly (1st)</option>
                            </select>
                        </div>
                        <div class="col-12 mt-3">
                            <button type="submit" class="btn btn-primary">Update Finance Settings</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
