@extends('layouts.app')

@section('title', 'Edit Email Template')
@section('page_title', 'Edit Email Template')

@section('sidebar')
    @include('superadmin.partials.sidebar')
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3>Edit Template: {{ $template->name }}</h3>
            </div>
            <div class="dashboard-card-body">
                <form action="{{ route('superadmin.email.templates.update', $template->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label class="form-label">Internal Name</label>
                        <input type="text" name="name" class="form-control" value="{{ $template->name }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email Subject</label>
                        <input type="text" name="subject" class="form-control" value="{{ $template->subject }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Available Variables (comma separated)</label>
                        <input type="text" name="variables" class="form-control" value="{{ implode(', ', $template->variables ?? []) }}">
                    </div>

                    <div class="mb-4">
                        <label class="form-label">HTML Content</label>
                        <textarea name="body" class="form-control" rows="15" required>{{ $template->body }}</textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('superadmin.email.templates.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Template</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
