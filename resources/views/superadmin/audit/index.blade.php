{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    Written by Shuaibu Abdulmumin
    View: Admin — System Audit Logs — Premium v2.0
--}}
@extends('layouts.app')

@section('title', 'Audit Logs')
@section('page_title', 'System Audit Logs')

@section('sidebar')
    @include('superadmin.partials.sidebar')
@endsection

@push('styles')
<style>
.audit-header-banner {
    background: linear-gradient(135deg, #1e1b4b 0%, #312e81 50%, #4c1d95 100%);
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
.audit-header-banner::before {
    content: '';
    position: absolute;
    top: -60px; right: -60px;
    width: 200px; height: 200px;
    background: rgba(139,92,246,.15);
    border-radius: 50%;
}
.audit-header-banner::after {
    content: '';
    position: absolute;
    bottom: -80px; left: 30%;
    width: 160px; height: 160px;
    background: rgba(79,70,229,.12);
    border-radius: 50%;
}
.audit-header-content { position: relative; z-index: 1; }
.audit-header-content h2 {
    color: white;
    font-size: 1.375rem;
    font-weight: 800;
    font-family: 'Outfit', sans-serif;
    margin-bottom: 4px;
}
.audit-header-content p { color: rgba(255,255,255,.65); font-size: .875rem; margin: 0; }
.audit-header-actions { position: relative; z-index: 1; display: flex; gap: 10px; flex-wrap: wrap; }
.audit-stat-pills {
    display: flex; gap: 10px; flex-wrap: wrap;
    margin-bottom: 24px;
}
.audit-stat-pill {
    display: flex; align-items: center; gap: 10px;
    padding: 12px 18px;
    background: var(--surface);
    border: 1.5px solid var(--border-color);
    border-radius: 14px;
    flex: 1; min-width: 140px;
    transition: all .2s;
}
.audit-stat-pill:hover { border-color: #8b5cf6; box-shadow: 0 0 0 3px rgba(139,92,246,.08); }
.asp-icon {
    width: 38px; height: 38px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: .95rem; flex-shrink: 0;
}
.asp-icon.purple { background: rgba(139,92,246,.1); color: #7c3aed; }
.asp-icon.blue   { background: rgba(14,165,233,.1);  color: #0284c7; }
.asp-icon.green  { background: rgba(16,185,129,.1);  color: #059669; }
.asp-icon.orange { background: rgba(245,158,11,.1);  color: #d97706; }
.asp-label { font-size: .7rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: .04em; }
.asp-value { font-size: 1.1rem; font-weight: 800; color: var(--text-primary); line-height: 1; }

.audit-filters {
    display: flex; align-items: center; gap: 8px; flex-wrap: wrap;
    padding: 14px 20px; background: var(--surface); border-bottom: 1px solid var(--border-color);
}
.audit-filter-select {
    padding: 7px 12px; border-radius: 9px; font-size: .8125rem; font-weight: 500;
    border: 1.5px solid var(--border-color); background: white; color: var(--text-primary);
    cursor: pointer; transition: all .15s; min-width: 140px;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 10px center;
    padding-right: 30px;
}
.audit-filter-select:focus { outline: none; border-color: #8b5cf6; box-shadow: 0 0 0 3px rgba(139,92,246,.1); }
.audit-search-wrap {
    display: flex; gap: 0; border: 1.5px solid var(--border-color);
    border-radius: 10px; overflow: hidden; margin-left: auto;
}
.audit-search-wrap input { border: none; font-size: .8125rem; padding: 8px 12px; min-width: 200px; }
.audit-search-wrap input:focus { outline: none; }
.audit-search-wrap button { border-radius: 0; padding: 8px 12px; border: none; background: #4f46e5; color: white; cursor: pointer; }

.log-row-actor { display: flex; align-items: center; gap: 8px; }
.log-actor-avatar {
    width: 28px; height: 28px; border-radius: 8px;
    background: linear-gradient(135deg, #4f46e5, #8b5cf6);
    display: flex; align-items: center; justify-content: center;
    color: white; font-size: .7rem; font-weight: 700; flex-shrink: 0;
}
.log-actor-avatar.system { background: linear-gradient(135deg, #64748b, #94a3b8); }
.log-actor-name { font-weight: 600; font-size: .8125rem; color: var(--text-primary); }
.log-actor-role { font-size: .7rem; color: var(--text-muted); }
.log-action-badge {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 4px 10px; border-radius: 7px;
    font-size: .7rem; font-weight: 700; text-transform: uppercase; letter-spacing: .04em;
}
.lab-create { background: rgba(16,185,129,.1); color: #059669; }
.lab-update { background: rgba(79,70,229,.1);  color: #4338ca; }
.lab-delete { background: rgba(244,63,94,.1);  color: #be123c; }
.lab-login  { background: rgba(14,165,233,.1); color: #0284c7; }
.lab-other  { background: rgba(100,116,139,.1);color: #475569; }
.log-model-tag {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 3px 9px; border-radius: 6px;
    background: rgba(100,116,139,.08); border: 1px solid rgba(100,116,139,.15);
    font-size: .75rem; font-weight: 600; color: var(--text-secondary);
}
.log-ip code {
    font-size: .75rem; color: var(--text-muted);
    background: rgba(100,116,139,.06); padding: 2px 6px; border-radius: 5px;
}
.log-time-cell .date { font-size: .8125rem; font-weight: 600; color: var(--text-primary); }
.log-time-cell .time { font-size: .7rem; color: var(--text-muted); margin-top: 1px; }

.btn-inspect {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 5px 12px; border-radius: 8px; font-size: .78rem; font-weight: 600;
    background: rgba(79,70,229,.08); color: #4f46e5;
    border: 1.5px solid rgba(79,70,229,.2); cursor: pointer; transition: all .15s;
}
.btn-inspect:hover { background: #4f46e5; color: white; }

/* Modal Styles */
.modal-overlay {
    position: fixed; inset: 0; z-index: 9999;
    background: rgba(0,0,0,.55); backdrop-filter: blur(4px);
    display: flex; align-items: center; justify-content: center;
    padding: 20px;
    opacity: 0; pointer-events: none; transition: opacity .2s;
}
.modal-overlay.show { opacity: 1; pointer-events: all; }
.modal-box {
    background: white; border-radius: 20px;
    width: 100%; max-width: 600px;
    box-shadow: 0 24px 80px rgba(0,0,0,.25);
    transform: translateY(20px); transition: transform .25s;
    overflow: hidden;
}
.modal-overlay.show .modal-box { transform: translateY(0); }
.modal-box-header {
    background: linear-gradient(135deg, #1e1b4b, #4c1d95);
    padding: 20px 24px; display: flex; align-items: center; justify-content: space-between;
}
.modal-box-title { color: white; font-size: 1rem; font-weight: 700; font-family: 'Outfit', sans-serif; }
.modal-close-btn {
    width: 30px; height: 30px; border-radius: 8px;
    background: rgba(255,255,255,.15); border: none; cursor: pointer;
    color: white; font-size: .85rem; display: flex; align-items: center; justify-content: center;
    transition: background .15s;
}
.modal-close-btn:hover { background: rgba(255,255,255,.25); }
.modal-box-body { padding: 24px; }
.payload-code {
    background: #0f172a; color: #e2e8f0;
    border-radius: 12px; padding: 16px;
    font-size: .78rem; line-height: 1.7;
    font-family: 'Courier New', monospace;
    max-height: 280px; overflow-y: auto;
    border: none;
}
.payload-code::-webkit-scrollbar { width: 6px; }
.payload-code::-webkit-scrollbar-track { background: #1e293b; }
.payload-code::-webkit-scrollbar-thumb { background: #475569; border-radius: 3px; }
</style>
@endpush

@section('content')

{{-- ═══ HEADER BANNER ═══ --}}
<div class="audit-header-banner">
    <div class="audit-header-content">
        <h2><i class="fas fa-shield-halved" style="margin-right:10px;opacity:.8;"></i>System Audit Logs</h2>
        <p>Immutable records of administrative actions, user changes & system events.</p>
    </div>
    <div class="audit-header-actions">
        <button class="btn btn-sm" style="background:rgba(255,255,255,.15);color:white;border:1.5px solid rgba(255,255,255,.25);border-radius:10px;">
            <i class="fas fa-file-csv"></i> Export CSV
        </button>
    </div>
</div>

{{-- ═══ STAT PILLS ═══ --}}
<div class="audit-stat-pills">
    <div class="audit-stat-pill">
        <div class="asp-icon purple"><i class="fas fa-list-check"></i></div>
        <div>
            <div class="asp-label">Total Logs</div>
            <div class="asp-value">{{ $logs->total() }}</div>
        </div>
    </div>
    <div class="audit-stat-pill">
        <div class="asp-icon blue"><i class="fas fa-user-shield"></i></div>
        <div>
            <div class="asp-label">Unique Actors</div>
            <div class="asp-value">{{ $logs->pluck('user_id')->filter()->unique()->count() }}</div>
        </div>
    </div>
    <div class="audit-stat-pill">
        <div class="asp-icon green"><i class="fas fa-calendar-day"></i></div>
        <div>
            <div class="asp-label">Today's Events</div>
            <div class="asp-value">{{ $logs->filter(fn($l) => $l->created_at->isToday())->count() }}</div>
        </div>
    </div>
    <div class="audit-stat-pill">
        <div class="asp-icon orange"><i class="fas fa-triangle-exclamation"></i></div>
        <div>
            <div class="asp-label">Delete Actions</div>
            <div class="asp-value">{{ $logs->filter(fn($l) => str_contains(strtolower($l->action), 'delete'))->count() }}</div>
        </div>
    </div>
</div>

{{-- ═══ MAIN TABLE CARD ═══ --}}
<div class="dashboard-card">
    <div class="dashboard-card-header">
        <div>
            <h3><i class="fas fa-clock-rotate-left" style="color:#8b5cf6;margin-right:8px;"></i>Activity Timeline</h3>
            <div style="font-size:.8rem;color:var(--text-muted);margin-top:2px;">Complete chronological log of all platform events.</div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="audit-filters">
        <select class="audit-filter-select">
            <option value="">All Actions</option>
            <option value="create">Create</option>
            <option value="update">Update</option>
            <option value="delete">Delete</option>
            <option value="login">Login</option>
        </select>
        <div class="audit-search-wrap">
            <input type="text" placeholder="Search actor, model, IP..." id="auditSearch">
            <button><i class="fas fa-search"></i></button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="data-table" id="auditTable">
            <thead>
                <tr>
                    <th>Timestamp</th>
                    <th>Actor</th>
                    <th>Action</th>
                    <th>Target</th>
                    <th>IP Address</th>
                    <th style="text-align:right;padding-right:20px;">Inspect</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    @php
                        $action = strtolower($log->action);
                        $actionClass = str_contains($action,'create') ? 'lab-create'
                            : (str_contains($action,'update') ? 'lab-update'
                            : (str_contains($action,'delete') ? 'lab-delete'
                            : (str_contains($action,'login')  ? 'lab-login'
                            : 'lab-other')));
                        $actionIcon = str_contains($action,'create') ? 'fa-plus'
                            : (str_contains($action,'update') ? 'fa-pen'
                            : (str_contains($action,'delete') ? 'fa-trash'
                            : (str_contains($action,'login')  ? 'fa-right-to-bracket'
                            : 'fa-bolt')));
                    @endphp
                    <tr>
                        <td>
                            <div class="log-time-cell">
                                <div class="date">{{ $log->created_at->format('d M Y') }}</div>
                                <div class="time">{{ $log->created_at->format('H:i:s') }}</div>
                            </div>
                        </td>
                        <td>
                            <div class="log-row-actor">
                                @if($log->user)
                                    <div class="log-actor-avatar">{{ strtoupper(substr($log->user->name, 0, 1)) }}</div>
                                    <div>
                                        <div class="log-actor-name">{{ $log->user->name }}</div>
                                        <div class="log-actor-role">{{ $log->user->role->name ?? 'User' }}</div>
                                    </div>
                                @else
                                    <div class="log-actor-avatar system"><i class="fas fa-robot" style="font-size:.65rem;"></i></div>
                                    <div>
                                        <div class="log-actor-name">System</div>
                                        <div class="log-actor-role">Automated</div>
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td>
                            <span class="log-action-badge {{ $actionClass }}">
                                <i class="fas {{ $actionIcon }}"></i> {{ $log->action }}
                            </span>
                        </td>
                        <td>
                            <span class="log-model-tag">
                                <i class="fas fa-database" style="font-size:.6rem;"></i>
                                {{ class_basename($log->model_type) }} #{{ $log->model_id }}
                            </span>
                        </td>
                        <td class="log-ip">
                            <code>{{ $log->ip_address ?? '—' }}</code>
                        </td>
                        <td style="text-align:right;padding-right:20px;">
                            <button class="btn-inspect" onclick="openAuditModal({{ $log->id }})">
                                <i class="fas fa-code"></i> Inspect
                            </button>

                            {{-- Hidden Modal --}}
                            <div class="modal-overlay" id="auditModal{{ $log->id }}">
                                <div class="modal-box">
                                    <div class="modal-box-header">
                                        <div class="modal-box-title">
                                            <i class="fas fa-file-code" style="margin-right:8px;opacity:.8;"></i>
                                            Payload State — {{ $log->action }}
                                        </div>
                                        <button class="modal-close-btn" onclick="closeAuditModal({{ $log->id }})">
                                            <i class="fas fa-xmark"></i>
                                        </button>
                                    </div>
                                    <div class="modal-box-body">
                                        <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;">
                                            <span class="log-action-badge {{ $actionClass }}">
                                                <i class="fas {{ $actionIcon }}"></i> {{ $log->action }}
                                            </span>
                                            <div style="font-size:.8rem;color:var(--text-muted);">
                                                by <strong>{{ $log->user->name ?? 'System' }}</strong>
                                                &bull; {{ $log->created_at->format('d M Y H:i:s') }}
                                            </div>
                                        </div>
                                        <div style="margin-bottom:8px;font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.04em;color:var(--text-muted);">
                                            JSON Payload
                                        </div>
                                        <pre class="payload-code">{{ json_encode($log->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                        <div style="margin-top:16px;display:flex;gap:8px;">
                                            <code style="font-size:.75rem;padding:5px 10px;background:rgba(100,116,139,.07);border-radius:7px;color:var(--text-secondary);">
                                                IP: {{ $log->ip_address ?? 'N/A' }}
                                            </code>
                                            <code style="font-size:.75rem;padding:5px 10px;background:rgba(100,116,139,.07);border-radius:7px;color:var(--text-secondary);">
                                                Model: {{ class_basename($log->model_type) }} #{{ $log->model_id }}
                                            </code>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <i class="fas fa-clipboard-list"></i>
                                <p>No audit logs found. System is running cleanly.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($logs->hasPages())
        <div style="padding:14px 20px;">
            {{ $logs->links() }}
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function openAuditModal(id) {
    const el = document.getElementById('auditModal' + id);
    if (el) { el.classList.add('show'); document.body.style.overflow = 'hidden'; }
}
function closeAuditModal(id) {
    const el = document.getElementById('auditModal' + id);
    if (el) { el.classList.remove('show'); document.body.style.overflow = ''; }
}
// Close on backdrop click
document.querySelectorAll('.modal-overlay').forEach(function(el) {
    el.addEventListener('click', function(e) {
        if (e.target === this) { this.classList.remove('show'); document.body.style.overflow = ''; }
    });
});
// Live search
const searchInput = document.getElementById('auditSearch');
if (searchInput) {
    searchInput.addEventListener('input', function() {
        const q = this.value.toLowerCase();
        document.querySelectorAll('#auditTable tbody tr').forEach(function(row) {
            row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    });
}
</script>
@endpush
