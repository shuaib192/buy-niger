@extends('layouts.app')

@section('title', 'Email Campaigns')
@section('page_title', 'Email Campaigns')

@section('sidebar')
    @include('superadmin.partials.sidebar')
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="dashboard-card">
            <div class="dashboard-card-header d-flex justify-content-between align-items-center">
                <h3>All Campaigns</h3>
                <a href="{{ route('superadmin.email.campaigns.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> New Campaign
                </a>
            </div>
            <div class="dashboard-card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Name</th>
                                <th>Subject</th>
                                <th>Audience</th>
                                <th>Status</th>
                                <th>Stats (Sent/Open)</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($campaigns as $campaign)
                                <tr>
                                    <td class="ps-4 fw-bold">{{ $campaign->name }}</td>
                                    <td>{{ $campaign->subject }}</td>
                                    <td><span class="badge bg-info text-dark">{{ ucfirst($campaign->target_audience) }}</span></td>
                                    <td>
                                        @if($campaign->status == 'sent')
                                            <span class="badge bg-success">Sent</span>
                                        @elseif($campaign->status == 'draft')
                                            <span class="badge bg-secondary">Draft</span>
                                        @elseif($campaign->status == 'sending')
                                            <span class="badge bg-warning text-dark">Sending</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $campaign->sent_count }} / {{ $campaign->open_count }}
                                    </td>
                                    <td class="text-end pe-4">
                                        <!-- Add Show/Send actions later -->
                                        <form action="{{ route('superadmin.email.campaigns.destroy', $campaign->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this campaign?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">No campaigns found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="dashboard-card-footer">
                {{ $campaigns->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
