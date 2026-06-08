@extends('layouts.app')

@section('title', 'Contact Messages')
@section('page_title', 'Contact Messages')

@section('sidebar')
    @include('superadmin.partials.sidebar')
@endsection

@section('content')
@php
    $prefix = request()->is('admin*') ? 'admin.' : 'superadmin.';
@endphp

<style>
    /* ── Messages page: zero-overflow layout ── */
    .admin-page-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 1px 4px rgba(0,0,0,.06);
        overflow: hidden;
        width: 100%;
        box-sizing: border-box;
    }
    .admin-page-header {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 16px 20px;
        border-bottom: 1px solid #f1f5f9;
    }
    .admin-page-header-left h2 {
        font-size: 1rem;
        font-weight: 700;
        margin: 0;
        color: #0f172a;
    }
    .admin-search-form {
        display: flex;
        gap: 8px;
        align-items: center;
        flex-wrap: wrap;
    }
    .admin-search-form input[type="text"] {
        font-size: 0.8125rem;
        padding: 7px 12px;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        background: #f8fafc;
        color: #334155;
        outline: none;
        transition: border-color .2s;
        min-width: 160px;
        flex: 1 1 160px;
    }
    .admin-search-form input:focus { border-color: #3b82f6; background: #fff; }

    /* ── Responsive table ── */
    .admin-table-wrap {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    .admin-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 520px;
    }
    .admin-table thead th {
        padding: 10px 16px;
        font-size: 0.6875rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: #94a3b8;
        background: #f8fafc;
        white-space: nowrap;
        border-bottom: 1px solid #f1f5f9;
    }
    .admin-table tbody td {
        padding: 12px 16px;
        font-size: 0.8125rem;
        color: #334155;
        border-bottom: 1px solid #f8fafc;
        vertical-align: middle;
    }
    .admin-table tbody tr:last-child td { border-bottom: none; }
    .admin-table tbody tr.unread-row { background: #fafbff; }
    .admin-table tbody tr:hover { background: #f8fafc; }

    .sender-name { font-weight: 600; color: #0f172a; }
    .sender-email { color: #94a3b8; font-size: 0.6875rem; display: block; }

    .msg-subject { font-weight: 600; color: #1e293b; }
    .msg-preview {
        font-size: 0.75rem;
        color: #94a3b8;
        margin-top: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 220px;
    }

    .btn-xs {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 5px 12px; font-size: 0.75rem; font-weight: 600;
        border-radius: 8px; border: 1.5px solid transparent;
        cursor: pointer; white-space: nowrap;
        transition: all .15s; text-decoration: none;
        background: none;
    }
    .btn-xs-primary { border-color:#3b82f6;color:#1d4ed8;background:#dbeafe; }
    .btn-xs-primary:hover { background:#bfdbfe; }
    .btn-xs-sec     { border-color:#e2e8f0;color:#475569;background:#f8fafc; }
    .btn-xs-sec:hover     { background:#e2e8f0; }

    .pager-wrap {
        padding: 14px 20px;
        border-top: 1px solid #f1f5f9;
        overflow-x: auto;
        width: 100%;
    }
    .empty-state { padding: 48px 20px; text-align: center; color: #94a3b8; }
    .empty-state i { font-size: 2rem; margin-bottom: 10px; display: block; }

    @media (max-width: 640px) {
        .admin-page-header {
            flex-direction: column;
            align-items: stretch;
            gap: 12px;
            padding: 12px 14px;
        }
        .admin-page-header-left {
            width: 100%;
        }
        .admin-search-form {
            width: 100%;
            flex-direction: column;
            align-items: stretch;
            gap: 8px;
        }
        .admin-search-form input[type="text"],
        .admin-search-form button {
            width: 100%;
            margin: 0;
        }
    }
</style>

<div class="admin-page-card">
    {{-- Header --}}
    <div class="admin-page-header">
        <div class="admin-page-header-left">
            <h2><i class="fas fa-envelope" style="color:#8b5cf6;"></i> Contact Messages</h2>
        </div>

        <form action="" method="GET" class="admin-search-form">
            <input type="text" name="search"
                   placeholder="Search messages…"
                   value="{{ request('search') }}">
            <button type="submit" class="btn-xs btn-xs-sec">
                <i class="fas fa-search"></i> Search
            </button>
        </form>
    </div>

    {{-- Table --}}
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Sender</th>
                    <th>Subject & Preview</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($messages as $message)
                    <tr class="{{ $message->is_read ? '' : 'unread-row' }}">
                        <td>
                            <span class="sender-name">{{ $message->name }}</span>
                            <span class="sender-email">{{ $message->email }}</span>
                        </td>
                        <td>
                            <div class="msg-subject">
                                @if(!$message->is_read)
                                    <span style="display:inline-block;width:7px;height:7px;background:#3b82f6;border-radius:50%;margin-right:6px;vertical-align:middle;"></span>
                                @endif
                                {{ $message->subject }}
                            </div>
                            <div class="msg-preview">{{ $message->message }}</div>
                        </td>
                        <td>
                            @if($message->is_read)
                                <span class="badge" style="background:#f1f5f9;color:#64748b;">Read</span>
                            @else
                                <span class="badge" style="background:#dbeafe;color:#1d4ed8;font-weight:700;">Unread</span>
                            @endif
                        </td>
                        <td style="white-space:nowrap;">{{ $message->created_at->format('M d, Y') }}</td>
                        <td>
                            <button class="btn-xs btn-xs-primary"
                                    onclick="openMessageModal({{ $message->id }}, '{{ addslashes($message->name) }}', '{{ addslashes($message->email) }}', '{{ addslashes($message->subject) }}', '{{ addslashes($message->message) }}', '{{ $message->created_at->format('M d, Y') }}')">
                                <i class="fas fa-eye"></i> View
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">
                            <div class="empty-state">
                                <i class="fas fa-envelope-open-text"></i>
                                No messages found.
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="pager-wrap">
        {{ $messages->links() }}
    </div>
</div>

{{-- Message View Modal --}}
<div id="messageModal" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(15,23,42,.45);align-items:center;justify-content:center;padding:16px;">
    <div style="background:#fff;border-radius:20px;width:100%;max-width:520px;box-shadow:0 20px 60px rgba(0,0,0,.25);overflow:hidden;">
        <div style="padding:18px 24px;border-bottom:1px solid #f1f5f9;display:flex;justify-content:space-between;align-items:center;">
            <h3 style="margin:0;font-size:1rem;font-weight:700;color:#0f172a;" id="modal-subject">—</h3>
            <button onclick="closeMessageModal()"
                    style="background:none;border:none;font-size:1.25rem;color:#94a3b8;cursor:pointer;line-height:1;">
                &times;
            </button>
        </div>
        <div style="padding:20px 24px;">
            <div style="display:flex;gap:12px;margin-bottom:16px;align-items:center;">
                <div style="width:40px;height:40px;border-radius:50%;background:#dbeafe;display:flex;align-items:center;justify-content:center;font-weight:700;color:#1d4ed8;font-size:1rem;" id="modal-initials">?</div>
                <div>
                    <div style="font-weight:600;color:#0f172a;" id="modal-name">—</div>
                    <div style="font-size:0.75rem;color:#94a3b8;" id="modal-email">—</div>
                </div>
                <div style="margin-left:auto;font-size:0.75rem;color:#cbd5e1;" id="modal-date">—</div>
            </div>
            <div style="background:#f8fafc;border-radius:12px;padding:16px;font-size:0.875rem;color:#334155;line-height:1.7;white-space:pre-wrap;word-break:break-word;" id="modal-message">—</div>
        </div>
        <div style="padding:14px 24px;border-top:1px solid #f1f5f9;text-align:right;">
            <button onclick="closeMessageModal()" class="btn-xs btn-xs-sec" style="border:1.5px solid #e2e8f0;">
                Close
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openMessageModal(id, name, email, subject, message, date) {
    document.getElementById('modal-subject').textContent  = subject;
    document.getElementById('modal-name').textContent     = name;
    document.getElementById('modal-email').textContent    = email;
    document.getElementById('modal-date').textContent     = date;
    document.getElementById('modal-message').textContent  = message;
    document.getElementById('modal-initials').textContent = name.charAt(0).toUpperCase();
    const modal = document.getElementById('messageModal');
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function closeMessageModal() {
    document.getElementById('messageModal').style.display = 'none';
    document.body.style.overflow = '';
}
document.getElementById('messageModal').addEventListener('click', function(e) {
    if (e.target === this) closeMessageModal();
});
</script>
@endpush
@endsection
