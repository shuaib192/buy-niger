@extends('layouts.shop')

@section('title', 'Terms and Conditions - BuyNiger')

@section('content')
<div class="container policy-container">
    <div class="policy-header">
        <h1>Terms and Conditions</h1>
        <p>Last Updated: February 19, 2026</p>
    </div>

    <div class="policy-content">
        <section>
            <h2>1. Agreement to Terms</h2>
            <p>By accessing or using BuyNiger, you agree to be bound by these Terms and Conditions. If you disagree with any part of these terms, you may not access the service.</p>
        </section>

        <section>
            <h2>2. Use of the Marketplace</h2>
            <p>BuyNiger is a multi-vendor marketplace. We provide a platform for vendors to list products and customers to purchase them. We are not a party to the transactions unless explicitly stated.</p>
            <ul>
                <li>You must be at least 18 years old to use this platform.</li>
                <li>You are responsible for maintaining the confidentiality of your account.</li>
                <li>You agree not to use the platform for any illegal or unauthorized purpose.</li>
            </ul>
        </section>

        <section>
            <h2>3. Vendor Responsibilities</h2>
            <p>Vendors must provide accurate information about their products, maintain stock levels, and fulfill orders in a timely manner. Vendors agree to abide by the BuyNiger Vendor Policy.</p>
        </section>

        <section>
            <h2>4. Payments and Refunds</h2>
            <p>All payments are processed securely. Refunds are subject to our Refund Policy. BuyNiger reserves the right to hold funds in case of disputes until resolution.</p>
        </section>

        <section>
            <h2>5. Intellectual Property</h2>
            <p>The service and its original content, features, and functionality are and will remain the exclusive property of BuyNiger and its licensors.</p>
        </section>

        <section>
            <h2>6. Limitation of Liability</h2>
            <p>In no event shall BuyNiger, nor its directors, employees, partners, agents, suppliers, or affiliates, be liable for any indirect, incidental, special, consequential or punitive damages resulting from your use of the service.</p>
        </section>

        <section>
            <h2>7. Governing Law</h2>
            <p>These Terms shall be governed and construed in accordance with the laws of the Federal Republic of Nigeria, without regard to its conflict of law provisions.</p>
        </section>
    </div>
</div>

<style>
    .policy-container {
        max-width: 900px;
        margin: 0 auto;
        padding: 60px 20px;
    }
    .policy-header {
        text-align: center;
        margin-bottom: 50px;
    }
    .policy-header h1 {
        font-size: 2.5rem;
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 10px;
    }
    .policy-header p {
        color: #64748b;
        font-size: 1rem;
    }
    .policy-content {
        background: white;
        padding: 40px;
        border-radius: 24px;
        border: 1px solid #e2e8f0;
        line-height: 1.8;
        color: #334155;
    }
    .policy-content section {
        margin-bottom: 40px;
    }
    .policy-content h2 {
        font-size: 1.5rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 20px;
        border-bottom: 2px solid #f1f5f9;
        padding-bottom: 10px;
    }
    .policy-content p {
        margin-bottom: 15px;
    }
    .policy-content ul {
        margin-bottom: 15px;
        padding-left: 20px;
    }
    .policy-content ul li {
        margin-bottom: 10px;
    }
</style>
@endsection
