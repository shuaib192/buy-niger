{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    View: Admin — Edit Email Template — Premium v2.0
--}}
@extends('layouts.app')

@section('title', 'Edit Email Template')
@section('page_title', 'Edit Email Template')

@section('sidebar')
    @include('superadmin.partials.sidebar')
@endsection

@push('styles')
<style>
.form-card {
    background: var(--surface);
    border: 1.5px solid var(--border-color);
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);
    margin-bottom: 24px;
}
.form-card-header {
    padding: 20px 24px;
    border-bottom: 1px solid var(--border-color);
    background: linear-gradient(135deg, rgba(79,70,229,.02), rgba(99,102,241,.02));
    display: flex;
    align-items: center;
    gap: 12px;
}
.form-card-header-icon {
    width: 36px; height: 36px; border-radius: 10px;
    background: rgba(99, 102, 241, 0.1); color: #4f46e5;
    display: flex; align-items: center; justify-content: center; font-size: 1rem;
}
.form-card-title {
    font-size: .95rem; font-weight: 800; color: var(--text-primary); font-family: 'Outfit', sans-serif;
}
.form-card-desc {
    font-size: .75rem; color: var(--text-muted); margin-top: 2px;
}
.form-card-body {
    padding: 24px;
}

.custom-form-group {
    margin-bottom: 20px;
}
.custom-label {
    display: block; margin-bottom: 8px;
    font-size: .78rem; font-weight: 700; color: var(--text-secondary);
    text-transform: uppercase; letter-spacing: .04em;
}
.custom-input {
    width: 100%; padding: 11px 14px;
    border: 1.5px solid var(--border-color);
    border-radius: 12px; font-size: .875rem;
    color: var(--text-primary); background: white;
    transition: all .15s; box-sizing: border-box;
}
.custom-input:focus {
    outline: none; border-color: #4f46e5;
    box-shadow: 0 0 0 3px rgba(99,102,241,.08);
}
.custom-textarea {
    width: 100%; padding: 12px 14px;
    border: 1.5px solid var(--border-color);
    border-radius: 12px; font-size: .875rem;
    color: var(--text-primary); background: white;
    transition: all .15s; box-sizing: border-box;
    font-family: monospace; line-height: 1.6;
}
.custom-textarea:focus {
    outline: none; border-color: #4f46e5;
    box-shadow: 0 0 0 3px rgba(99,102,241,.08);
}
.custom-form-text {
    font-size: .72rem; color: var(--text-muted); margin-top: 5px;
}

.form-actions {
    display: flex; justify-content: flex-end; gap: 12px;
    padding-top: 20px; border-top: 1px solid var(--border-color);
}
.btn-submit-template {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 11px 22px; border-radius: 12px; font-size: .875rem;
    font-weight: 700; background: linear-gradient(135deg, #4f46e5, #6366f1);
    color: white; border: none; cursor: pointer; transition: all .2s;
    text-decoration: none;
}
.btn-submit-template:hover { transform: translateY(-1px); box-shadow: 0 5px 18px rgba(99,102,241,.3); }

.btn-cancel-template {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 11px 22px; border-radius: 12px; font-size: .875rem;
    font-weight: 700; background: white; color: var(--text-secondary);
    border: 1.5px solid var(--border-color); cursor: pointer; transition: all .2s;
    text-decoration: none;
}
.btn-cancel-template:hover { border-color: #4f46e5; color: #4f46e5; }
</style>
@endpush

@section('content')
<div class="row justify-content-center">
    <div class="col-md-9">
        {{-- Breadcrumb back link --}}
        <div class="mb-4">
            <a href="{{ route('superadmin.email.templates.index') }}" class="text-decoration-none text-muted small" style="font-weight: 600;">
                <i class="fas fa-arrow-left me-1"></i> Back to Templates
            </a>
        </div>

        <div class="form-card">
            <div class="form-card-header">
                <div class="form-card-header-icon"><i class="fas fa-pen-to-square"></i></div>
                <div>
                    <div class="form-card-title">Edit Template: {{ $template->name }}</div>
                    <div class="form-card-desc">Modify subject line, tags, and inline HTML markup for this template.</div>
                </div>
            </div>
            <div class="form-card-body">
                <form action="{{ route('superadmin.email.templates.update', $template->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="custom-form-group">
                        <label class="custom-label">Internal Template Name</label>
                        <input type="text" name="name" class="custom-input" value="{{ $template->name }}" required>
                        <div class="custom-form-text">For administration and internal tracking only.</div>
                    </div>

                    <div class="custom-form-group">
                        <label class="custom-label">Email Subject Line</label>
                        <input type="text" name="subject" class="custom-input" value="{{ $template->subject }}" required>
                        <div class="custom-form-text">The subject line visible to recipients. Supports placeholders.</div>
                    </div>

                    <div class="custom-form-group">
                        <label class="custom-label">Supported Placeholders (Comma-separated)</label>
                        <input type="text" name="variables" class="custom-input" value="{{ implode(', ', $template->variables ?? []) }}">
                        <div class="custom-form-text">Comma-separated placeholders matching your HTML body variables.</div>
                    </div>

                    <div class="custom-form-group">
                        <label class="custom-label">HTML Email Content</label>
                        <textarea name="body" class="custom-textarea" rows="15" required>{{ $template->body }}</textarea>
                        <div class="custom-form-text">Write valid, responsive inline-styled HTML. Make sure variables match placeholders.</div>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('superadmin.email.templates.index') }}" class="btn-cancel-template">Cancel</a>
                        <button type="submit" class="btn-submit-template">
                            <i class="fas fa-floppy-disk"></i> Update Template
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
