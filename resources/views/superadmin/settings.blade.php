{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    Written by Shuaibu Abdulmumin (08122598372, 07049906420)
    
    View: Super Admin - System Settings
--}}
@extends('layouts.app')

@section('title', 'System Settings')
@section('page_title', 'System Settings')

@section('sidebar')
    @include('superadmin.partials.sidebar')
@endsection

@php
    $prefix = request()->is('admin*') ? 'admin.' : 'superadmin.';
@endphp

@section('content')
    <form action="{{ route($prefix.'settings.update') }}" method="POST">
        @csrf
        <div class="row g-4">
            <!-- General Settings -->
            <div class="col-8">
                <div class="dashboard-card">
                    <div class="dashboard-card-header">
                        <h3>General Configuration</h3>
                    </div>
                    <div class="dashboard-card-body">
                        <div class="form-group mb-4">
                            <label class="form-label">Application Name</label>
                            <input type="text" name="settings[app_name]" class="form-control" value="{{ \App\Models\SystemSetting::get('site_name', 'BuyNiger') }}">
                        </div>

                        <div class="settings-row">
                            <div class="settings-col">
                                <label class="form-label">Currency Symbol</label>
                                <input type="text" name="settings[currency_symbol]" class="form-control" value="{{ \App\Models\SystemSetting::get('currency_symbol', '₦') }}">
                            </div>
                            <div class="settings-col">
                                <label class="form-label">Currency Code</label>
                                <input type="text" name="settings[currency_code]" class="form-control" value="{{ \App\Models\SystemSetting::get('currency', 'NGN') }}">
                            </div>
                        </div>

                        <div class="settings-row mt-4">
                            <div class="settings-col">
                                <label class="form-label">Timezone</label>
                                <select name="settings[timezone]" class="form-select">
                                    <option value="Africa/Lagos" selected>Africa/Lagos</option>
                                    <option value="UTC">UTC</option>
                                </select>
                            </div>
                            <div class="settings-col">
                                <label class="form-label">Language</label>
                                <select name="settings[language]" class="form-select">
                                    <option value="en" selected>English</option>
                                    <option value="fr">French</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group mt-4">
                            <label class="form-label">Site Logo</label>
                            <input type="file" name="logo" class="form-control">
                            <small class="text-muted">Recommended: 200x50px PNG</small>
                        </div>

                        <div class="form-group mt-4">
                            <label class="form-label">Contact Email</label>
                            <input type="email" name="settings[contact_email]" class="form-control" value="{{ \App\Models\SystemSetting::get('support_email', 'infor@buyniger.com') }}">
                        </div>

                        <div class="form-group mt-4">
                            <label class="form-label">Contact Phone</label>
                            <input type="text" name="settings[phone]" class="form-control" value="{{ \App\Models\SystemSetting::get('phone', '09019194418') }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Feature Toggles -->
            <div class="col-4">
                <div class="dashboard-card">
                    <div class="dashboard-card-header">
                        <h3>Feature Toggles</h3>
                    </div>
                    <div class="dashboard-card-body">
                        @foreach($features as $feature)
                            <div class="feature-toggle-item">
                                <div class="feature-info">
                                    <strong>{{ $feature->display_name }}</strong>
                                    @if($feature->description)
                                        <small>{{ $feature->description }}</small>
                                    @endif
                                </div>
                                <label class="toggle-switch">
                                    <input type="checkbox" name="features[{{ $feature->feature }}]" {{ $feature->is_enabled ? 'checked' : '' }}>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Save Button -->
            <div class="col-12 mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </div>
        </div>
    </form>

    <!-- System Optimization Section -->
    <div class="row g-4 mt-4">
        <div class="col-12">
            <div class="dashboard-card">
                <div class="dashboard-card-header">
                    <h3><i class="fas fa-rocket text-primary"></i> System Optimization</h3>
                </div>
                <div class="dashboard-card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h5 class="mb-1">Optimize Cache & Performance</h5>
                            <p class="text-muted small mb-0">This will clear all application caches (Config, Routes, Views, and Homepage) and rebuild them. Recommended after making configuration changes or if the site feels slow.</p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <form action="{{ route($prefix.'optimize') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-sync-alt"></i> Optimize Now
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
