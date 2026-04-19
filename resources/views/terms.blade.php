@extends('layouts.app')

@section('title', 'Terms & Conditions')

@section('content')
<section class="section-pad">
    <div class="container-ed--reading">
        <p class="eyebrow">Legal</p>
        <h1 class="font-display text-5xl md:text-6xl font-light mt-3 leading-tight">Terms & Conditions</h1>
        <p class="lede mt-6">By using our website and services, you agree to the terms below.</p>

        <div class="prose mt-12">
            <h2>1. Acceptance of terms</h2>
            <p>By accessing and using District Hamilton's website and services, you accept and agree to be bound by these Terms and Conditions. If you do not agree to these terms, please do not use our website or services.</p>

            <h2>2. Use of website</h2>
            <p>This website is provided for your personal and non-commercial use. You agree not to misuse the website or help anyone else do so.</p>

            <h2>3. Online ordering</h2>
            <p>All orders placed through our website are subject to availability and acceptance. We reserve the right to refuse service to anyone for any reason at any time.</p>

            <h2>4. Pricing and payment</h2>
            <p>All prices listed on the website are in Canadian dollars and are subject to change without notice. Payment is required at the time of ordering.</p>

            <h2>5. Delivery and pickup</h2>
            <p>Delivery times are estimates only. We are not responsible for delays beyond our control. Pickup orders should be collected at the specified time.</p>

            <h2>6. Modifications to service</h2>
            <p>We reserve the right to modify or discontinue any aspect of our service at any time without notice.</p>

            <h2>7. Disclaimer of warranties</h2>
            <p>Our services are provided "as is" without any warranties, expressed or implied.</p>

            <h2>8. Contact</h2>
            <p>For any questions regarding these terms, please <a href="{{ route('contact') }}">contact us</a>.</p>

            <p class="text-sm text-ink-muted mt-10">Last updated: {{ date('F j, Y') }}</p>
        </div>
    </div>
</section>
@endsection
