@extends('layouts.app')

@section('title', 'Email Templates')
@section('page_title', 'Email Templates')

@section('sidebar')
    @include('superadmin.partials.sidebar')
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="dashboard-card">
            <div class="dashboard-card-header d-flex justify-content-between align-items-center">
                <h3>All Templates</h3>
                <a href="{{ route('superadmin.email.templates.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> New Template
                </a>
            </div>
            <div class="dashboard-card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Name</th>
                                <th>Subject</th>
                                <th>Variables</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($templates as $template)
                                <tr>
                                    <td class="ps-4 fw-bold">{{ $template->name }}</td>
                                    <td>{{ $template->subject }}</td>
                                    <td>
                                        @foreach($template->variables ?? [] as $var)
                                            <span class="badge bg-light text-dark">{{ $var }}</span>
                                        @endforeach
                                    </td>
                                    <td>
                                        @if($template->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="{{ route('superadmin.email.templates.edit', $template->id) }}" class="btn btn-sm btn-outline-secondary me-1">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('superadmin.email.templates.destroy', $template->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this template?')">
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
                                    <td colspan="5" class="text-center py-5 text-muted">No templates found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="dashboard-card-footer">
                {{ $templates->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
