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
            <div class="dashboard-card col-8">
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
                        <input type="email" name="settings[contact_email]" class="form-control" value="{{ \App\Models\SystemSetting::get('support_email', 'support@buyniger.com') }}">
                    </div>

                    <div class="form-group mt-4">
                        <label class="form-label">Contact Phone</label>
                        <input type="text" name="settings[phone]" class="form-control" value="{{ \App\Models\SystemSetting::get('phone', '08122598372') }}">
                    </div>
                </div>
            </div>

            <!-- Feature Toggles -->
            <div class="dashboard-card col-4">
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
                            <label class="switch">
                                <input type="checkbox" name="features[{{ $feature->feature }}]" {{ $feature->is_enabled ? 'checked' : '' }}>
                                <span class="slider round"></span>
                            </label>
                        </div>
                    @endforeach
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

    <style>
        .settings-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: var(--spacing-md);
        }

        .feature-toggle-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: var(--spacing-md) 0;
            border-bottom: 1px solid var(--secondary-100);
        }

        .feature-toggle-item:last-child {
            border-bottom: none;
        }

        .feature-info {
            flex: 1;
        }

        .feature-info strong {
            display: block;
            font-size: 0.875rem;
        }

        .feature-info small {
            color: var(--secondary-500);
            font-size: 0.75rem;
        }

        .switch {
            position: relative;
            display: inline-block;
            width: 44px;
            height: 24px;
            flex-shrink: 0;
        }

        .switch input { 
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: var(--secondary-200);
            transition: .4s;
            border-radius: 34px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .slider {
            background-color: var(--primary-600);
        }

        input:checked + .slider:before {
            transform: translateX(20px);
        }

        @media (max-width: 768px) {
            .settings-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection
