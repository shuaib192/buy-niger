@extends('layouts.app')

@section('title', 'Dispute Management')
@section('page_title', 'Dispute Management')

@section('sidebar')
    @include('superadmin.partials.sidebar')
@endsection

@section('content')
@php
    $prefix = request()->is('admin*') ? 'admin.' : 'superadmin.';
@endphp
<div class="row g-4">
    <div class="col-12">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <h3>Disputes & Complaints</h3>
                    <form action="{{ route($prefix.'disputes') }}" method="GET" class="d-flex gap-2">
                        <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">All Statuses</option>
                            <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                            <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                            <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                            <option value="escalated" {{ request('status') == 'escalated' ? 'selected' : '' }}>Escalated</option>
                        </select>
                    </form>
                </div>
            </div>
            <div class="dashboard-card-body">
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Subject</th>
                                <th>User</th>
                                <th>Order</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($disputes as $dispute)
                                <tr>
                                    <td>#{{ $dispute->id }}</td>
                                    <td>
                                        <div class="fw-bold">{{ Str::limit($dispute->subject, 30) }}</div>
                                        <small class="text-muted">{{ Str::limit($dispute->description, 50) }}</small>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="{{ $dispute->user->avatar_url }}" class="rounded-circle" width="24" height="24">
                                            <span>{{ $dispute->user->name }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        @if($dispute->order)
                                            <a href="{{ route($prefix.'orders.show', $dispute->order->id) }}">
                                                #{{ $dispute->order->order_number }}
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($dispute->priority == 'critical') <span class="badge badge-danger">Critical</span>
                                        @elseif($dispute->priority == 'high') <span class="badge badge-orange">High</span>
                                        @elseif($dispute->priority == 'medium') <span class="badge badge-warning">Medium</span>
                                        @else <span class="badge badge-secondary">Low</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($dispute->status == 'open') <span class="badge badge-primary">Open</span>
                                        @elseif($dispute->status == 'resolved') <span class="badge badge-success">Resolved</span>
                                        @elseif($dispute->status == 'closed') <span class="badge badge-secondary">Closed</span>
                                        @else <span class="badge badge-danger">Escalated</span>
                                        @endif
                                    </td>
                                    <td>{{ $dispute->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#resolveModal{{ $dispute->id }}">
                                            Manage
                                        </button>

                                        <!-- Resolve Modal -->
                                        <div class="modal fade" id="resolveModal{{ $dispute->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form action="{{ route($prefix.'disputes.update', $dispute) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Manage Dispute #{{ $dispute->id }}</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label class="form-label">Status</label>
                                                                <select name="status" class="form-select">
                                                                    <option value="open" {{ $dispute->status == 'open' ? 'selected' : '' }}>Open</option>
                                                                    <option value="resolved" {{ $dispute->status == 'resolved' ? 'selected' : '' }}>Resolved</option>
                                                                    <option value="closed" {{ $dispute->status == 'closed' ? 'selected' : '' }}>Closed</option>
                                                                    <option value="escalated" {{ $dispute->status == 'escalated' ? 'selected' : '' }}>Escalated</option>
                                                                </select>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Resolution Notes</label>
                                                                <textarea name="resolution_notes" class="form-control" rows="3">{{ $dispute->resolution_notes }}</textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">No disputes found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $disputes->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
