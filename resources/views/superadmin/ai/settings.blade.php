{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    Written by Shuaibu Abdulmumin
    View: Super Admin - AI Provider Configuration — Premium v2.0
--}}
@extends('layouts.app')

@section('title', 'AI Configuration')
@section('page_title', 'AI Configuration')

@section('sidebar')
    @include('superadmin.partials.sidebar')
@endsection

@php
    $prefix = request()->is('admin*') ? 'admin.' : 'superadmin.';
    
    // Load providers safely
    $groq = null;
    $gemini = null;
    $openai = null;
    $killSwitch = false;
    
    try {
        $groq = \App\Models\AIProvider::where('name', 'groq')->first();
        $gemini = \App\Models\AIProvider::where('name', 'gemini')->first();
        $openai = \App\Models\AIProvider::where('name', 'openai')->first();
        $killSwitch = \App\Models\AIEmergencyStatus::first()?->kill_switch_enabled ?? false;
    } catch (\Exception $e) {}
    
    // Get masked API key
    $groqKey = $groq?->credentials['api_key'] ?? '';
    $groqMasked = $groqKey ? 'gsk_' . str_repeat('x', 12) : '';
    
    $geminiKey = $gemini?->credentials['api_key'] ?? '';
    $geminiMasked = $geminiKey ? 'AIza' . str_repeat('x', 8) : '';
    
    $openaiKey = $openai?->credentials['api_key'] ?? '';
    $openaiMasked = $openaiKey ? 'sk-' . str_repeat('x', 8) : '';
@endphp

