{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    View: Admin — Create Email Campaign — Premium v2.0
--}}
@extends('layouts.app')

@section('title', 'Create Email Campaign')
@section('page_title', 'Create Email Campaign')

@section('sidebar')
    @include('superadmin.partials.sidebar')
@endsection

@push('styles')
<style>
.form-card {
    background: var(--surface);
    border: 1.5px solid var(--border-color);
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);
    margin-bottom: 24px;
}
.form-card-header {
    padding: 20px 24px;
    border-bottom: 1px solid var(--border-color);
    background: linear-gradient(135deg, rgba(2,132,199,.02), rgba(14,165,233,.02));
    display: flex;
    align-items: center;
    gap: 12px;
}
.form-card-header-icon {
    width: 36px; height: 36px; border-radius: 10px;
    background: rgba(14, 165, 233, 0.1); color: #0284c7;
    display: flex; align-items: center; justify-content: center; font-size: 1rem;
}
.form-card-title {
    font-size: .95rem; font-weight: 800; color: var(--text-primary); font-family: 'Outfit', sans-serif;
}
.form-card-desc {
    font-size: .75rem; color: var(--text-muted); margin-top: 2px;
}
.form-card-body {
    padding: 24px;
}

.custom-form-group {
    margin-bottom: 20px;
}
.custom-label {
    display: block; margin-bottom: 8px;
    font-size: .78rem; font-weight: 700; color: var(--text-secondary);
    text-transform: uppercase; letter-spacing: .04em;
}
.custom-input {
    width: 100%; padding: 11px 14px;
    border: 1.5px solid var(--border-color);
    border-radius: 12px; font-size: .875rem;
    color: var(--text-primary); background: white;
    transition: all .15s; box-sizing: border-box;
}
.custom-input:focus {
    outline: none; border-color: #0284c7;
    box-shadow: 0 0 0 3px rgba(14,165,233,.08);
}
.custom-select {
    width: 100%; padding: 11px 14px;
    border: 1.5px solid var(--border-color);
    border-radius: 12px; font-size: .875rem;
    color: var(--text-primary); background: white;
    cursor: pointer; transition: all .15s;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 12px center;
    padding-right: 36px;
    box-sizing: border-box;
}
.custom-select:focus { outline: none; border-color: #0284c7; box-shadow: 0 0 0 3px rgba(14,165,233,.08); }

.custom-form-text {
    font-size: .72rem; color: var(--text-muted); margin-top: 5px;
}

.form-actions {
    display: flex; justify-content: flex-end; gap: 12px;
    padding-top: 20px; border-top: 1px solid var(--border-color);
}
.btn-submit-campaign {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 11px 22px; border-radius: 12px; font-size: .875rem;
    font-weight: 700; background: linear-gradient(135deg, #0284c7, #0ea5e9);
    color: white; border: none; cursor: pointer; transition: all .2s;
    text-decoration: none;
}
.btn-submit-campaign:hover { transform: translateY(-1px); box-shadow: 0 5px 18px rgba(14,165,233,.3); }

.btn-cancel-campaign {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 11px 22px; border-radius: 12px; font-size: .875rem;
    font-weight: 700; background: white; color: var(--text-secondary);
    border: 1.5px solid var(--border-color); cursor: pointer; transition: all .2s;
    text-decoration: none;
}
.btn-cancel-campaign:hover { border-color: #0284c7; color: #0284c7; }
</style>
@endpush

@section('content')
<div class="row justify-content-center">
    <div class="col-md-9">
        {{-- Breadcrumb back link --}}
        <div class="mb-4">
            <a href="{{ route('superadmin.email.campaigns.index') }}" class="text-decoration-none text-muted small" style="font-weight: 600;">
                <i class="fas fa-arrow-left me-1"></i> Back to Campaigns
            </a>
        </div>

        <div class="form-card">
            <div class="form-card-header">
                <div class="form-card-header-icon"><i class="fas fa-bullhorn"></i></div>
                <div>
                    <div class="form-card-title">Create Email Campaign</div>
                    <div class="form-card-desc">Prepare a newsletter blast or marketing notification.</div>
                </div>
            </div>
            <div class="form-card-body">
                <form action="{{ route('superadmin.email.campaigns.store') }}" method="POST">
                    @csrf
                    
                    <div class="custom-form-group">
                        <label class="custom-label">Campaign Name (Internal)</label>
                        <input type="text" name="name" class="custom-input" placeholder="e.g. June Vendor Promo Announcement" required>
                        <div class="custom-form-text">Used for administration and reports. Recipients will not see this name.</div>
                    </div>

                    <div class="custom-form-group">
                        <label class="custom-label">Email Subject Line</label>
                        <input type="text" name="subject" class="custom-input" placeholder="e.g. Boost your sales with BuyNiger's new features!" required>
                        <div class="custom-form-text">The subject line visible in the inbox of your recipients.</div>
                    </div>

                    <div class="custom-form-group">
                        <label class="custom-label">Select Base Template</label>
                        <select name="template_id" class="custom-select" required>
                            <option value="">-- Select a template to populate email body --</option>
                            @foreach($templates as $template)
                                <option value="{{ $template->id }}">{{ $template->name }}</option>
                            @endforeach
                        </select>
                        <div class="custom-form-text">Choose the HTML template to use for this campaign.</div>
                    </div>

                    <div class="custom-form-group">
                        <label class="custom-label">Target Audience Segment</label>
                        <select name="target_audience" class="custom-select" required>
                            <option value="all">All Registered Users</option>
                            <option value="customers">Customers Only</option>
                            <option value="vendors">Vendors Only</option>
                        </select>
                        <div class="custom-form-text">Define who will receive this broadcast campaign.</div>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('superadmin.email.campaigns.index') }}" class="btn-cancel-campaign">Cancel</a>
                        <button type="submit" class="btn-submit-campaign">
                            <i class="fas fa-bullhorn mr-1"></i> Create Draft Campaign
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
