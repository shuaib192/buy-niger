{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    Written by Shuaibu Abdulmumin (08122598372, 07049906420)
    
    View: Contact Us
--}}
@extends('layouts.shop')

@section('title', 'Contact Us')

@section('content')
<div class="container" style="padding-top:32px; padding-bottom:80px;">

    <!-- Hero -->
    <div class="contact-hero">
        <span class="contact-badge"><i class="fas fa-envelope"></i> Get in Touch</span>
        <h1>We'd Love to<br>Hear From You</h1>
        <p>Have questions, feedback, or need support? Reach out and our team will respond within 24 hours.</p>
    </div>

    <!-- Success Message -->
    @if(session('success'))
    <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:14px; padding:16px 24px; margin-bottom:32px; display:flex; align-items:center; gap:12px;">
        <i class="fas fa-check-circle" style="color:#10b981; font-size:20px;"></i>
        <span style="color:#166534; font-weight:600;">{{ session('success') }}</span>
    </div>
    @endif

    <div class="contact-grid">
        <!-- Contact Form -->
        <div class="contact-form-card">
            <h2>Send Us a Message</h2>
            <form action="{{ route('contact.send') }}" method="POST">
                @csrf
                <div class="contact-form-row">
                    <div class="form-group">
                        <label>Your Name <span style="color:#ef4444;">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="John Doe" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label>Email Address <span style="color:#ef4444;">*</span></label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="you@example.com" required>
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="form-group">
                    <label>Subject <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="subject" class="form-control @error('subject') is-invalid @enderror" value="{{ old('subject') }}" placeholder="How can we help?" required>
                    @error('subject') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label>Message <span style="color:#ef4444;">*</span></label>
                    <textarea name="message" class="form-control @error('message') is-invalid @enderror" rows="5" placeholder="Write your message here..." required>{{ old('message') }}</textarea>
                    @error('message') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <button type="submit" class="btn btn-primary btn-lg" style="width:100%;">
                    <i class="fas fa-paper-plane"></i> Send Message
                </button>
            </form>
        </div>

        <!-- Contact Info -->
        <div class="contact-info-side">
            <div class="contact-info-card">
                <div class="contact-info-icon" style="background:linear-gradient(135deg,#3b82f6,#1d4ed8);">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <h4>Our Location</h4>
                <p>Niamey, Niger Republic<br>P3 Consulting Limited</p>
            </div>
            <div class="contact-info-card">
                <div class="contact-info-icon" style="background:linear-gradient(135deg,#10b981,#059669);">
                    <i class="fas fa-phone-alt"></i>
                </div>
                <h4>Call Us</h4>
                <p><a href="tel:+2348122598372">+234 812 259 8372</a><br>
                   <a href="tel:+2347049906420">+234 704 990 6420</a></p>
            </div>
            <div class="contact-info-card">
                <div class="contact-info-icon" style="background:linear-gradient(135deg,#f59e0b,#d97706);">
                    <i class="fas fa-envelope"></i>
                </div>
                <h4>Email Us</h4>
                <p><a href="mailto:shuaibabdul192@gmail.com">shuaibabdul192@gmail.com</a></p>
            </div>
            <div class="contact-info-card">
                <div class="contact-info-icon" style="background:linear-gradient(135deg,#8b5cf6,#7c3aed);">
                    <i class="fas fa-clock"></i>
                </div>
                <h4>Business Hours</h4>
                <p>Mon – Sat: 8AM – 8PM<br>Sunday: 10AM – 4PM</p>
            </div>
        </div>
    </div>
</div>

<style>
    .contact-hero {
        text-align: center;
        padding: 56px 20px;
        background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 50%, #1e40af 100%);
        border-radius: 28px;
        color: white;
        margin-bottom: 48px;
    }
    .contact-badge {
        display: inline-flex; align-items: center; gap: 8px;
        background: rgba(59,130,246,0.2); border: 1px solid rgba(59,130,246,0.3);
        padding: 8px 18px; border-radius: 50px; font-size: 13px; font-weight: 600;
        margin-bottom: 20px; color: #93c5fd;
    }
    .contact-badge i { color: #fbbf24; }
    .contact-hero h1 {
        font-size: clamp(2rem, 5vw, 2.8rem); font-weight: 800; margin-bottom: 14px;
        line-height: 1.1; letter-spacing: -0.025em;
    }
    .contact-hero p {
        font-size: 1.05rem; opacity: 0.8; max-width: 540px; margin: 0 auto; line-height: 1.7;
    }

    .contact-grid {
        display: grid;
        grid-template-columns: 1.5fr 1fr;
        gap: 32px;
        align-items: start;
    }

    .contact-form-card {
        background: white; border-radius: 20px; padding: 36px;
        border: 1px solid #e2e8f0; box-shadow: 0 4px 20px rgba(0,0,0,0.04);
    }
    .contact-form-card h2 {
        font-size: 20px; font-weight: 700; color: #1e293b; margin-bottom: 24px;
    }
    .contact-form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .contact-form-card .form-group { margin-bottom: 18px; }
    .contact-form-card label {
        display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px;
    }
    .contact-form-card .form-control {
        width: 100%; padding: 12px 16px; border: 1px solid #e2e8f0; border-radius: 10px;
        font-size: 14px; transition: border-color 0.2s, box-shadow 0.2s;
        font-family: inherit;
    }
    .contact-form-card .form-control:focus {
        outline: none; border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.1);
    }
    .contact-form-card textarea.form-control { resize: vertical; min-height: 120px; }

    .contact-info-side { display: flex; flex-direction: column; gap: 16px; }
    .contact-info-card {
        background: white; border-radius: 16px; padding: 24px;
        border: 1px solid #e2e8f0; display: flex; align-items: flex-start; gap: 16px;
        transition: transform 0.3s, box-shadow 0.3s;
    }
    .contact-info-card:hover { transform: translateY(-4px); box-shadow: 0 12px 32px rgba(0,0,0,0.06); }
    .contact-info-icon {
        width: 44px; height: 44px; border-radius: 12px; display: flex;
        align-items: center; justify-content: center; color: white; font-size: 16px;
        flex-shrink: 0;
    }
    .contact-info-card h4 { font-size: 15px; font-weight: 700; color: #1e293b; margin: 0 0 4px; }
    .contact-info-card p { font-size: 13px; color: #64748b; line-height: 1.6; margin: 0; }
    .contact-info-card a { color: #3b82f6; text-decoration: none; }
    .contact-info-card a:hover { text-decoration: underline; }

    .invalid-feedback { color: #ef4444; font-size: 12px; margin-top: 4px; }

    @media (max-width: 768px) {
        .contact-grid { grid-template-columns: 1fr; }
        .contact-form-row { grid-template-columns: 1fr; }
        .contact-form-card { padding: 24px; }
    }
</style>
@endsection
