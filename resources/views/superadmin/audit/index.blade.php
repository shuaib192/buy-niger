@extends('layouts.app')

@section('title', 'Audit Logs')
@section('page_title', 'System Audit Logs')

@section('sidebar')
    @include('superadmin.partials.sidebar')
@endsection

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3>Activity Log</h3>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-filter"></i> Filter</button>
                    <button class="btn btn-sm btn-outline-primary"><i class="fas fa-download"></i> Export CSV</button>
                </div>
            </div>
            <div class="dashboard-card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Timestamp</th>
                                <th>User</th>
                                <th>Action</th>
                                <th>Subject</th>
                                <th>IP Address</th>
                                <th>Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                                <tr>
                                    <td>{{ $log->created_at->format('M d, Y H:i:s') }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            @if($log->user)
                                                <img src="{{ $log->user->avatar_url }}" class="rounded-circle" width="24" height="24">
                                                <span>{{ $log->user->name }}</span>
                                            @else
                                                <span class="text-muted">System</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td><span class="badge bg-secondary">{{ $log->action }}</span></td>
                                    <td>{{ $log->model_type }} #{{ $log->model_id }}</td>
                                    <td>{{ $log->ip_address }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-light" data-bs-toggle="tooltip" title="{{ json_encode($log->new_values) }}">
                                            View
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">No audit logs found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $logs->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
