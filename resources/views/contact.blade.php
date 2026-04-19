@extends('layouts.app')

@section('title', 'Contact')
@section('description', 'Get in touch with The District Tapas + Bar in Hamilton — reservations, questions, feedback.')

@php
    $faqs = [
        ['q' => 'Do you take reservations?', 'a' => 'Yes — for parties of all sizes. For groups larger than eight, please email us at thedistricthamilton@gmail.com to coordinate.'],
        ['q' => 'Is there parking?', 'a' => 'Limited street parking is available out front, with several public lots nearby within short walking distance.'],
        ['q' => 'Do you have vegetarian options?', 'a' => 'Always. The menu is clearly marked with dietary info and our staff can steer you toward vegetarian or gluten-conscious dishes.'],
        ['q' => 'Can you host private events?', 'a' => 'Yes — see our Events page or get in touch. We handle birthdays, corporate gatherings, and semi-private buyouts.'],
    ];
    $faqSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => array_map(fn($f) => [
            '@type' => 'Question',
            'name' => $f['q'],
            'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f['a']],
        ], $faqs),
    ];
@endphp

@section('content')
    <section class="subhero">
        <div class="container-ed">
            <p class="eyebrow">Contact</p>
            <h1 class="font-display text-6xl md:text-8xl font-light leading-[1.02] mt-3">
                Say <em class="italic text-brand">hello</em>.
            </h1>
            <p class="lede mt-8 max-w-2xl">
                Reservations, private events, a question about the menu — we'd love to hear from you. The fastest way for table bookings is our reservation widget; for everything else, drop us a note.
            </p>
        </div>
    </section>

    <section class="section-pad">
        <div class="container-ed grid lg:grid-cols-[1fr_1.2fr] gap-12 md:gap-16">
            {{-- INFO --}}
            <div class="space-y-10">
                <div>
                    <p class="eyebrow">Find</p>
                    <h2 class="font-display text-3xl mt-2">The room</h2>
                    <p class="mt-3 text-ink-muted leading-relaxed">
                        61 Barton Street East<br>
                        Hamilton, ON L8L 2V7
                    </p>
                </div>
                <div>
                    <p class="eyebrow">Call</p>
                    <p class="mt-2 numeral text-2xl"><a href="tel:+19055222580" class="hover:text-brand">(905) 522-2580</a></p>
                </div>
                <div>
                    <p class="eyebrow">Write</p>
                    <p class="mt-2 text-lg"><a href="mailto:thedistricthamilton@gmail.com" class="hover:text-brand break-all">thedistricthamilton@gmail.com</a></p>
                </div>
                <div>
                    <p class="eyebrow">Hours</p>
                    <dl class="mt-3 grid grid-cols-[auto_1fr] gap-x-6 gap-y-1 text-sm max-w-sm">
                        <dt class="uppercase tracking-wider font-semibold">Monday</dt><dd class="numeral text-ink-muted">Closed</dd>
                        <dt class="uppercase tracking-wider font-semibold">Tue – Fri</dt><dd class="numeral text-ink-muted">5pm – 11pm</dd>
                        <dt class="uppercase tracking-wider font-semibold">Saturday</dt><dd class="numeral text-ink-muted">11am – 10pm</dd>
                        <dt class="uppercase tracking-wider font-semibold">Sunday</dt><dd class="numeral text-ink-muted">11am – 9pm</dd>
                    </dl>
                </div>
                <div>
                    <p class="eyebrow">Follow</p>
                    <div class="flex gap-3 mt-3">
                        <a href="https://www.facebook.com/profile.php?id=61576713382021" class="w-11 h-11 rounded-full border border-line flex items-center justify-center hover:border-brand hover:text-brand transition" aria-label="Facebook">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/></svg>
                        </a>
                        <a href="https://www.instagram.com/districttapasbarhamilton/" class="w-11 h-11 rounded-full border border-line flex items-center justify-center hover:border-brand hover:text-brand transition" aria-label="Instagram">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 1.366.062 2.633.334 3.608 1.308.975.975 1.247 2.242 1.308 3.608.058 1.266.07 1.646.07 4.85s-.012 3.584-.07 4.85c-.062 1.366-.334 2.633-1.308 3.608-.975.975-2.242 1.247-3.608 1.308-1.266.058-1.646.07-4.85.07s-3.584-.012-4.85-.07c-1.366-.062-2.633-.334-3.608-1.308-.975-.975-1.247-2.242-1.308-3.608C2.175 15.747 2.163 15.367 2.163 12s.012-3.584.07-4.85c.062-1.366.334-2.633 1.308-3.608C4.516 2.567 5.783 2.295 7.15 2.233 8.416 2.175 8.796 2.163 12 2.163zm0 3.135A5.865 5.865 0 1117.865 12 5.872 5.872 0 0112 6.135zm0 9.668A3.803 3.803 0 1015.803 12 3.807 3.807 0 0012 15.803z"/></svg>
                        </a>
                    </div>
                </div>
            </div>

            {{-- FORM --}}
            <div>
                <p class="eyebrow">Form</p>
                <h2 class="font-display text-3xl mt-2 mb-8">Drop us a note</h2>

                <form id="contactForm" action="{{ route('contact.submit') }}" method="POST" class="space-y-5">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label for="first_name" class="label">First name <span class="label-req">*</span></label>
                            <input type="text" id="first_name" name="first_name" required value="{{ old('first_name') }}" class="field @error('first_name') field--error @enderror">
                            @error('first_name') <p class="field-error">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="last_name" class="label">Last name <span class="label-req">*</span></label>
                            <input type="text" id="last_name" name="last_name" required value="{{ old('last_name') }}" class="field @error('last_name') field--error @enderror">
                            @error('last_name') <p class="field-error">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label for="email" class="label">Email <span class="label-req">*</span></label>
                            <input type="email" id="email" name="email" required value="{{ old('email') }}" class="field @error('email') field--error @enderror">
                            @error('email') <p class="field-error">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="phone" class="label">Phone <span class="label-req">*</span></label>
                            <input type="tel" id="phone" name="phone" required value="{{ old('phone') }}" class="field @error('phone') field--error @enderror">
                            @error('phone') <p class="field-error">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label for="subject" class="label">Subject</label>
                        <input type="text" id="subject" name="subject" value="{{ old('subject') }}" class="field">
                    </div>

                    <div>
                        <label for="message" class="label">Message <span class="label-req">*</span></label>
                        <textarea id="message" name="message" rows="5" required class="field @error('message') field--error @enderror">{{ old('message') }}</textarea>
                        @error('message') <p class="field-error">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit" class="btn btn-ember btn-lg">
                        Send message
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-4 h-4 ml-2"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>

                    <div id="contactFormResponse" class="hidden mt-2 alert" role="status"></div>
                </form>
            </div>
        </div>
    </section>

    {{-- MAP --}}
    <section class="pb-24">
        <div class="container-ed">
            <div class="rounded-2xl overflow-hidden shadow-lg">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2907.2631553510034!2d-79.8776914843762!3d43.258605779137074!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x882c9b83efa7fb19%3A0xb3bb81c8b578c6a!2s61%20Barton%20St%20E%2C%20Hamilton%2C%20ON%20L8L%202V7!5e0!3m2!1sen!2sca!4v1623331088837!5m2!1sen!2sca"
                    width="100%" height="420" style="border:0; display:block;" allowfullscreen loading="lazy" title="Map to The District"></iframe>
            </div>
        </div>
    </section>

    {{-- FAQ --}}
    <section class="section-pad bg-paper-warm">
        <div class="container-ed--narrow">
            <div class="text-center mb-12">
                <p class="eyebrow">FAQ</p>
                <h2 class="font-display text-4xl md:text-5xl font-light mt-3">Frequently asked</h2>
            </div>
            <dl class="divide-y divide-line" style="--tw-divide-opacity:1;" x-data="{ open: null }">
                @foreach($faqs as $i => $faq)
                    <div class="py-5">
                        <button type="button" @click="open = open === {{ $i }} ? null : {{ $i }}" class="w-full flex justify-between items-start gap-4 text-left group">
                            <h3 class="font-display text-xl group-hover:text-brand transition">{{ $faq['q'] }}</h3>
                            <svg class="w-5 h-5 shrink-0 mt-1 transition" :class="open === {{ $i }} ? 'rotate-45 text-brand' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4"/>
                            </svg>
                        </button>
                        <div x-show="open === {{ $i }}" x-collapse>
                            <p class="mt-3 text-ink-muted leading-relaxed">{{ $faq['a'] }}</p>
                        </div>
                    </div>
                @endforeach
            </dl>
        </div>
    </section>
@endsection

@push('scripts')
<script>
    document.getElementById('contactForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        const form = this;
        const submitButton = form.querySelector('button[type="submit"]');
        const responseDiv = document.getElementById('contactFormResponse');
        const originalLabel = submitButton.innerHTML;
        submitButton.disabled = true;
        submitButton.textContent = 'Sending…';

        fetch(form.action, {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            body: new FormData(form)
        })
        .then(r => r.json())
        .then(data => {
            responseDiv.classList.remove('hidden', 'alert-error');
            responseDiv.classList.add('alert-success');
            responseDiv.innerHTML = '<span>' + (data.message || 'Thanks — we\'ll be in touch shortly.') + '</span>';
            form.reset();
            if (window.Alpine?.store('ui')) window.Alpine.store('ui').flash('Message sent');
        })
        .catch(() => {
            responseDiv.classList.remove('hidden', 'alert-success');
            responseDiv.classList.add('alert-error');
            responseDiv.innerHTML = '<span>Sorry — something went wrong. Please try again or call us.</span>';
        })
        .finally(() => {
            submitButton.disabled = false;
            submitButton.innerHTML = originalLabel;
            responseDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
        });
    });
</script>
@endpush
