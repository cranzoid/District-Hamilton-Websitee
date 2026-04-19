@extends('layouts.app')

@section('title', 'Privacy Policy')

@section('content')
<section class="section-pad">
    <div class="container-ed--reading">
        <p class="eyebrow">Legal</p>
        <h1 class="font-display text-5xl md:text-6xl font-light mt-3 leading-tight">Privacy Policy</h1>
        <p class="lede mt-6">How we handle the information you share with us.</p>

        <div class="prose mt-12">
            <h2>1. Information we collect</h2>
            <p>We collect information you provide directly to us, including:</p>
            <ul>
                <li>Name and contact information</li>
                <li>Delivery address</li>
                <li>Order history and preferences</li>
                <li>Payment information (processed securely via our payment providers)</li>
            </ul>

            <h2>2. How we use your information</h2>
            <ul>
                <li>Process and deliver your orders</li>
                <li>Send order confirmations and updates</li>
                <li>Improve our services and customer experience</li>
                <li>Communicate about promotions and special offers (if you've opted in)</li>
            </ul>

            <h2>3. Information sharing</h2>
            <p>We do not sell or rent your personal information. We may share it only with:</p>
            <ul>
                <li>Delivery partners to fulfill your orders</li>
                <li>Payment processors to complete transactions</li>
                <li>Service providers who assist our operations</li>
            </ul>

            <h2>4. Data security</h2>
            <p>We implement appropriate security measures to protect your personal information from unauthorized access, alteration, or disclosure.</p>

            <h2>5. Cookies and tracking</h2>
            <p>We use cookies and similar technologies to enhance your experience and analyze usage patterns.</p>

            <h2>6. Your rights</h2>
            <ul>
                <li>Access your personal information</li>
                <li>Correct inaccurate information</li>
                <li>Request deletion of your information</li>
                <li>Opt-out of marketing communications</li>
            </ul>

            <h2>7. Contact</h2>
            <p>If you have any questions about our Privacy Policy, please <a href="{{ route('contact') }}">contact us</a>.</p>

            <p class="text-sm text-ink-muted mt-10">Last updated: {{ date('F j, Y') }}</p>
        </div>
    </div>
</section>
@endsection
