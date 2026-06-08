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

{{-- CSS classes in dashboard.css (admin-page-card, admin-table, btn-xs, admin-search-form, etc.) --}}

<div class="admin-page-card">
    {{-- Header --}}
    <div class="admin-page-header">
        <div class="admin-page-header-left">
            <h2><i class="fas fa-envelope text-purple"></i> Contact Messages</h2>
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
                                    <span class="unread-dot"></span>
                                @endif
                                {{ $message->subject }}
                            </div>
                            <div class="msg-preview">{{ $message->message }}</div>
                        </td>
                        <td>
                            @if($message->is_read)
                                <span class="badge badge-secondary">Read</span>
                            @else
                                <span class="badge badge-primary font-bold">Unread</span>
                            @endif
                        </td>
                        <td class="text-nowrap">{{ $message->created_at->format('M d, Y') }}</td>
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
<div id="messageModal" class="admin-modal">
    <div class="admin-modal-panel">
        <div class="admin-modal-header">
            <h3 id="modal-subject">—</h3>
            <button onclick="closeMessageModal()" class="admin-modal-close">&times;</button>
        </div>
        <div class="admin-modal-body">
            <div class="d-flex gap-3 mb-3 align-items-center">
                <div class="modal-initials" id="modal-initials">?</div>
                <div>
                    <div class="font-semibold text-secondary-900" id="modal-name">—</div>
                    <div class="text-xs text-secondary-400" id="modal-email">—</div>
                </div>
                <div class="ms-auto text-xs text-secondary-300" id="modal-date">—</div>
            </div>
            <div class="modal-message-body" id="modal-message">—</div>
        </div>
        <div class="admin-modal-footer">
            <button onclick="closeMessageModal()" class="btn-xs btn-xs-sec">Close</button>
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
