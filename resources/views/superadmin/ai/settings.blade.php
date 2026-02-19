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
    <!-- Status Alert -->
    @if(session('success'))
        <div class="col-12">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    <!-- Kill Switch -->
    <div class="col-12">
        <div class="dashboard-card {{ $killSwitch ? 'border-success' : 'border-danger' }}" style="border-width:2px;">
            <div class="dashboard-card-body d-flex justify-content-between align-items-center py-3">
                <div>
                    <h5 class="{{ $killSwitch ? 'text-success' : 'text-danger' }} mb-1">
                        <i class="fas fa-power-off"></i> AI System Status: {{ $killSwitch ? 'DISABLED' : 'ACTIVE' }}
                    </h5>
                    <p class="mb-0 text-muted small">Emergency kill switch to disable all AI operations instantly.</p>
                </div>
                <form action="{{ route($prefix.'ai.killswitch') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-{{ $killSwitch ? 'success' : 'danger' }}">
                        {{ $killSwitch ? 'Enable AI' : 'Disable AI' }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Provider Settings -->
    <div class="col-lg-8">
        <div class="dashboard-card">
            <div class="dashboard-card-header d-flex justify-content-between align-items-center">
                <h3 class="mb-0">AI Providers</h3>
                <span class="badge bg-info">Groq Recommended</span>
            </div>
            <div class="dashboard-card-body">
                <form action="{{ route($prefix.'ai.settings.update') }}" method="POST">
                    @csrf
                    
                    <!-- GROQ -->
                    <div class="provider-card p-4 mb-4 rounded-3" style="background:linear-gradient(135deg,#f0fdf4,#dcfce7);border:2px solid #22c55e;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h5 class="mb-1"><i class="fas fa-bolt text-warning"></i> Groq (Llama 3.3)</h5>
                                <span class="badge bg-success">Recommended - Fast & Free</span>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="settings[ai_groq_active]" value="1" id="groqActive" style="width:50px;height:25px;" {{ $groq?->is_active ? 'checked' : '' }}>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">API Key</label>
                            <input type="text" name="settings[ai_groq_key]" class="form-control form-control-lg" placeholder="gsk_xxxxxxxxxxxxx" value="{{ $groqMasked }}">
                            <div class="form-text">Get free API key: <a href="https://console.groq.com/keys" target="_blank">console.groq.com/keys</a></div>
                        </div>
                        <div>
                            <label class="form-label fw-bold">Model</label>
                            <select name="settings[ai_groq_model]" class="form-select">
                                <option value="llama-3.3-70b-versatile" {{ $groq?->model == 'llama-3.3-70b-versatile' ? 'selected' : '' }}>Llama 3.3 70B (Versatile)</option>
                                <option value="llama-3.1-8b-instant" {{ $groq?->model == 'llama-3.1-8b-instant' ? 'selected' : '' }}>Llama 3.1 8B (Fast)</option>
                                <option value="mixtral-8x7b-32768" {{ $groq?->model == 'mixtral-8x7b-32768' ? 'selected' : '' }}>Mixtral 8x7B</option>
                            </select>
                        </div>
                    </div>

                    <!-- GEMINI -->
                    <div class="provider-card p-4 mb-4 rounded-3 bg-light">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0"><i class="fab fa-google text-primary"></i> Google Gemini</h5>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="settings[ai_gemini_active]" value="1" style="width:50px;height:25px;" {{ $gemini?->is_active ? 'checked' : '' }}>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">API Key</label>
                            <input type="text" name="settings[ai_gemini_key]" class="form-control" placeholder="AIzaSy..." value="{{ $geminiMasked }}">
                        </div>
                        <div>
                            <label class="form-label">Model</label>
                            <select name="settings[ai_gemini_model]" class="form-select">
                                <option value="gemini-pro">Gemini Pro</option>
                                <option value="gemini-1.5-flash">Gemini 1.5 Flash</option>
                            </select>
                        </div>
                    </div>

                    <!-- OPENAI -->
                    <div class="provider-card p-4 mb-4 rounded-3 bg-light">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0"><i class="fas fa-robot text-success"></i> OpenAI GPT-4</h5>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="settings[ai_openai_active]" value="1" style="width:50px;height:25px;" {{ $openai?->is_active ? 'checked' : '' }}>
                            </div>
                        </div>
                        <div>
                            <label class="form-label">API Key</label>
                            <input type="text" name="settings[ai_openai_key]" class="form-control" placeholder="sk-..." value="{{ $openaiMasked }}">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100">
                        <i class="fas fa-save"></i> Save AI Settings
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <div class="dashboard-card mb-4">
            <div class="dashboard-card-header">
                <h5 class="mb-0">Provider Status</h5>
            </div>
            <div class="dashboard-card-body">
                <div class="d-flex flex-column gap-2">
                    @if($groq?->is_active && $groq?->credentials)
                        <div class="d-flex align-items-center gap-2 text-success">
                            <i class="fas fa-check-circle"></i> <strong>Groq</strong> - Active
                        </div>
                    @else
                        <div class="d-flex align-items-center gap-2 text-muted">
                            <i class="fas fa-times-circle"></i> Groq - Not configured
                        </div>
                    @endif
                    
                    @if($gemini?->is_active && $gemini?->credentials)
                        <div class="d-flex align-items-center gap-2 text-success">
                            <i class="fas fa-check-circle"></i> <strong>Gemini</strong> - Active
                        </div>
                    @else
                        <div class="d-flex align-items-center gap-2 text-muted">
                            <i class="fas fa-times-circle"></i> Gemini - Not configured
                        </div>
                    @endif
                    
                    @if($openai?->is_active && $openai?->credentials)
                        <div class="d-flex align-items-center gap-2 text-success">
                            <i class="fas fa-check-circle"></i> <strong>OpenAI</strong> - Active
                        </div>
                    @else
                        <div class="d-flex align-items-center gap-2 text-muted">
                            <i class="fas fa-times-circle"></i> OpenAI - Not configured
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h5 class="mb-0">Quick Test</h5>
            </div>
            <div class="dashboard-card-body">
                <p class="text-muted small">Test your AI by clicking the chat button in the bottom-right corner.</p>
                <ol class="small ps-3 mb-0">
                    <li>Enter your Groq API key above</li>
                    <li>Make sure toggle is ON</li>
                    <li>Click Save</li>
                    <li>Click the 💬 chat button</li>
                    <li>Say "hello"</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection
