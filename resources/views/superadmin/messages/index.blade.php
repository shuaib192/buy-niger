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
<div class="row g-4">
    <div class="col-12">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <h3>Contact Messages</h3>
                    <div class="d-flex gap-2">
                        <form action="" method="GET" class="d-flex">
                            <input type="text" name="search" class="form-control form-control-sm" placeholder="Search..." value="{{ request('search') }}">
                        </form>
                    </div>
                </div>
            </div>
            <div class="dashboard-card-body">
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Subject</th>
                                <th>Message</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($messages as $message)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $message->name }}</div>
                                        <small class="text-muted">{{ $message->email }}</small>
                                    </td>
                                    <td>{{ $message->subject }}</td>
                                    <td>{{ Str::limit($message->message, 50) }}</td>
                                    <td>
                                        @if($message->is_read)
                                            <span class="badge badge-success">Read</span>
                                        @else
                                            <span class="badge badge-warning">Unread</span>
                                        @endif
                                    </td>
                                    <td>{{ $message->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary">
                                            View
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">No messages found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $messages->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
