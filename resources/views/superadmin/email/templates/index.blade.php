{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    View: Admin — Email Templates — Premium v2.0
--}}
@extends('layouts.app')

@section('title', 'Email Templates')
@section('page_title', 'Email Templates')

@section('sidebar')
    @include('superadmin.partials.sidebar')
@endsection

@push('styles')
<style>
.templates-hero {
    background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
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
.templates-hero::before {
    content: '';
    position: absolute;
    top: -60px; right: -40px;
    width: 200px; height: 200px;
    background: rgba(255,255,255,.1);
    border-radius: 50%;
}
.templates-hero-content { position: relative; z-index: 1; }
.templates-hero-content h2 {
    color: white; font-size: 1.375rem; font-weight: 800;
    font-family: 'Outfit', sans-serif; margin-bottom: 4px;
}
.templates-hero-content p { color: rgba(255,255,255,.8); font-size: .875rem; margin: 0; }
.templates-hero-actions { position: relative; z-index: 1; }

.btn-new-template {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 11px 22px; border-radius: 12px; font-size: .875rem;
    font-weight: 700; background: white; color: #4f46e5;
    border: none; cursor: pointer; transition: all .2s;
    text-decoration: none; box-shadow: 0 4px 14px rgba(0,0,0,0.1);
}
.btn-new-template:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(0,0,0,0.15); color: #4338ca; }

/* Table styling */
.tpl-card {
    background: var(--surface);
    border: 1.5px solid var(--border-color);
    border-radius: 20px; overflow: hidden;
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);
}
.tpl-card-header {
    padding: 20px 24px;
    border-bottom: 1px solid var(--border-color);
    display: flex; align-items: center; justify-content: space-between;
}
.tpl-card-title {
    font-size: .95rem; font-weight: 800; color: var(--text-primary); font-family: 'Outfit', sans-serif;
}
.tpl-card-desc {
    font-size: .75rem; color: var(--text-muted); margin-top: 2px;
}

.tpl-table {
    width: 100%; border-collapse: collapse; text-align: left;
}
.tpl-table th {
    padding: 14px 20px; font-size: .75rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .05em; color: var(--text-secondary);
    background: var(--bg-surface); border-bottom: 1.5px solid var(--border-color);
}
.tpl-table td {
    padding: 18px 20px; font-size: .8125rem; border-bottom: 1px solid var(--border-color);
    vertical-align: middle; color: var(--text-secondary);
}
.tpl-table tr:last-child td { border-bottom: none; }
.tpl-table tr:hover { background: rgba(99, 102, 241, 0.02); }

.tpl-name {
    font-size: .875rem; font-weight: 700; color: var(--text-primary); font-family: 'Outfit', sans-serif;
}
.tpl-subject {
    font-size: .8125rem; color: var(--text-muted);
}
.variable-badge {
    display: inline-block; padding: 3px 8px; border-radius: 6px;
    background: rgba(99, 102, 241, 0.08); color: #4f46e5;
    font-size: .7rem; font-weight: 600; margin: 2px;
    border: 1px solid rgba(99, 102, 241, 0.15);
}
.status-badge {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 4px 10px; border-radius: 20px; font-size: .72rem; font-weight: 700;
}
.status-badge.active {
    background: rgba(16, 185, 129, 0.1); color: #059669; border: 1px solid rgba(16, 185, 129, 0.2);
}
.status-badge.inactive {
    background: rgba(107, 114, 128, 0.1); color: #4b5563; border: 1px solid rgba(107, 114, 128, 0.2);
}
.status-badge .dot {
    width: 6px; height: 6px; border-radius: 50%;
}
.status-badge.active .dot { background: #10b981; }
.status-badge.inactive .dot { background: #6b7280; }

.tpl-actions {
    display: flex; gap: 8px; justify-content: flex-end;
}
.btn-action-edit {
    width: 32px; height: 32px; border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    color: var(--text-secondary); background: white;
    border: 1.5px solid var(--border-color); cursor: pointer; transition: all .15s;
    text-decoration: none;
}
.btn-action-edit:hover { border-color: #4f46e5; color: #4f46e5; background: rgba(99, 102, 241, 0.02); }

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
    font-size: 2.5rem; color: rgba(99, 102, 241, 0.2); margin-bottom: 16px;
}
.empty-state p { font-size: .875rem; margin-bottom: 20px; }
</style>
@endpush

@section('content')

{{-- ═══ HERO ═══ --}}
<div class="templates-hero">
    <div class="templates-hero-content">
        <h2><i class="fas fa-envelopes-bulk" style="margin-right:10px;opacity:.8;"></i>Email Templates</h2>
        <p>Design and customize structured HTML emails triggered by system actions.</p>
    </div>
    <div class="templates-hero-actions">
        <a href="{{ route('superadmin.email.templates.create') }}" class="btn-new-template">
            <i class="fas fa-plus"></i> New Template
        </a>
    </div>
</div>

{{-- ═══ LISTING ═══ --}}
<div class="tpl-card">
    <div class="tpl-card-header">
        <div>
            <div class="tpl-card-title">All Email Templates</div>
            <div class="tpl-card-desc">Configure dynamic HTML templates using variables.</div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="tpl-table">
            <thead>
                <tr>
                    <th>Template Name</th>
                    <th>Email Subject</th>
                    <th>Supported Variables</th>
                    <th>Status</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($templates as $template)
                    <tr>
                        <td>
                            <div class="tpl-name">{{ $template->name }}</div>
                        </td>
                        <td>
                            <div class="tpl-subject">{{ $template->subject }}</div>
                        </td>
                        <td>
                            @forelse($template->variables ?? [] as $var)
                                <span class="variable-badge">{{ $var }}</span>
                            @empty
                                <span class="text-muted small">None</span>
                            @endforelse
                        </td>
                        <td>
                            @if($template->is_active ?? true)
                                <span class="status-badge active">
                                    <span class="dot"></span> Active
                                </span>
                            @else
                                <span class="status-badge inactive">
                                    <span class="dot"></span> Inactive
                                </span>
                            @endif
                        </td>
                        <td>
                            <div class="tpl-actions">
                                <a href="{{ route('superadmin.email.templates.edit', $template->id) }}" class="btn-action-edit" title="Edit Template">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('superadmin.email.templates.destroy', $template->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this template?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action-delete" title="Delete Template">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">
                            <div class="empty-state">
                                <i class="fas fa-envelope-open-text"></i>
                                <h4>No templates created yet</h4>
                                <p>Get started by creating your first transactional mail template.</p>
                                <a href="{{ route('superadmin.email.templates.create') }}" class="btn btn-primary btn-sm rounded-pill px-4">Create Template</a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($templates->hasPages())
        <div style="padding: 16px 20px; border-top: 1px solid var(--border-color);">
            {{ $templates->links() }}
        </div>
    @endif
</div>

@endsection
