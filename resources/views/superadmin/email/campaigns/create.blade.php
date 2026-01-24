@extends('layouts.app')

@section('title', 'New Campaign')
@section('page_title', 'Create Email Campaign')

@section('sidebar')
    @include('superadmin.partials.sidebar')
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3>Campaign Details</h3>
            </div>
            <div class="dashboard-card-body">
                <form action="{{ route('superadmin.email.campaigns.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label">Campaign Name (Internal)</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Black Friday 2025" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email Subject</label>
                        <input type="text" name="subject" class="form-control" placeholder="e.g. Huge Savings Inside!" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Select Template</label>
                        <select name="template_id" class="form-select" required>
                            <option value="">-- Choose a Template --</option>
                            @foreach($templates as $template)
                                <option value="{{ $template->id }}">{{ $template->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Target Audience</label>
                        <select name="target_audience" class="form-select" required>
                            <option value="all">All Users</option>
                            <option value="customers">Customers Only</option>
                            <option value="vendors">Vendors Only</option>
                            <option value="custom">Custom Segment</option>
                        </select>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('superadmin.email.campaigns.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Create Draft</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
