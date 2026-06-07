{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    Written by Shuaibu Abdulmumin
    View: Admin — Dispute Management — Premium v2.0
--}}
@extends('layouts.app')

@section('title', 'Dispute Management')
@section('page_title', 'Dispute Management')

@section('sidebar')
    @include('superadmin.partials.sidebar')
@endsection

@php
    $prefix = request()->is('admin*') ? 'admin.' : 'superadmin.';
@endphp

@push('styles')
<style>
.dispute-header-banner {
    background: linear-gradient(135deg, #450a0a 0%, #7f1d1d 50%, #991b1b 100%);
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
.dispute-header-banner::before {
    content: '';
    position: absolute;
    top: -60px; right: -40px;
    width: 200px; height: 200px;
    background: rgba(254,202,202,.08);
    border-radius: 50%;
}
.dhb-content { position: relative; z-index: 1; }
.dhb-content h2 {
    color: white; font-size: 1.375rem; font-weight: 800;
    font-family: 'Outfit', sans-serif; margin-bottom: 4px;
}
.dhb-content p { color: rgba(255,255,255,.6); font-size: .875rem; margin: 0; }
.dhb-badge {
    position: relative; z-index: 1;
    display: inline-flex; align-items: center; gap: 8px;
    padding: 8px 16px; border-radius: 20px;
    background: rgba(255,255,255,.12); color: white;
    font-size: .85rem; font-weight: 700; border: 1px solid rgba(255,255,255,.2);
}
.dhb-badge-dot { width: 8px; height: 8px; border-radius: 50%; background: #fca5a5; }

.dispute-stats { display: flex; gap: 14px; flex-wrap: wrap; margin-bottom: 24px; }
.d-stat-pill {
    flex: 1; min-width: 130px;
    background: var(--surface);
    border: 1.5px solid var(--border-color);
    border-radius: 14px;
    padding: 14px 18px;
    display: flex; align-items: center; gap: 12px;
    transition: all .2s;
}
.d-stat-pill:hover { transform: translateY(-2px); box-shadow: 0 4px 16px rgba(0,0,0,.07); }
.dsp-icon {
    width: 38px; height: 38px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: .9rem; flex-shrink: 0;
}
.dsp-icon.red    { background: rgba(244,63,94,.1);  color: #be123c; }
.dsp-icon.blue   { background: rgba(14,165,233,.1); color: #0284c7; }
.dsp-icon.green  { background: rgba(16,185,129,.1); color: #059669; }
.dsp-icon.gray   { background: rgba(100,116,139,.1);color: #475569; }
.dsp-icon.orange { background: rgba(245,158,11,.1); color: #d97706; }
.dsp-label { font-size: .72rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: .04em; }
.dsp-value { font-size: 1.2rem; font-weight: 800; color: var(--text-primary); line-height: 1; }

.dispute-filters {
    display: flex; align-items: center; gap: 8px; flex-wrap: wrap;
    padding: 14px 20px; background: var(--surface); border-bottom: 1px solid var(--border-color);
}
.d-filter-tab {
    padding: 5px 14px; border-radius: 8px; font-size: .8rem; font-weight: 600;
    color: var(--text-secondary); background: white; border: 1.5px solid var(--border-color);
    text-decoration: none; transition: all .15s;
}
.d-filter-tab:hover:not(.active) { border-color: #be123c; color: #be123c; }
.d-filter-tab.active { background: #be123c; border-color: #be123c; color: white; }
.d-filter-tab.open.active   { background: #2563eb; border-color: #2563eb; }
.d-filter-tab.prog.active   { background: #0284c7; border-color: #0284c7; }
.d-filter-tab.res.active    { background: #059669; border-color: #059669; }
.d-filter-tab.esc.active    { background: #be123c; border-color: #be123c; }

.dispute-id-tag {
    font-family: 'Courier New', monospace;
    font-size: .8rem; font-weight: 700; color: #4f46e5;
    background: rgba(79,70,229,.07); padding: 3px 8px; border-radius: 6px;
}
.dispute-subject-title { font-weight: 700; font-size: .875rem; color: var(--text-primary); }
.dispute-subject-desc  { font-size: .73rem; color: var(--text-muted); margin-top: 2px; }
.dispute-customer { display: flex; align-items: center; gap: 8px; }
.d-cust-avatar {
    width: 28px; height: 28px; border-radius: 8px;
    background: linear-gradient(135deg, #be123c, #f43f5e);
    display: flex; align-items: center; justify-content: center;
    color: white; font-size: .7rem; font-weight: 700; flex-shrink: 0;
}
.d-cust-name  { font-weight: 600; font-size: .8125rem; color: var(--text-primary); }
.d-cust-email { font-size: .7rem; color: var(--text-muted); }
.d-order-link {
    font-weight: 700; font-size: .85rem; color: #4f46e5;
    text-decoration: none; transition: color .15s;
}
.d-order-link:hover { color: #4338ca; text-decoration: underline; }
.priority-badge {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 4px 9px; border-radius: 7px;
    font-size: .73rem; font-weight: 700;
}
.pb-critical { background: rgba(244,63,94,.12); color: #be123c; border: 1px solid rgba(244,63,94,.2); }
.pb-high     { background: rgba(245,158,11,.12); color: #d97706; border: 1px solid rgba(245,158,11,.2); }
.pb-medium   { background: rgba(14,165,233,.12); color: #0284c7; border: 1px solid rgba(14,165,233,.2); }
.pb-low      { background: rgba(100,116,139,.12);color: #475569; border: 1px solid rgba(100,116,139,.2); }
.status-badge {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 4px 9px; border-radius: 7px;
    font-size: .73rem; font-weight: 700;
}
.sb-open     { background: rgba(79,70,229,.1);  color: #4338ca; }
.sb-progress { background: rgba(14,165,233,.1); color: #0284c7; }
.sb-resolved { background: rgba(16,185,129,.1); color: #059669; }
.sb-closed   { background: rgba(100,116,139,.1);color: #475569; }
.sb-escalated{ background: rgba(244,63,94,.1);  color: #be123c; }

.d-time-date { font-size: .8125rem; font-weight: 600; color: var(--text-primary); }
.d-time-ago  { font-size: .72rem; color: var(--text-muted); margin-top: 1px; }

.btn-view-dispute {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 6px 13px; border-radius: 9px;
    background: rgba(244,63,94,.08); color: #be123c;
    border: 1.5px solid rgba(244,63,94,.2);
    font-size: .78rem; font-weight: 700;
    text-decoration: none; transition: all .15s;
}
.btn-view-dispute:hover { background: #be123c; color: white; }
</style>
@endpush

@section('content')

{{-- ═══ HEADER BANNER ═══ --}}
<div class="dispute-header-banner">
    <div class="dhb-content">
        <h2><i class="fas fa-scale-balanced" style="margin-right:10px;opacity:.8;"></i>Dispute Management</h2>
        <p>Manage customer complaints, order issues, and escalations across the platform.</p>
    </div>
    <div class="dhb-badge">
        <div class="dhb-badge-dot"></div>
        {{ $disputes->total() }} Active Cases
    </div>
</div>

{{-- ═══ STAT PILLS ═══ --}}
<div class="dispute-stats">
    @php
        $allDisputes = $disputes->getCollection();
    @endphp
    <div class="d-stat-pill">
        <div class="dsp-icon red"><i class="fas fa-envelope-open"></i></div>
        <div>
            <div class="dsp-label">Open</div>
            <div class="dsp-value">{{ $allDisputes->where('status','open')->count() }}</div>
        </div>
    </div>
    <div class="d-stat-pill">
        <div class="dsp-icon blue"><i class="fas fa-spinner"></i></div>
        <div>
            <div class="dsp-label">In Progress</div>
            <div class="dsp-value">{{ $allDisputes->where('status','in_progress')->count() }}</div>
        </div>
    </div>
    <div class="d-stat-pill">
        <div class="dsp-icon orange"><i class="fas fa-circle-exclamation"></i></div>
        <div>
            <div class="dsp-label">Escalated</div>
            <div class="dsp-value">{{ $allDisputes->where('status','escalated')->count() }}</div>
        </div>
    </div>
    <div class="d-stat-pill">
        <div class="dsp-icon green"><i class="fas fa-circle-check"></i></div>
        <div>
            <div class="dsp-label">Resolved</div>
            <div class="dsp-value">{{ $allDisputes->where('status','resolved')->count() }}</div>
        </div>
    </div>
    <div class="d-stat-pill">
        <div class="dsp-icon gray"><i class="fas fa-circle-xmark"></i></div>
        <div>
            <div class="dsp-label">Closed</div>
            <div class="dsp-value">{{ $allDisputes->where('status','closed')->count() }}</div>
        </div>
    </div>
</div>

{{-- ═══ MAIN CARD ═══ --}}
<div class="dashboard-card">
    <div class="dashboard-card-header">
        <div>
            <h3><i class="fas fa-comments" style="color:#be123c;margin-right:8px;"></i>All Disputes &amp; Complaints</h3>
            <div style="font-size:.8rem;color:var(--text-muted);margin-top:2px;">
                Showing {{ $disputes->firstItem() }}–{{ $disputes->lastItem() }} of {{ $disputes->total() }} cases
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="dispute-filters">
        <a href="{{ route($prefix.'disputes') }}" class="d-filter-tab {{ !request('status') ? 'active' : '' }}">All</a>
        <a href="{{ route($prefix.'disputes', ['status'=>'open']) }}"        class="d-filter-tab open {{ request('status')=='open' ? 'active' : '' }}">Open</a>
        <a href="{{ route($prefix.'disputes', ['status'=>'in_progress']) }}" class="d-filter-tab prog {{ request('status')=='in_progress' ? 'active' : '' }}">In Progress</a>
        <a href="{{ route($prefix.'disputes', ['status'=>'escalated']) }}"   class="d-filter-tab esc {{ request('status')=='escalated' ? 'active' : '' }}">Escalated</a>
        <a href="{{ route($prefix.'disputes', ['status'=>'resolved']) }}"    class="d-filter-tab res {{ request('status')=='resolved' ? 'active' : '' }}">Resolved</a>
        <a href="{{ route($prefix.'disputes', ['status'=>'closed']) }}"      class="d-filter-tab {{ request('status')=='closed' ? 'active' : '' }}">Closed</a>
    </div>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Subject</th>
                    <th>Customer</th>
                    <th>Order</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th style="text-align:right;padding-right:20px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($disputes as $dispute)
                    <tr>
                        <td>
                            <span class="dispute-id-tag">#{{ $dispute->id }}</span>
                        </td>
                        <td>
                            <div class="dispute-subject-title">{{ Str::limit($dispute->subject, 32) }}</div>
                            <div class="dispute-subject-desc">{{ Str::limit($dispute->description, 55) }}</div>
                        </td>
                        <td>
                            <div class="dispute-customer">
                                <div class="d-cust-avatar">{{ strtoupper(substr($dispute->user->name ?? 'U', 0, 1)) }}</div>
                                <div>
                                    <div class="d-cust-name">{{ $dispute->user->name ?? 'Unknown' }}</div>
                                    <div class="d-cust-email">{{ $dispute->user->email ?? '' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($dispute->order)
                                <a href="{{ route($prefix.'orders.show', $dispute->order->id) }}" class="d-order-link">
                                    #{{ $dispute->order->order_number }}
                                </a>
                            @else
                                <span style="color:var(--text-muted);">—</span>
                            @endif
                        </td>
                        <td>
                            @php $p = $dispute->priority; @endphp
                            <span class="priority-badge {{ $p=='critical' ? 'pb-critical' : ($p=='high' ? 'pb-high' : ($p=='medium' ? 'pb-medium' : 'pb-low')) }}">
                                <i class="fas {{ $p=='critical' ? 'fa-triangle-exclamation' : ($p=='high' ? 'fa-circle-exclamation' : 'fa-circle-info') }}"></i>
                                {{ ucfirst($p ?? 'Low') }}
                            </span>
                        </td>
                        <td>
                            @php $s = $dispute->status; @endphp
                            <span class="status-badge {{ $s=='open' ? 'sb-open' : ($s=='in_progress' ? 'sb-progress' : ($s=='resolved' ? 'sb-resolved' : ($s=='closed' ? 'sb-closed' : 'sb-escalated'))) }}">
                                <i class="fas {{ $s=='open' ? 'fa-envelope-open' : ($s=='in_progress' ? 'fa-spinner' : ($s=='resolved' ? 'fa-circle-check' : ($s=='closed' ? 'fa-circle-xmark' : 'fa-circle-exclamation'))) }}"></i>
                                {{ ucwords(str_replace('_',' ',$s)) }}
                            </span>
                        </td>
                        <td>
                            <div class="d-time-date">{{ $dispute->created_at->format('d M Y') }}</div>
                            <div class="d-time-ago">{{ $dispute->created_at->diffForHumans() }}</div>
                        </td>
                        <td style="text-align:right;padding-right:20px;">
                            <a href="{{ route($prefix.'disputes.show', $dispute->id) }}" class="btn-view-dispute">
                                <i class="fas fa-eye"></i> View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">
                            <div class="empty-state">
                                <i class="fas fa-scale-balanced"></i>
                                <p>No disputes found. All customer cases are clear!</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($disputes->hasPages())
        <div style="padding:14px 20px;">
            {{ $disputes->appends(request()->query())->links() }}
        </div>
    @endif
</div>

@endsection
