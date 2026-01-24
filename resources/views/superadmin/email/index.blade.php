@extends('layouts.app')

@section('title', 'Email Configuration')
@section('page_title', 'Email Configuration')

@section('sidebar')
    @include('superadmin.partials.sidebar')
@endsection

@php
    $prefix = request()->is('admin*') ? 'admin.' : 'superadmin.';
@endphp

@section('content')
<div class="row g-4">
    <div class="col-8">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3>SMTP Settings</h3>
            </div>
            <div class="dashboard-card-body">
                <form action="{{ route($prefix.'settings.update') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Mail Driver</label>
                            <select name="settings[mail_driver]" class="form-select">
                                <option value="smtp">SMTP</option>
                                <option value="sendmail">Sendmail</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Mail Host</label>
                            <input type="text" name="settings[mail_host]" class="form-control" value="smtp.mailtrap.io">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Mail Port</label>
                            <input type="text" name="settings[mail_port]" class="form-control" value="2525">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Mail Encryption</label>
                            <input type="text" name="settings[mail_encryption]" class="form-control" value="tls">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Mail Username</label>
                            <input type="text" name="settings[mail_username]" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Mail Password</label>
                            <input type="password" name="settings[mail_password]" class="form-control">
                        </div>
                        <div class="col-12 mt-4">
                            <button type="submit" class="btn btn-primary">Save Configuration</button>
                            <button type="button" class="btn btn-secondary ms-2">Test Connection</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-4">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3>Email Templates</h3>
            </div>
            <div class="dashboard-card-body">
                <div class="list-group list-group-flush">
                    <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        Welcome Email
                        <span class="badge bg-success rounded-pill">Active</span>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        Order Confirmation
                        <span class="badge bg-success rounded-pill">Active</span>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        Vendor Approval
                        <span class="badge bg-success rounded-pill">Active</span>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        Password Reset
                        <span class="badge bg-success rounded-pill">Active</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
