@extends('layouts.app')

@section('title', 'Create Email Template')
@section('page_title', 'New Email Template')

@section('sidebar')
    @include('superadmin.partials.sidebar')
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3>Template Details</h3>
            </div>
            <div class="dashboard-card-body">
                <form action="{{ route('superadmin.email.templates.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label">Internal Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Welcome Email" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email Subject</label>
                        <input type="text" name="subject" class="form-control" placeholder="e.g. Welcome to BuyNiger!" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Available Variables (comma separated)</label>
                        <input type="text" name="variables" class="form-control" placeholder="e.g. {name}, {order_id}, {url}">
                        <div class="form-text">These can be used in the body content.</div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">HTML Content</label>
                        <textarea name="body" class="form-control" rows="15" placeholder="<html>...</html>" required></textarea>
                        <!-- In a real app, integrate TinyMCE/Quill here -->
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('superadmin.email.templates.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Create Template</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