@section('content')
<div class="row g-4">
    <!-- Kill Switch -->
    <div class="col-12">
        <div class="dashboard-card" style="border-left: 5px solid {{ $killSwitch ? '#10b981' : '#f43f5e' }};">
            <div class="dashboard-card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center py-4 gap-3">
                <div>
                    <h4 class="{{ $killSwitch ? 'text-success' : 'text-danger' }} fw-bold mb-1">
                        <i class="fas fa-power-off me-2"></i> AI System Status: {{ $killSwitch ? 'DISABLED' : 'ACTIVE' }}
                    </h4>
                    <p class="mb-0 text-muted small">Emergency kill switch to halt all autonomous AI API calls and database integrations immediately.</p>
                </div>
                <form action="{{ route($prefix.'ai.killswitch') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn {{ $killSwitch ? 'btn-success' : 'btn-danger' }} rounded-pill px-4">
                        <i class="fas {{ $killSwitch ? 'fa-play' : 'fa-circle-stop' }} me-2"></i>
                        {{ $killSwitch ? 'Re-Enable AI Services' : 'Emergency Shutdown' }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Provider Settings -->
    <div class="col-lg-8">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <div>
                    <h3 class="mb-1">AI API Providers</h3>
                    <p class="text-muted small mb-0">Select model providers and input credential keys to power the intelligence layer.</p>
                </div>
                <span class="badge badge-success"><i class="fas fa-bolt me-1"></i> Groq Recommended</span>
            </div>
            <div class="dashboard-card-body">
                <form action="{{ route($prefix.'ai.settings.update') }}" method="POST">
                    @csrf
                    
                    <!-- GROQ -->
                    <div class="p-4 mb-4 rounded-3 border" style="background: rgba(16, 185, 129, 0.02); border-color: rgba(16, 185, 129, 0.2) !important;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h5 class="mb-1 fw-bold text-dark"><i class="fas fa-bolt text-warning me-1"></i> Groq (Llama 3.3)</h5>
                                <span class="badge badge-success">High Speed & Cost Efficient</span>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="settings[ai_groq_active]" value="1" id="groqActive" style="width:48px;height:24px;cursor:pointer;" {{ $groq?->is_active ? 'checked' : '' }}>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-dark fw-semibold small">API Secret Key</label>
                            <input type="text" name="settings[ai_groq_key]" class="form-control form-control-lg" placeholder="gsk_xxxxxxxxxxxxx" value="{{ $groqMasked }}">
                            <div class="form-text small text-muted">Generate a key: <a href="https://console.groq.com/keys" target="_blank" class="text-indigo text-decoration-none">console.groq.com/keys</a></div>
                        </div>
                        <div>
                            <label class="form-label text-dark fw-semibold small">Default Active Model</label>
                            <select name="settings[ai_groq_model]" class="form-select">
                                <option value="llama-3.3-70b-versatile" {{ $groq?->model == 'llama-3.3-70b-versatile' ? 'selected' : '' }}>Llama 3.3 70B (Versatile)</option>
                                <option value="llama-3.1-8b-instant" {{ $groq?->model == 'llama-3.1-8b-instant' ? 'selected' : '' }}>Llama 3.1 8B (Instant)</option>
                                <option value="mixtral-8x7b-32768" {{ $groq?->model == 'mixtral-8x7b-32768' ? 'selected' : '' }}>Mixtral 8x7B</option>
                            </select>
                        </div>
                    </div>

                    <!-- GEMINI -->
                    <div class="p-4 mb-4 rounded-3 border bg-light" style="border-color: var(--border-color) !important;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0 fw-bold text-dark"><i class="fab fa-google text-primary me-2"></i> Google Gemini</h5>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="settings[ai_gemini_active]" value="1" style="width:48px;height:24px;cursor:pointer;" {{ $gemini?->is_active ? 'checked' : '' }}>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-dark fw-semibold small">API Key</label>
                            <input type="text" name="settings[ai_gemini_key]" class="form-control" placeholder="AIzaSy..." value="{{ $geminiMasked }}">
                        </div>
                        <div>
                            <label class="form-label text-dark fw-semibold small">Gemini Model Choice</label>
                            <select name="settings[ai_gemini_model]" class="form-select">
                                <option value="gemini-pro" {{ $gemini?->model == 'gemini-pro' ? 'selected' : '' }}>Gemini Pro</option>
                                <option value="gemini-1.5-flash" {{ $gemini?->model == 'gemini-1.5-flash' ? 'selected' : '' }}>Gemini 1.5 Flash</option>
                            </select>
                        </div>
                    </div>

                    <!-- OPENAI -->
                    <div class="p-4 mb-4 rounded-3 border bg-light" style="border-color: var(--border-color) !important;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-brain text-success me-2"></i> OpenAI GPT-4</h5>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="settings[ai_openai_active]" value="1" style="width:48px;height:24px;cursor:pointer;" {{ $openai?->is_active ? 'checked' : '' }}>
                            </div>
                        </div>
                        <div>
                            <label class="form-label text-dark fw-semibold small">Secret Key</label>
                            <input type="text" name="settings[ai_openai_key]" class="form-control" placeholder="sk-..." value="{{ $openaiMasked }}">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill mt-3">
                        <i class="fas fa-floppy-disk me-2"></i> Commit AI Provider Settings
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Telemetry Status Sidebar -->
    <div class="col-lg-4">
        <div class="dashboard-card mb-4">
            <div class="dashboard-card-header">
                <h3>Connection Status</h3>
            </div>
            <div class="dashboard-card-body">
                <div class="d-flex flex-column gap-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fas fa-bolt text-warning" style="width: 16px;"></i>
                            <span class="text-dark fw-semibold small">Groq Cloud</span>
                        </div>
                        @if($groq?->is_active && $groq?->credentials)
                            <span class="badge badge-success"><i class="fas fa-link me-1"></i> Active</span>
                        @else
                            <span class="badge badge-secondary">Inactive</span>
                        @endif
                    </div>
                    
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fab fa-google text-primary" style="width: 16px;"></i>
                            <span class="text-dark fw-semibold small">Google Vertex AI</span>
                        </div>
                        @if($gemini?->is_active && $gemini?->credentials)
                            <span class="badge badge-success"><i class="fas fa-link me-1"></i> Active</span>
                        @else
                            <span class="badge badge-secondary">Inactive</span>
                        @endif
                    </div>
                    
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fas fa-brain text-success" style="width: 16px;"></i>
                            <span class="text-dark fw-semibold small">OpenAI Engine</span>
                        </div>
                        @if($openai?->is_active && $openai?->credentials)
                            <span class="badge badge-success"><i class="fas fa-link me-1"></i> Active</span>
                        @else
                            <span class="badge badge-secondary">Inactive</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3>Oversight Verification</h3>
            </div>
            <div class="dashboard-card-body">
                <p class="text-muted small mb-3">To verify connectivity to the active LLM provider:</p>
                <ol class="small text-muted ps-3 mb-0" style="line-height: 1.6;">
                    <li>Ensure Groq or Gemini switches are turned ON.</li>
                    <li>Add the respective API key and hit Save.</li>
                    <li>Open the chatbot widget in the bottom right corner of the screen.</li>
                    <li>Send a test prompt (e.g. "Ping") to check the response velocity.</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection
