@extends('layouts.shop')

@section('title', 'Vendor Policy - BuyNiger')

@section('content')
<div class="container policy-container">
    <div class="policy-header">
        <h1>Vendor Policy</h1>
        <p>Rules for Sellers on BuyNiger</p>
    </div>

    <div class="policy-content">
        <section>
            <h2>1. Vendor Registration and KYC</h2>
            <p>To sell on BuyNiger, vendors must register and undergo a "Know Your Customer" (KYC) verification process. Vendors must provide accurate business information and valid identification.</p>
        </section>

        <section>
            <h2>2. Product Quality and Authenticity</h2>
            <p>Vendors are strictly prohibited from listing counterfeit, pirated, or illegal goods. All products must be as described and of professional quality. BuyNiger reserves the right to remove any listing that violates these standards.</p>
        </section>

        <section>
            <h2>3. Order Fulfillment</h2>
            <p>Vendors must acknowledge and process orders within 24-48 hours. Consistent delays in shipping may lead to account suspension or termination.</p>
        </section>

        <section>
            <h2>4. Commissions and Fees</h2>
            <p>BuyNiger charges a standard commission on every successful sale. This commission is deducted automatically during payout processing. Current rates are available in the Vendor Dashboard settings.</p>
        </section>

        <section>
            <h2>5. Payouts</h2>
            <p>Payouts are processed after the customer confirms receipt of the item or after the 7-day return window has closed. Vendors must maintain valid bank details in their profile to receive funds.</p>
        </section>

        <section>
            <h2>6. Communication with Customers</h2>
            <p>Vendors should maintain professional communication with customers. Harassment or unprofessional behavior via the platform's messaging system is grounds for immediate ban.</p>
        </section>

        <section>
            <h2>7. Prohibited Items</h2>
            <p>The following items are strictly prohibited for sale: firearms, ammunition, illegal drugs, tobacco products, adult content, and any other items prohibited by Nigerian law.</p>
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
</style>
@endsection
