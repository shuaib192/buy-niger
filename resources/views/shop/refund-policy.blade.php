@extends('layouts.shop')

@section('title', 'Return and Refund Policy - BuyNiger')

@section('content')
<div class="container policy-container">
    <div class="policy-header">
        <h1>Return and Refund Policy</h1>
        <p>Our commitment to your satisfaction</p>
    </div>

    <div class="policy-content">
        <section>
            <h2>1. Return Eligibility</h2>
            <p>You may request a return within 7 days of receiving your item. To be eligible for a return, your item must be:</p>
            <ul>
                <li>In the same condition that you received it.</li>
                <li>Unworn, unused, or unwashed.</li>
                <li>In its original packaging with all tags attached.</li>
            </ul>
        </section>

        <section>
            <h2>2. Non-Returnable Items</h2>
            <p>Certain types of items cannot be returned, including:</p>
            <ul>
                <li>Perishable goods (such as food, flowers, or plants).</li>
                <li>Custom products (such as special orders or personalized items).</li>
                <li>Personal care goods (such as beauty products).</li>
                <li>Items on final sale or digital products.</li>
            </ul>
        </section>

        <section>
            <h2>3. Return Process</h2>
            <p>To start a return, you can contact the vendor directly via the platform's messaging system or initiate a return request from your Order History dashboard. If your return is accepted, we will send you instructions on how and where to send your package.</p>
        </section>

        <section>
            <h2>4. Refunds</h2>
            <p>Once we receive and inspect your return, we will notify you of the approval or rejection of your refund. If approved, you’ll be automatically refunded on your original payment method within 10 business days.</p>
        </section>

        <section>
            <h2>5. Damaged or Wrong Items</h2>
            <p>Please inspect your order upon reception and contact us immediately if the item is defective, damaged or if you receive the wrong item, so that we can evaluate the issue and make it right.</p>
        </section>

        <section>
            <h2>6. Exchanges</h2>
            <p>The fastest way to ensure you get what you want is to return the item you have, and once the return is accepted, make a separate purchase for the new item.</p>
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
