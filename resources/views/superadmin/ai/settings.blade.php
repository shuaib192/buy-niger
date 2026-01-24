@extends('layouts.app')

@section('title', 'AI Configuration')
@section('page_title', 'AI Configuration')

@section('sidebar')
    @include('superadmin.partials.sidebar')
@endsection

@php
    $prefix = request()->is('admin*') ? 'admin.' : 'superadmin.';
@endphp

@section('content')
<div class="row g-4">
    <!-- Kill Switch -->
    <div class="col-12">
        <div class="dashboard-card border-danger">
            <div class="dashboard-card-body d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="text-danger mb-1"><i class="fas fa-biohazard"></i> Emergency Kill Switch</h4>
                    <p class="mb-0 text-muted">Instantly disable all AI operations across the platform.</p>
                </div>
                <form action="{{ route($prefix.'ai.killswitch') }}" method="POST">
                    @csrf
                    @php
                        $killSwitch = \App\Models\AIEmergencyStatus::first()->kill_switch_enabled ?? false;
                    @endphp
                    <button type="submit" class="btn btn-{{ $killSwitch ? 'success' : 'danger' }} btn-lg">
                        {{ $killSwitch ? 'RESTORE AI SYSTEMS' : 'DISABLE ALL AI' }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Provider Settings -->
    <div class="col-8">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3>AI Providers</h3>
            </div>
            <div class="dashboard-card-body">
                <form action="{{ route($prefix.'settings.update') }}" method="POST">
                    @csrf
                    <div class="mb-4 border-bottom pb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0"><i class="fab fa-google text-danger"></i> Gemini (Google)</h5>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" checked>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">API Key</label>
                            <input type="password" name="settings[ai_gemini_key]" class="form-control" value="AIzaSy...">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Model</label>
                            <select name="settings[ai_gemini_model]" class="form-select">
                                <option value="gemini-pro">Gemini Pro</option>
                                <option value="gemini-ultra">Gemini Ultra</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0"><i class="fas fa-robot text-success"></i> OpenAI (GPT-4)</h5>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="settings[ai_openai_active]">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">API Key</label>
                            <input type="password" name="settings[ai_openai_key]" class="form-control" placeholder="sk-...">
                        </div>
                    </div>

                    <div class="mb-4 border-top pt-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0"><i class="fas fa-bolt text-warning"></i> Groq (Llama 3.3)</h5>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="settings[ai_groq_active]" checked>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">API Key</label>
                            <input type="password" name="settings[ai_groq_key]" class="form-control" placeholder="gsk_..." value="{{ env('GROQ_API_KEY') ? '••••••••••' : '' }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Model</label>
                            <select name="settings[ai_groq_model]" class="form-select">
                                <option value="llama-3.3-70b-versatile">Llama 3.3 70B (Versatile)</option>
                                <option value="llama-3.1-8b-instant">Llama 3.1 8B (Fast)</option>
                                <option value="mixtral-8x7b-32768">Mixtral 8x7B</option>
                            </select>
                        </div>
                        <div class="alert alert-success py-2">
                            <i class="fas fa-check-circle"></i> <strong>Recommended:</strong> Groq provides fast, affordable AI responses.
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Save AI Settings</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Usage Stats -->
    <div class="col-4">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3>Usage & Cost</h3>
            </div>
            <div class="dashboard-card-body">
                <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                    <span>Total Calls (This Month)</span>
                    <strong>1,245</strong>
                </div>
                <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                    <span>Estimated Cost</span>
                    <strong>$4.50</strong>
                </div>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Costs are estimates based on token usage.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
