{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    Written by Shuaibu Abdulmumin
    View: Admin — Email Configuration — Premium v2.0
--}}
@extends('layouts.app')

@section('title', 'Email Configuration')
@section('page_title', 'Email Configuration')

@section('sidebar')
    @include('superadmin.partials.sidebar')
@endsection

@php
    $prefix = request()->is('admin*') ? 'admin.' : 'superadmin.';
@endphp

@push('styles')
<style>
.email-hero {
    background: linear-gradient(135deg, #1e3a5f 0%, #1e40af 50%, #1d4ed8 100%);
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
.email-hero::before {
    content: '';
    position: absolute;
    top: -60px; right: -40px;
    width: 200px; height: 200px;
    background: rgba(147,197,253,.1);
    border-radius: 50%;
}
.email-hero-content { position: relative; z-index: 1; }
.email-hero-content h2 {
    color: white; font-size: 1.375rem; font-weight: 800;
    font-family: 'Outfit', sans-serif; margin-bottom: 4px;
}
.email-hero-content p { color: rgba(255,255,255,.65); font-size: .875rem; margin: 0; }
.email-hero-badge {
    position: relative; z-index: 1;
    display: inline-flex; align-items: center; gap: 7px;
    padding: 8px 16px; border-radius: 20px;
    background: rgba(255,255,255,.15); color: white;
    font-size: .82rem; font-weight: 600; border: 1px solid rgba(255,255,255,.2);
}
.email-hero-badge .dot { width: 8px; height: 8px; border-radius: 50%; background: #4ade80; animation: pulse 2s infinite; }
@keyframes pulse {
    0%, 100% { opacity: 1; } 50% { opacity: .4; }
}

.email-layout {
    display: grid;
    grid-template-columns: 1fr 320px;
    gap: 20px;
    align-items: start;
}
@media (max-width: 900px) { .email-layout { grid-template-columns: 1fr; } }

/* SMTP Card */
.smtp-card {
    background: var(--surface);
    border: 1.5px solid var(--border-color);
    border-radius: 20px; overflow: hidden;
}
.smtp-card-header {
    padding: 20px 24px;
    border-bottom: 1px solid var(--border-color);
    display: flex; align-items: center; gap: 14px;
    background: linear-gradient(135deg, rgba(29,78,216,.04), rgba(37,99,235,.04));
}
.smtp-icon {
    width: 44px; height: 44px; border-radius: 12px;
    background: linear-gradient(135deg, #1d4ed8, #2563eb);
    display: flex; align-items: center; justify-content: center;
    color: white; font-size: 1.1rem;
}
.smtp-title { font-size: .95rem; font-weight: 800; color: var(--text-primary); font-family: 'Outfit', sans-serif; }
.smtp-desc  { font-size: .75rem; color: var(--text-muted); margin-top: 2px; }
.smtp-card-body { padding: 24px; }

.smtp-field-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px; }
@media (max-width: 600px) { .smtp-field-grid { grid-template-columns: 1fr; } }
.smtp-field-full { margin-bottom: 16px; }

.s-label {
    display: block; margin-bottom: 6px;
    font-size: .78rem; font-weight: 700; color: var(--text-secondary);
    text-transform: uppercase; letter-spacing: .04em;
}
.s-input {
    width: 100%; padding: 11px 14px;
    border: 1.5px solid var(--border-color);
    border-radius: 12px; font-size: .875rem;
    color: var(--text-primary); background: white;
    transition: all .15s; box-sizing: border-box;
}
.s-input:focus {
    outline: none; border-color: #1d4ed8;
    box-shadow: 0 0 0 3px rgba(29,78,216,.08);
}
.s-select {
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
.s-select:focus { outline: none; border-color: #1d4ed8; box-shadow: 0 0 0 3px rgba(29,78,216,.08); }

.smtp-actions {
    display: flex; gap: 10px;
    padding-top: 20px; border-top: 1px solid var(--border-color);
    margin-top: 8px;
}
.btn-save-email {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 11px 22px; border-radius: 12px; font-size: .875rem;
    font-weight: 700; background: linear-gradient(135deg, #1d4ed8, #2563eb);
    color: white; border: none; cursor: pointer; transition: all .2s;
}
.btn-save-email:hover { transform: translateY(-1px); box-shadow: 0 5px 18px rgba(29,78,216,.3); }
.btn-test-email {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 11px 22px; border-radius: 12px; font-size: .875rem;
    font-weight: 700; background: white; color: var(--text-secondary);
    border: 1.5px solid var(--border-color); cursor: pointer; transition: all .2s;
}
.btn-test-email:hover { border-color: #1d4ed8; color: #1d4ed8; }

/* Template list */
.templates-card {
    background: var(--surface);
    border: 1.5px solid var(--border-color);
    border-radius: 20px; overflow: hidden;
}
.templates-header {
    padding: 18px 22px;
    border-bottom: 1px solid var(--border-color);
    display: flex; align-items: center; gap: 12px;
}
.templates-icon {
    width: 38px; height: 38px; border-radius: 10px;
    background: rgba(29,78,216,.1); color: #1d4ed8;
    display: flex; align-items: center; justify-content: center; font-size: .9rem;
}
.template-item {
    display: flex; align-items: center;
    padding: 14px 22px; border-bottom: 1px solid var(--border-color);
    gap: 12px; transition: background .15s; cursor: pointer;
}
.template-item:last-child { border-bottom: none; }
.template-item:hover { background: rgba(29,78,216,.03); }
.template-item-icon {
    width: 34px; height: 34px; border-radius: 9px;
    display: flex; align-items: center; justify-content: center;
    font-size: .8rem; flex-shrink: 0;
}
.ti-indigo { background: rgba(79,70,229,.1); color: #4338ca; }
.ti-green  { background: rgba(16,185,129,.1); color: #059669; }
.ti-orange { background: rgba(245,158,11,.1); color: #d97706; }
.ti-blue   { background: rgba(14,165,233,.1); color: #0284c7; }
.template-name  { font-size: .8125rem; font-weight: 600; color: var(--text-primary); }
.template-trigger { font-size: .72rem; color: var(--text-muted); margin-top: 1px; }
.template-active-pill {
    margin-left: auto;
    padding: 3px 10px; border-radius: 20px;
    background: rgba(16,185,129,.1); color: #059669;
    font-size: .72rem; font-weight: 700;
    border: 1px solid rgba(16,185,129,.2);
}

/* Stats card */
.email-stats-card {
    background: var(--surface);
    border: 1.5px solid var(--border-color);
    border-radius: 20px;
    padding: 20px 22px;
    margin-top: 16px;
}
.email-stats-title {
    font-size: .8rem; font-weight: 700; color: var(--text-muted);
    text-transform: uppercase; letter-spacing: .04em; margin-bottom: 14px;
}
.email-stat-row {
    display: flex; justify-content: space-between; align-items: center;
    padding: 8px 0; border-bottom: 1px solid var(--border-color);
    font-size: .8125rem;
}
.email-stat-row:last-child { border-bottom: none; }
.email-stat-row .label { color: var(--text-secondary); }
.email-stat-row .value { font-weight: 700; color: var(--text-primary); }
</style>
@endpush

@section('content')

{{-- ═══ HERO ═══ --}}
<div class="email-hero">
    <div class="email-hero-content">
        <h2><i class="fas fa-envelope-open-text" style="margin-right:10px;opacity:.8;"></i>Email Configuration</h2>
        <p>Configure SMTP credentials and manage transactional email templates.</p>
    </div>
    <div class="email-hero-badge">
        <div class="dot"></div> Mail Service Active
    </div>
</div>

{{-- ═══ LAYOUT ═══ --}}
<div class="email-layout">

    {{-- ─── LEFT: SMTP Form ─── --}}
    <div>
        <div class="smtp-card">
            <div class="smtp-card-header">
                <div class="smtp-icon"><i class="fas fa-server"></i></div>
                <div>
                    <div class="smtp-title">SMTP Mail Server</div>
                    <div class="smtp-desc">Configure outgoing transactional mail delivery settings.</div>
                </div>
            </div>
            <div class="smtp-card-body">
                @if(session('success'))
                    <div class="alert alert-success" style="margin-bottom:16px;">
                        <i class="fas fa-check-circle"></i> <span>{{ session('success') }}</span>
                    </div>
                @endif
                <form action="{{ route($prefix.'settings.update') }}" method="POST">
                    @csrf
                    <div class="smtp-field-grid">
                        <div>
                            <label class="s-label">Mail Driver</label>
                            <select name="settings[mail_driver]" class="s-select">
                                <option value="smtp">SMTP Driver</option>
                                <option value="sendmail">Sendmail</option>
                                <option value="mailgun">Mailgun</option>
                                <option value="ses">Amazon SES</option>
                            </select>
                        </div>
                        <div>
                            <label class="s-label">Mail Host</label>
                            <input type="text" name="settings[mail_host]" class="s-input" placeholder="smtp.mailtrap.io" value="smtp.mailtrap.io">
                        </div>
                    </div>
                    <div class="smtp-field-grid">
                        <div>
                            <label class="s-label">Port</label>
                            <input type="text" name="settings[mail_port]" class="s-input" placeholder="2525" value="2525">
                        </div>
                        <div>
                            <label class="s-label">Encryption</label>
                            <select name="settings[mail_encryption]" class="s-select">
                                <option value="tls">TLS</option>
                                <option value="ssl">SSL</option>
                                <option value="">None</option>
                            </select>
                        </div>
                    </div>
                    <div class="smtp-field-grid">
                        <div>
                            <label class="s-label">Username</label>
                            <input type="text" name="settings[mail_username]" class="s-input" placeholder="your@email.com">
                        </div>
                        <div>
                            <label class="s-label">Password</label>
                            <input type="password" name="settings[mail_password]" class="s-input" placeholder="••••••••">
                        </div>
                    </div>
                    <div class="smtp-field-grid">
                        <div>
                            <label class="s-label">From Name</label>
                            <input type="text" name="settings[mail_from_name]" class="s-input" placeholder="BuyNiger" value="BuyNiger">
                        </div>
                        <div>
                            <label class="s-label">From Email</label>
                            <input type="email" name="settings[mail_from_address]" class="s-input" placeholder="noreply@buyniger.com">
                        </div>
                    </div>
                    <div class="smtp-actions">
                        <button type="submit" class="btn-save-email">
                            <i class="fas fa-floppy-disk"></i> Save Configuration
                        </button>
                        <button type="button" class="btn-test-email">
                            <i class="fas fa-rotate"></i> Test Connection
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ─── RIGHT: Templates + Stats ─── --}}
    <div>
        {{-- Email Templates --}}
        <div class="templates-card">
            <div class="templates-header">
                <div class="templates-icon"><i class="fas fa-envelope"></i></div>
                <div>
                    <div style="font-size:.9rem;font-weight:800;color:var(--text-primary);font-family:'Outfit',sans-serif;">Mail Templates</div>
                    <div style="font-size:.72rem;color:var(--text-muted);">System triggers &amp; layouts</div>
                </div>
            </div>
            <div class="template-item">
                <div class="template-item-icon ti-indigo"><i class="fas fa-user-plus"></i></div>
                <div>
                    <div class="template-name">Welcome Email</div>
                    <div class="template-trigger">Trigger: New user registration</div>
                </div>
                <span class="template-active-pill">Active</span>
            </div>
            <div class="template-item">
                <div class="template-item-icon ti-green"><i class="fas fa-cart-shopping"></i></div>
                <div>
                    <div class="template-name">Order Confirmation</div>
                    <div class="template-trigger">Trigger: Payment successful</div>
                </div>
                <span class="template-active-pill">Active</span>
            </div>
            <div class="template-item">
                <div class="template-item-icon ti-orange"><i class="fas fa-store"></i></div>
                <div>
                    <div class="template-name">Vendor Activation</div>
                    <div class="template-trigger">Trigger: Vendor approved</div>
                </div>
                <span class="template-active-pill">Active</span>
            </div>
            <div class="template-item">
                <div class="template-item-icon ti-blue"><i class="fas fa-key"></i></div>
                <div>
                    <div class="template-name">Password Reset</div>
                    <div class="template-trigger">Trigger: Reset request</div>
                </div>
                <span class="template-active-pill">Active</span>
            </div>
        </div>

        {{-- Quick Stats --}}
        <div class="email-stats-card">
            <div class="email-stats-title">Delivery Overview</div>
            <div class="email-stat-row">
                <span class="label"><i class="fas fa-paper-plane" style="color:#1d4ed8;margin-right:6px;"></i> Total Sent</span>
                <span class="value">—</span>
            </div>
            <div class="email-stat-row">
                <span class="label"><i class="fas fa-check-circle" style="color:#10b981;margin-right:6px;"></i> Delivered</span>
                <span class="value">—</span>
            </div>
            <div class="email-stat-row">
                <span class="label"><i class="fas fa-triangle-exclamation" style="color:#f59e0b;margin-right:6px;"></i> Bounced</span>
                <span class="value">—</span>
            </div>
            <div class="email-stat-row">
                <span class="label"><i class="fas fa-clock" style="color:#8b5cf6;margin-right:6px;"></i> Queued</span>
                <span class="value">—</span>
            </div>
        </div>
    </div>
</div>

@endsection
