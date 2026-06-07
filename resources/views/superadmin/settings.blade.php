{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    Written by Shuaibu Abdulmumin
    View: Super Admin — System Settings — Premium v2.0
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

@push('styles')
<style>
.settings-hero {
    background: linear-gradient(135deg, #18181b 0%, #27272a 50%, #3f3f46 100%);
    border-radius: 18px;
    padding: 28px 32px;
    margin-bottom: 24px;
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
}
.settings-hero::before {
    content: '';
    position: absolute;
    top: -60px; right: -40px;
    width: 200px; height: 200px;
    background: rgba(161,161,170,.07);
    border-radius: 50%;
}
.settings-hero-content { position: relative; z-index: 1; }
.settings-hero-content h2 {
    color: white; font-size: 1.375rem; font-weight: 800;
    font-family: 'Outfit', sans-serif; margin-bottom: 4px;
}
.settings-hero-content p { color: rgba(255,255,255,.55); font-size: .875rem; margin: 0; }
.settings-hero-version {
    position: relative; z-index: 1;
    display: inline-flex; align-items: center; gap: 7px;
    padding: 7px 16px; border-radius: 20px;
    background: rgba(255,255,255,.1); color: rgba(255,255,255,.8);
    font-size: .82rem; font-weight: 600; border: 1px solid rgba(255,255,255,.15);
}

.settings-layout {
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: 20px;
    align-items: start;
}
@media (max-width: 960px) { .settings-layout { grid-template-columns: 1fr; } }

/* Section cards */
.settings-section {
    background: var(--surface);
    border: 1.5px solid var(--border-color);
    border-radius: 20px; overflow: hidden;
    margin-bottom: 20px; transition: box-shadow .2s;
}
.settings-section:hover { box-shadow: 0 4px 20px rgba(0,0,0,.06); }
.settings-section:last-child { margin-bottom: 0; }
.ss-header {
    padding: 18px 24px;
    border-bottom: 1px solid var(--border-color);
    display: flex; align-items: center; gap: 13px;
    background: linear-gradient(135deg, rgba(100,116,139,.03), rgba(71,85,105,.03));
}
.ss-icon {
    width: 40px; height: 40px; border-radius: 11px;
    display: flex; align-items: center; justify-content: center;
    font-size: .9rem; flex-shrink: 0;
}
.ss-icon.slate  { background: rgba(100,116,139,.12); color: #475569; }
.ss-icon.indigo { background: rgba(79,70,229,.1);    color: #4338ca; }
.ss-icon.amber  { background: rgba(245,158,11,.1);   color: #d97706; }
.ss-icon.rose   { background: rgba(244,63,94,.1);    color: #be123c; }
.ss-icon.teal   { background: rgba(20,184,166,.1);   color: #0f766e; }
.ss-title { font-size: .9rem; font-weight: 800; color: var(--text-primary); font-family: 'Outfit', sans-serif; }
.ss-desc  { font-size: .75rem; color: var(--text-muted); margin-top: 1px; }
.ss-body  { padding: 24px; }

/* Fields */
.sfield-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px; }
@media (max-width: 600px) { .sfield-grid { grid-template-columns: 1fr; } }
.sfield-full { margin-bottom: 16px; }
.sfield-full:last-child { margin-bottom: 0; }
.sf-label {
    display: block; margin-bottom: 6px;
    font-size: .78rem; font-weight: 700; color: var(--text-secondary);
    text-transform: uppercase; letter-spacing: .04em;
}
.sf-input {
    width: 100%; padding: 11px 14px;
    border: 1.5px solid var(--border-color);
    border-radius: 12px; font-size: .875rem;
    color: var(--text-primary); background: white;
    transition: all .15s; box-sizing: border-box;
}
.sf-input:focus {
    outline: none; border-color: #4f46e5;
    box-shadow: 0 0 0 3px rgba(79,70,229,.08);
}
.sf-select {
    width: 100%; padding: 11px 14px;
    border: 1.5px solid var(--border-color);
    border-radius: 12px; font-size: .875rem;
    color: var(--text-primary); background: white;
    cursor: pointer; transition: all .15s;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 12px center;
    padding-right: 36px; box-sizing: border-box;
}
.sf-select:focus { outline: none; border-color: #4f46e5; box-shadow: 0 0 0 3px rgba(79,70,229,.08); }

.upload-zone {
    border: 2px dashed var(--border-color);
    border-radius: 12px; padding: 20px;
    text-align: center; cursor: pointer;
    transition: all .2s;
    position: relative;
}
.upload-zone:hover { border-color: #4f46e5; background: rgba(79,70,229,.02); }
.upload-zone input[type=file] { position: absolute; inset: 0; opacity: 0; cursor: pointer; }
.upload-zone-icon { font-size: 1.5rem; color: var(--text-muted); margin-bottom: 8px; }
.upload-zone-text { font-size: .8rem; color: var(--text-secondary); font-weight: 500; }
.upload-zone-hint { font-size: .72rem; color: var(--text-muted); margin-top: 4px; }

.save-settings-btn {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 12px 28px; border-radius: 12px; font-size: .9rem;
    font-weight: 700; background: linear-gradient(135deg, #3730a3, #4f46e5);
    color: white; border: none; cursor: pointer; transition: all .2s;
    margin-top: 4px;
}
.save-settings-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(79,70,229,.3); }

/* Feature Toggles */
.feature-item {
    display: flex; align-items: center; justify-content: space-between;
    padding: 14px 24px; border-bottom: 1px solid var(--border-color);
    transition: background .15s;
}
.feature-item:last-child { border-bottom: none; }
.feature-item:hover { background: rgba(79,70,229,.02); }
.feature-info { flex: 1; min-width: 0; padding-right: 14px; }
.feature-name { font-size: .875rem; font-weight: 700; color: var(--text-primary); }
.feature-desc { font-size: .72rem; color: var(--text-muted); margin-top: 2px; line-height: 1.4; }
.toggle-switch { position: relative; width: 48px; height: 26px; flex-shrink: 0; }
.toggle-switch input { opacity: 0; width: 0; height: 0; }
.toggle-track {
    position: absolute; inset: 0; background: #e2e8f0;
    border-radius: 99px; cursor: pointer; transition: background .2s;
}
.toggle-track::after {
    content: '';
    position: absolute; top: 3px; left: 3px;
    width: 20px; height: 20px;
    background: white; border-radius: 50%;
    transition: transform .2s;
    box-shadow: 0 1px 4px rgba(0,0,0,.2);
}
.toggle-switch input:checked ~ .toggle-track { background: #10b981; }
.toggle-switch input:checked ~ .toggle-track::after { transform: translateX(22px); }

/* Optimize card (right side) */
.optimize-card {
    background: var(--surface);
    border: 1.5px solid var(--border-color);
    border-radius: 20px; overflow: hidden;
    margin-bottom: 20px;
}
.optimize-header {
    padding: 18px 22px; border-bottom: 1px solid var(--border-color);
    display: flex; align-items: center; gap: 12px;
    background: linear-gradient(135deg, rgba(245,158,11,.05), rgba(234,179,8,.05));
}
.optimize-icon {
    width: 38px; height: 38px; border-radius: 10px;
    background: rgba(245,158,11,.1); color: #d97706;
    display: flex; align-items: center; justify-content: center; font-size: .9rem;
}
.optimize-body { padding: 20px; }
.optimize-desc { font-size: .8rem; color: var(--text-secondary); line-height: 1.6; margin-bottom: 16px; }
.btn-optimize {
    display: flex; align-items: center; justify-content: center; gap: 8px;
    width: 100%; padding: 11px 16px; border-radius: 12px;
    background: linear-gradient(135deg, #d97706, #f59e0b);
    color: white; font-weight: 700; font-size: .875rem;
    border: none; cursor: pointer; transition: all .2s;
}
.btn-optimize:hover { transform: translateY(-1px); box-shadow: 0 5px 18px rgba(245,158,11,.35); }

/* Info card */
.info-card {
    background: var(--surface);
    border: 1.5px solid var(--border-color);
    border-radius: 20px; padding: 20px;
}
.info-title { font-size: .8rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: .04em; margin-bottom: 14px; }
.info-row {
    display: flex; justify-content: space-between; align-items: center;
    padding: 8px 0; border-bottom: 1px solid var(--border-color);
    font-size: .8125rem;
}
.info-row:last-child { border-bottom: none; }
.info-row .lbl { color: var(--text-secondary); }
.info-row .val { font-weight: 700; color: var(--text-primary); }
.info-row .val.green { color: #059669; }
</style>
@endpush

@section('content')

{{-- ═══ HERO ═══ --}}
<div class="settings-hero">
    <div class="settings-hero-content">
        <h2><i class="fas fa-sliders" style="margin-right:10px;opacity:.8;"></i>System Settings</h2>
        <p>Configure global platform identities, features, and operational parameters.</p>
    </div>
    <div class="settings-hero-version">
        <i class="fas fa-code-branch" style="font-size:.8rem;"></i> BuyNiger v2.0
    </div>
</div>

<form action="{{ route($prefix.'settings.update') }}" method="POST" enctype="multipart/form-data">
@csrf

<div class="settings-layout">

    {{-- ─── LEFT: Settings Sections ─── --}}
    <div>

        {{-- General Info --}}
        <div class="settings-section">
            <div class="ss-header">
                <div class="ss-icon indigo"><i class="fas fa-globe"></i></div>
                <div>
                    <div class="ss-title">General Platform Config</div>
                    <div class="ss-desc">Platform name, currency, language &amp; timezone.</div>
                </div>
            </div>
            <div class="ss-body">
                @if(session('success'))
                    <div class="alert alert-success" style="margin-bottom:16px;">
                        <i class="fas fa-check-circle"></i> <span>{{ session('success') }}</span>
                    </div>
                @endif
                <div class="sfield-full">
                    <label class="sf-label">Application Name</label>
                    <input type="text" name="settings[app_name]" class="sf-input"
                           value="{{ \App\Models\SystemSetting::get('site_name', 'BuyNiger') }}">
                </div>
                <div class="sfield-grid">
                    <div>
                        <label class="sf-label">Currency Symbol</label>
                        <input type="text" name="settings[currency_symbol]" class="sf-input"
                               value="{{ \App\Models\SystemSetting::get('currency_symbol', '₦') }}">
                    </div>
                    <div>
                        <label class="sf-label">Currency Code</label>
                        <input type="text" name="settings[currency_code]" class="sf-input"
                               value="{{ \App\Models\SystemSetting::get('currency', 'NGN') }}">
                    </div>
                </div>
                <div class="sfield-grid">
                    <div>
                        <label class="sf-label">Timezone</label>
                        <select name="settings[timezone]" class="sf-select">
                            <option value="Africa/Lagos" selected>Africa/Lagos (WAT)</option>
                            <option value="UTC">UTC</option>
                            <option value="Africa/Abidjan">Africa/Abidjan</option>
                        </select>
                    </div>
                    <div>
                        <label class="sf-label">Language</label>
                        <select name="settings[language]" class="sf-select">
                            <option value="en" selected>English (en)</option>
                            <option value="fr">French (fr)</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- Contact & Branding --}}
        <div class="settings-section">
            <div class="ss-header">
                <div class="ss-icon teal"><i class="fas fa-address-card"></i></div>
                <div>
                    <div class="ss-title">Contact &amp; Branding</div>
                    <div class="ss-desc">Support details and platform logo.</div>
                </div>
            </div>
            <div class="ss-body">
                <div class="sfield-grid">
                    <div>
                        <label class="sf-label">Support Email</label>
                        <input type="email" name="settings[contact_email]" class="sf-input"
                               value="{{ \App\Models\SystemSetting::get('support_email', 'info@buyniger.com') }}"
                               placeholder="info@buyniger.com">
                    </div>
                    <div>
                        <label class="sf-label">Support Phone</label>
                        <input type="text" name="settings[phone]" class="sf-input"
                               value="{{ \App\Models\SystemSetting::get('phone', '09019194418') }}"
                               placeholder="09019194418">
                    </div>
                </div>
                <div class="sfield-full">
                    <label class="sf-label">Platform Logo</label>
                    <div class="upload-zone">
                        <input type="file" name="logo" accept="image/*">
                        <div class="upload-zone-icon"><i class="fas fa-cloud-arrow-up"></i></div>
                        <div class="upload-zone-text">Click to upload or drag &amp; drop</div>
                        <div class="upload-zone-hint">Recommended: 200×50px PNG or SVG</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Save button --}}
        <div style="display:flex;justify-content:flex-end;">
            <button type="submit" class="save-settings-btn">
                <i class="fas fa-circle-check"></i> Save System Config
            </button>
        </div>

    </div>

    {{-- ─── RIGHT: Feature Toggles + Tools ─── --}}
    <div>

        {{-- Feature Toggles --}}
        <div class="settings-section" style="margin-bottom:20px;">
            <div class="ss-header">
                <div class="ss-icon amber"><i class="fas fa-toggle-on"></i></div>
                <div>
                    <div class="ss-title">Feature Toggles</div>
                    <div class="ss-desc">Enable or disable platform components.</div>
                </div>
            </div>
            @foreach($features as $feature)
                <div class="feature-item">
                    <div class="feature-info">
                        <div class="feature-name">{{ $feature->display_name }}</div>
                        @if($feature->description)
                            <div class="feature-desc">{{ $feature->description }}</div>
                        @endif
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="features[{{ $feature->feature }}]"
                               {{ $feature->is_enabled ? 'checked' : '' }}>
                        <span class="toggle-track"></span>
                    </label>
                </div>
            @endforeach
        </div>

        {{-- Cache Optimization --}}
        <div class="optimize-card">
            <div class="optimize-header">
                <div class="optimize-icon"><i class="fas fa-bolt"></i></div>
                <div>
                    <div style="font-size:.9rem;font-weight:800;color:var(--text-primary);font-family:'Outfit',sans-serif;">Cache Optimization</div>
                    <div style="font-size:.72rem;color:var(--text-muted);">Clear &amp; rebuild system caches</div>
                </div>
            </div>
            <div class="optimize-body">
                <p class="optimize-desc">
                    Clears config, route, view and database caches. Fixes stale states and applies pending configuration updates.
                </p>
                <form action="{{ route($prefix.'optimize') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-optimize">
                        <i class="fas fa-arrows-spin"></i> Run Optimization
                    </button>
                </form>
            </div>
        </div>

        {{-- Environment Info --}}
        <div class="info-card">
            <div class="info-title">Environment</div>
            <div class="info-row">
                <span class="lbl">Laravel</span>
                <span class="val">{{ app()->version() }}</span>
            </div>
            <div class="info-row">
                <span class="lbl">PHP</span>
                <span class="val">{{ phpversion() }}</span>
            </div>
            <div class="info-row">
                <span class="lbl">Environment</span>
                <span class="val green">{{ ucfirst(app()->environment()) }}</span>
            </div>
            <div class="info-row">
                <span class="lbl">Debug Mode</span>
                <span class="val" style="{{ config('app.debug') ? 'color:#f59e0b;' : 'color:#059669;' }}">
                    {{ config('app.debug') ? 'ON' : 'OFF' }}
                </span>
            </div>
        </div>

    </div>
</div>

</form>
@endsection
