{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    Written by Shuaibu Abdulmumin
    View: Admin — Payment Gateways & Commission Settings — Premium v2.0
--}}
@extends('layouts.app')

@section('title', 'Payment Gateways')
@section('page_title', 'Payment Gateways')

@section('sidebar')
    @include('superadmin.partials.sidebar')
@endsection

@php
    $prefix = request()->is('admin*') ? 'admin.' : 'superadmin.';
@endphp

@push('styles')
<style>
.pay-header-banner {
    background: linear-gradient(135deg, #0c4a6e 0%, #075985 50%, #0369a1 100%);
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
.pay-header-banner::before {
    content: '';
    position: absolute;
    top: -60px; right: -40px;
    width: 200px; height: 200px;
    background: rgba(186,230,253,.1);
    border-radius: 50%;
}
.pay-header-content { position: relative; z-index: 1; }
.pay-header-content h2 {
    color: white; font-size: 1.375rem; font-weight: 800;
    font-family: 'Outfit', sans-serif; margin-bottom: 4px;
}
.pay-header-content p { color: rgba(255,255,255,.65); font-size: .875rem; margin: 0; }
.pay-header-badges { position: relative; z-index: 1; display: flex; gap: 8px; }
.pay-badge {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 6px 14px; border-radius: 20px;
    background: rgba(255,255,255,.15); color: white;
    font-size: .8rem; font-weight: 600; border: 1px solid rgba(255,255,255,.25);
}
.pay-badge .dot { width: 7px; height: 7px; border-radius: 50%; background: #4ade80; }

.gateway-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(340px, 1fr)); gap: 20px; margin-bottom: 24px; }

.gateway-card {
    background: var(--surface);
    border: 1.5px solid var(--border-color);
    border-radius: 20px;
    overflow: hidden;
    transition: all .25s;
}
.gateway-card:hover { box-shadow: 0 8px 32px rgba(0,0,0,.08); transform: translateY(-2px); }
.gateway-card.active-card { border-color: #0ea5e9; }

.gateway-card-header {
    padding: 20px 24px 16px;
    border-bottom: 1px solid var(--border-color);
    display: flex; align-items: center; justify-content: space-between;
    gap: 12px;
}
.gateway-logo-wrap { display: flex; align-items: center; gap: 14px; }
.gateway-logo {
    width: 48px; height: 48px; border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.25rem; flex-shrink: 0;
}
.gateway-logo.paystack    { background: linear-gradient(135deg, #0ba4db, #0077b5); color: white; }
.gateway-logo.flutterwave { background: linear-gradient(135deg, #f6a623, #ff5b00); color: white; }
.gateway-logo.bank        { background: linear-gradient(135deg, #059669, #047857); color: white; }
.gateway-name { font-size: 1rem; font-weight: 800; color: var(--text-primary); font-family: 'Outfit', sans-serif; }
.gateway-desc { font-size: .75rem; color: var(--text-muted); margin-top: 2px; }

/* Toggle switch */
.toggle-switch { position: relative; width: 48px; height: 26px; flex-shrink: 0; }
.toggle-switch input { opacity: 0; width: 0; height: 0; }
.toggle-track {
    position: absolute; inset: 0; background: #e2e8f0;
    border-radius: 99px; cursor: pointer; transition: background .2s;
}
.toggle-track::after {
    content: '';
    position: absolute;
    top: 3px; left: 3px;
    width: 20px; height: 20px;
    background: white; border-radius: 50%;
    transition: transform .2s;
    box-shadow: 0 1px 4px rgba(0,0,0,.2);
}
.toggle-switch input:checked ~ .toggle-track { background: #10b981; }
.toggle-switch input:checked ~ .toggle-track::after { transform: translateX(22px); }

.gateway-card-body { padding: 20px 24px 24px; }
.gw-field-group { margin-bottom: 16px; }
.gw-label {
    display: block; margin-bottom: 5px;
    font-size: .78rem; font-weight: 700; color: var(--text-secondary);
    text-transform: uppercase; letter-spacing: .04em;
}
.gw-input {
    width: 100%; padding: 10px 14px;
    border: 1.5px solid var(--border-color);
    border-radius: 11px; font-size: .875rem;
    color: var(--text-primary); background: white;
    transition: all .15s;
}
.gw-input:focus {
    outline: none; border-color: #0ea5e9;
    box-shadow: 0 0 0 3px rgba(14,165,233,.1);
}
.gw-input-group { display: flex; align-items: center; }
.gw-input-group .gw-input { border-radius: 11px 0 0 11px; }
.gw-input-addon {
    padding: 10px 14px; border: 1.5px solid var(--border-color);
    border-left: none; border-radius: 0 11px 11px 0;
    background: rgba(100,116,139,.06); font-size: .875rem;
    color: var(--text-muted); font-weight: 600;
}
.gw-save-btn {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 10px 20px; border-radius: 11px; font-size: .875rem;
    font-weight: 700; background: #0ea5e9; color: white;
    border: none; cursor: pointer; transition: all .2s;
    width: 100%; justify-content: center; margin-top: 4px;
}
.gw-save-btn:hover { background: #0284c7; transform: translateY(-1px); box-shadow: 0 4px 14px rgba(14,165,233,.3); }
.gw-save-btn.green { background: #10b981; }
.gw-save-btn.green:hover { background: #059669; box-shadow: 0 4px 14px rgba(16,185,129,.3); }
.gw-save-btn.indigo { background: #4f46e5; }
.gw-save-btn.indigo:hover { background: #4338ca; box-shadow: 0 4px 14px rgba(79,70,229,.3); }

.gw-status-note {
    display: flex; align-items: center; gap: 8px;
    padding: 10px 14px; border-radius: 10px;
    background: rgba(16,185,129,.07); border: 1px solid rgba(16,185,129,.15);
    font-size: .8rem; color: #059669; font-weight: 600;
    margin-bottom: 16px;
}

/* Commission section */
.commission-card {
    background: var(--surface);
    border: 1.5px solid var(--border-color);
    border-radius: 20px; overflow: hidden;
}
.commission-card-header {
    padding: 20px 24px;
    border-bottom: 1px solid var(--border-color);
    background: linear-gradient(135deg, rgba(79,70,229,.04), rgba(139,92,246,.04));
    display: flex; align-items: center; gap: 14px;
}
.commission-icon {
    width: 46px; height: 46px; border-radius: 13px;
    background: linear-gradient(135deg, #4f46e5, #8b5cf6);
    display: flex; align-items: center; justify-content: center;
    color: white; font-size: 1.1rem;
}
.commission-card-body { padding: 24px; }
.commission-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 18px; }
.comm-field-wrap { }
.comm-input-wrap {
    display: flex; align-items: center;
    border: 1.5px solid var(--border-color);
    border-radius: 11px; overflow: hidden; transition: all .15s;
}
.comm-input-wrap:focus-within {
    border-color: #4f46e5;
    box-shadow: 0 0 0 3px rgba(79,70,229,.1);
}
.comm-prefix {
    padding: 10px 14px;
    background: rgba(100,116,139,.06);
    font-size: .875rem; font-weight: 700;
    color: var(--text-secondary);
    border-right: 1.5px solid var(--border-color);
    white-space: nowrap;
}
.comm-input {
    flex: 1; border: none; padding: 10px 14px;
    font-size: .875rem; color: var(--text-primary);
    background: transparent;
}
.comm-input:focus { outline: none; }
.comm-save-btn {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 12px 28px; border-radius: 12px; font-size: .9rem;
    font-weight: 700; background: linear-gradient(135deg, #4f46e5, #7c3aed);
    color: white; border: none; cursor: pointer; transition: all .2s;
    margin-top: 8px;
}
.comm-save-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(79,70,229,.3); }

.schedule-select {
    width: 100%; padding: 10px 14px;
    border: 1.5px solid var(--border-color);
    border-radius: 11px; font-size: .875rem;
    color: var(--text-primary); background: white;
    cursor: pointer; transition: all .15s;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 12px center;
    padding-right: 36px;
}
.schedule-select:focus { outline: none; border-color: #4f46e5; box-shadow: 0 0 0 3px rgba(79,70,229,.1); }
</style>
@endpush

@section('content')

{{-- ═══ HEADER BANNER ═══ --}}
<div class="pay-header-banner">
    <div class="pay-header-content">
        <h2><i class="fas fa-shield-halved" style="margin-right:10px;opacity:.8;"></i>Payment Gateways</h2>
        <p>Configure payment processors, API credentials and payout commission policies.</p>
    </div>
    <div class="pay-header-badges">
        <div class="pay-badge"><div class="dot"></div> Paystack Live</div>
        <div class="pay-badge"><div class="dot"></div> Flutterwave Live</div>
    </div>
</div>

{{-- ═══ GATEWAY CARDS ═══ --}}
<div class="gateway-grid">

    {{-- ─── PAYSTACK ─── --}}
    <div class="gateway-card active-card">
        <div class="gateway-card-header">
            <div class="gateway-logo-wrap">
                <div class="gateway-logo paystack"><i class="fas fa-credit-card"></i></div>
                <div>
                    <div class="gateway-name">Paystack</div>
                    <div class="gateway-desc">Primary gateway · Local card &amp; bank transfers</div>
                </div>
            </div>
            <label class="toggle-switch">
                <input type="checkbox" checked>
                <span class="toggle-track"></span>
            </label>
        </div>
        <div class="gateway-card-body">
            <div class="gw-status-note">
                <i class="fas fa-circle-check"></i> Gateway is active and processing payments
            </div>
            <form action="{{ route($prefix.'settings.update') }}" method="POST">
                @csrf
                <div class="gw-field-group">
                    <label class="gw-label">Public Key</label>
                    <input type="text" name="settings[paystack_public]" class="gw-input" placeholder="pk_live_..." value="{{ old('settings.paystack_public') }}">
                </div>
                <div class="gw-field-group">
                    <label class="gw-label">Secret Key</label>
                    <input type="password" name="settings[paystack_secret]" class="gw-input" placeholder="sk_live_...">
                </div>
                <div class="gw-field-group">
                    <label class="gw-label">Settlement Email</label>
                    <input type="email" name="settings[paystack_email]" class="gw-input" placeholder="merchant@buyniger.com">
                </div>
                <button type="submit" class="gw-save-btn">
                    <i class="fas fa-floppy-disk"></i> Save Paystack Config
                </button>
            </form>
        </div>
    </div>

    {{-- ─── FLUTTERWAVE ─── --}}
    <div class="gateway-card active-card">
        <div class="gateway-card-header">
            <div class="gateway-logo-wrap">
                <div class="gateway-logo flutterwave"><i class="fas fa-globe"></i></div>
                <div>
                    <div class="gateway-name">Flutterwave</div>
                    <div class="gateway-desc">Multi-currency · Mobile money &amp; international</div>
                </div>
            </div>
            <label class="toggle-switch">
                <input type="checkbox" checked>
                <span class="toggle-track"></span>
            </label>
        </div>
        <div class="gateway-card-body">
            <div class="gw-status-note">
                <i class="fas fa-circle-check"></i> Gateway is active and processing payments
            </div>
            <form action="{{ route($prefix.'settings.update') }}" method="POST">
                @csrf
                <div class="gw-field-group">
                    <label class="gw-label">Public Key</label>
                    <input type="text" name="settings[flutterwave_public]" class="gw-input" placeholder="FLWPUBK_TEST-...">
                </div>
                <div class="gw-field-group">
                    <label class="gw-label">Secret Key</label>
                    <input type="password" name="settings[flutterwave_secret]" class="gw-input" placeholder="FLWSECK_TEST-...">
                </div>
                <div class="gw-field-group">
                    <label class="gw-label">Encryption Key</label>
                    <input type="password" name="settings[flutterwave_enc]" class="gw-input" placeholder="Encryption key...">
                </div>
                <button type="submit" class="gw-save-btn green">
                    <i class="fas fa-floppy-disk"></i> Save Flutterwave Config
                </button>
            </form>
        </div>
    </div>

</div>

{{-- ═══ COMMISSION & PAYOUT POLICY ═══ --}}
<div class="commission-card">
    <div class="commission-card-header">
        <div class="commission-icon"><i class="fas fa-money-bill-transfer"></i></div>
        <div>
            <div style="font-size:1rem;font-weight:800;color:var(--text-primary);font-family:'Outfit',sans-serif;">Commission &amp; Payout Policy</div>
            <div style="font-size:.8rem;color:var(--text-muted);margin-top:2px;">Set platform-wide commission rates and vendor payout scheduling.</div>
        </div>
    </div>
    <div class="commission-card-body">
        <form action="{{ route($prefix.'settings.update') }}" method="POST">
            @csrf
            <div class="commission-grid">
                <div>
                    <label class="gw-label" style="margin-bottom:6px;">Platform Commission Rate</label>
                    <div class="comm-input-wrap">
                        <span class="comm-prefix">%</span>
                        <input type="number" name="settings[commission_rate]" class="comm-input" value="5" min="0" max="100" step="0.5">
                    </div>
                    <div style="font-size:.73rem;color:var(--text-muted);margin-top:5px;">Percentage deducted from each sale.</div>
                </div>
                <div>
                    <label class="gw-label" style="margin-bottom:6px;">Minimum Payout Threshold</label>
                    <div class="comm-input-wrap">
                        <span class="comm-prefix">₦</span>
                        <input type="number" name="settings[min_payout]" class="comm-input" value="5000" min="0">
                    </div>
                    <div style="font-size:.73rem;color:var(--text-muted);margin-top:5px;">Minimum balance before vendor can withdraw.</div>
                </div>
                <div>
                    <label class="gw-label" style="margin-bottom:6px;">Payout Schedule</label>
                    <select name="settings[payout_schedule]" class="schedule-select">
                        <option value="manual">Manual Requests (Recommended)</option>
                        <option value="weekly">Weekly Automated (Every Friday)</option>
                        <option value="monthly">Monthly Automated (1st of Month)</option>
                    </select>
                    <div style="font-size:.73rem;color:var(--text-muted);margin-top:5px;">How vendors receive their payouts.</div>
                </div>
            </div>
            <div style="margin-top:24px;padding-top:20px;border-top:1px solid var(--border-color);">
                <button type="submit" class="comm-save-btn">
                    <i class="fas fa-circle-check"></i> Save Policy Settings
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
