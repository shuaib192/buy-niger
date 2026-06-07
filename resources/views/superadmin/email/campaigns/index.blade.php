{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    View: Admin — Email Campaigns — Premium v2.0
--}}
@extends('layouts.app')

@section('title', 'Email Campaigns')
@section('page_title', 'Email Campaigns')

@section('sidebar')
    @include('superadmin.partials.sidebar')
@endsection

@push('styles')
<style>
.campaigns-hero {
    background: linear-gradient(135deg, #0284c7 0%, #0ea5e9 100%);
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
.campaigns-hero::before {
    content: '';
    position: absolute;
    top: -60px; right: -40px;
    width: 200px; height: 200px;
    background: rgba(255,255,255,.15);
    border-radius: 50%;
}
.campaigns-hero-content { position: relative; z-index: 1; }
.campaigns-hero-content h2 {
    color: white; font-size: 1.375rem; font-weight: 800;
    font-family: 'Outfit', sans-serif; margin-bottom: 4px;
}
.campaigns-hero-content p { color: rgba(255,255,255,.8); font-size: .875rem; margin: 0; }
.campaigns-hero-actions { position: relative; z-index: 1; }

.btn-new-campaign {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 11px 22px; border-radius: 12px; font-size: .875rem;
    font-weight: 700; background: white; color: #0284c7;
    border: none; cursor: pointer; transition: all .2s;
    text-decoration: none; box-shadow: 0 4px 14px rgba(0,0,0,0.1);
}
.btn-new-campaign:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(0,0,0,0.15); color: #0369a1; }

/* Table card styling */
.cmp-card {
    background: var(--surface);
    border: 1.5px solid var(--border-color);
    border-radius: 20px; overflow: hidden;
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);
}
.cmp-card-header {
    padding: 20px 24px;
    border-bottom: 1px solid var(--border-color);
    display: flex; align-items: center; justify-content: space-between;
}
.cmp-card-title {
    font-size: .95rem; font-weight: 800; color: var(--text-primary); font-family: 'Outfit', sans-serif;
}
.cmp-card-desc {
    font-size: .75rem; color: var(--text-muted); margin-top: 2px;
}

.cmp-table {
    width: 100%; border-collapse: collapse; text-align: left;
}
.cmp-table th {
    padding: 14px 20px; font-size: .75rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .05em; color: var(--text-secondary);
    background: var(--bg-surface); border-bottom: 1.5px solid var(--border-color);
}
.cmp-table td {
    padding: 18px 20px; font-size: .8125rem; border-bottom: 1px solid var(--border-color);
    vertical-align: middle; color: var(--text-secondary);
}
.cmp-table tr:last-child td { border-bottom: none; }
.cmp-table tr:hover { background: rgba(14, 165, 233, 0.02); }

.cmp-name {
    font-size: .875rem; font-weight: 700; color: var(--text-primary); font-family: 'Outfit', sans-serif;
}
.cmp-subject {
    font-size: .8125rem; color: var(--text-muted);
}
.audience-badge {
    display: inline-block; padding: 4px 10px; border-radius: 6px;
    background: rgba(14, 165, 233, 0.08); color: #0284c7;
    font-size: .72rem; font-weight: 700;
    border: 1px solid rgba(14, 165, 233, 0.15);
}

.status-badge {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 4px 10px; border-radius: 20px; font-size: .72rem; font-weight: 700;
}
.status-badge.sent {
    background: rgba(16, 185, 129, 0.1); color: #059669; border: 1px solid rgba(16, 185, 129, 0.2);
}
.status-badge.draft {
    background: rgba(107, 114, 128, 0.1); color: #4b5563; border: 1px solid rgba(107, 114, 128, 0.2);
}
.status-badge.sending {
    background: rgba(245, 158, 11, 0.1); color: #d97706; border: 1px solid rgba(245, 158, 11, 0.2);
}
.status-badge .dot {
    width: 6px; height: 6px; border-radius: 50%;
}
.status-badge.sent .dot { background: #10b981; }
.status-badge.draft .dot { background: #6b7280; }
.status-badge.sending .dot { background: #f59e0b; animation: pulse 1.5s infinite; }

.stats-container {
    display: flex; gap: 16px; align-items: center;
}
.stat-pill {
    display: flex; flex-direction: column;
}
.stat-val { font-weight: 700; color: var(--text-primary); font-size: .875rem; }
.stat-lbl { font-size: .68rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: .02em; }

.cmp-actions {
    display: flex; gap: 8px; justify-content: flex-end;
}
.btn-action-delete {
    width: 32px; height: 32px; border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    color: #ef4444; background: white;
    border: 1.5px solid var(--border-color); cursor: pointer; transition: all .15s;
}
.btn-action-delete:hover { border-color: #ef4444; background: #fef2f2; }

.empty-state {
    padding: 48px 24px; text-align: center; color: var(--text-muted);
}
.empty-state i {
    font-size: 2.5rem; color: rgba(14, 165, 233, 0.2); margin-bottom: 16px;
}
.empty-state p { font-size: .875rem; margin-bottom: 20px; }
</style>
@endpush

@section('content')

{{-- ═══ HERO ═══ --}}
<div class="campaigns-hero">
    <div class="campaigns-hero-content">
        <h2><i class="fas fa-paper-plane" style="margin-right:10px;opacity:.8;"></i>Marketing Campaigns</h2>
        <p>Send bulk email broadcasts, newsletters, and promotional announcements to your users.</p>
    </div>
    <div class="campaigns-hero-actions">
        <a href="{{ route('superadmin.email.campaigns.create') }}" class="btn-new-campaign">
            <i class="fas fa-plus"></i> New Campaign
        </a>
    </div>
</div>

{{-- ═══ LISTING ═══ --}}
<div class="cmp-card">
    <div class="cmp-card-header">
        <div>
            <div class="cmp-card-title">All Campaigns</div>
            <div class="cmp-card-desc">Broadcast analytics and tracking history.</div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="cmp-table">
            <thead>
                <tr>
                    <th>Campaign</th>
                    <th>Email Subject</th>
                    <th>Target Audience</th>
                    <th>Status</th>
                    <th>Delivery Stats</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($campaigns as $campaign)
                    <tr>
                        <td>
                            <div class="cmp-name">{{ $campaign->name }}</div>
                        </td>
                        <td>
                            <div class="cmp-subject">{{ $campaign->subject }}</div>
                        </td>
                        <td>
                            <span class="audience-badge">{{ ucfirst($campaign->target_audience) }}</span>
                        </td>
                        <td>
                            @if($campaign->status == 'sent')
                                <span class="status-badge sent">
                                    <span class="dot"></span> Sent
                                </span>
                            @elseif($campaign->status == 'sending')
                                <span class="status-badge sending">
                                    <span class="dot"></span> Sending
                                </span>
                            @else
                                <span class="status-badge draft">
                                    <span class="dot"></span> Draft
                                </span>
                            @endif
                        </td>
                        <td>
                            <div class="stats-container">
                                <div class="stat-pill">
                                    <span class="stat-val">{{ number_format($campaign->sent_count ?? 0) }}</span>
                                    <span class="stat-lbl">Sent</span>
                                </div>
                                <div style="width: 1.5px; height: 20px; background: var(--border-color);"></div>
                                <div class="stat-pill">
                                    <span class="stat-val">{{ number_format($campaign->open_count ?? 0) }}</span>
                                    <span class="stat-lbl">Opens</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="cmp-actions">
                                <form action="{{ route('superadmin.email.campaigns.destroy', $campaign->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this campaign?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action-delete" title="Delete Campaign">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <i class="fas fa-paper-plane"></i>
                                <h4>No campaigns created yet</h4>
                                <p>Launch a new marketing broadcast to engage with your customers or vendors.</p>
                                <a href="{{ route('superadmin.email.campaigns.create') }}" class="btn btn-primary btn-sm rounded-pill px-4">Create Campaign</a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($campaigns->hasPages())
        <div style="padding: 16px 20px; border-top: 1px solid var(--border-color);">
            {{ $campaigns->links() }}
        </div>
    @endif
</div>

@endsection
