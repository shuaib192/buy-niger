@extends('layouts.shop')

@section('title', 'Privacy Policy - BuyNiger')

@section('content')
<div class="container policy-container">
    <div class="policy-header">
        <h1>Privacy Policy</h1>
        <p>Last Updated: February 19, 2026</p>
    </div>

    <div class="policy-content">
        <section>
            <h2>1. Introduction</h2>
            <p>Welcome to BuyNiger. We respect your privacy and are committed to protecting your personal data. This privacy policy will inform you as to how we look after your personal data when you visit our website (regardless of where you visit it from) and tell you about your privacy rights and how the law protects you.</p>
        </section>

        <section>
            <h2>2. The Data We Collect About You</h2>
            <p>Personal data, or personal information, means any information about an individual from which that person can be identified. We may collect, use, store and transfer different kinds of personal data about you which we have grouped together as follows:</p>
            <ul>
                <li><strong>Identity Data:</strong> includes first name, last name, username or similar identifier.</li>
                <li><strong>Contact Data:</strong> includes billing address, delivery address, email address and telephone numbers.</li>
                <li><strong>Financial Data:</strong> includes payment card details (processed securely via Paystack).</li>
                <li><strong>Transaction Data:</strong> includes details about payments to and from you and other details of products and services you have purchased from us.</li>
                <li><strong>Technical Data:</strong> includes internet protocol (IP) address, your login data, browser type and version, time zone setting and location.</li>
            </ul>
        </section>

        <section>
            <h2>3. How We Use Your Personal Data</h2>
            <p>We will only use your personal data when the law allows us to. Most commonly, we will use your personal data in the following circumstances:</p>
            <ul>
                <li>To register you as a new customer or vendor.</li>
                <li>To process and deliver your order including managing payments, fees and charges.</li>
                <li>To manage our relationship with you.</li>
                <li>To enable you to partake in a prize draw, competition or complete a survey.</li>
                <li>To improve our website, products/services, marketing and customer relationships.</li>
            </ul>
        </section>

        <section>
            <h2>4. Data Security</h2>
            <p>We have put in place appropriate security measures to prevent your personal data from being accidentally lost, used or accessed in an unauthorized way, altered or disclosed. In addition, we limit access to your personal data to those employees, agents, contractors and other third parties who have a business need to know.</p>
        </section>

        <section>
            <h2>5. Your Legal Rights</h2>
            <p>Under certain circumstances, you have rights under data protection laws in relation to your personal data, including the right to request access, correction, erasure, restriction, transfer, to object to processing, and the right to withdraw consent.</p>
        </section>

        <section>
            <h2>6. Contact Us</h2>
            <p>If you have any questions about this privacy policy or our privacy practices, please contact our privacy team at <a href="mailto:privacy@buyniger.com">privacy@buyniger.com</a>.</p>
        </section>
    </div>
</div>

{{-- Policy CSS in shop.css --}}
@endsection
