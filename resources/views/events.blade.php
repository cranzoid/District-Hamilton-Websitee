@extends('layouts.app')

@section('title', 'Private events + buyouts')
@section('description', 'Host your next event at The District Tapas + Bar — private dining, standing receptions, and midday gatherings in downtown Hamilton.')

@section('content')
    <section class="subhero">
        <div class="container-ed">
            <p class="eyebrow">Private events</p>
            <h1 class="font-display text-6xl md:text-8xl font-light leading-[1.02] mt-3">
                A room <em class="italic text-brand">built</em> for the occasion.
            </h1>
            <p class="lede mt-8 max-w-2xl">
                From 20-person seated dinners to full-room receptions of 65, we'll build the evening around your group — custom menus, staffed bar, and a space designed for lingering.
            </p>
            <a href="#event-inquiry" class="btn btn-ember btn-lg mt-10">Start an inquiry</a>
        </div>
    </section>

    {{-- EVENT TYPES --}}
    <section class="section-pad">
        <div class="container-ed space-y-20">
            @foreach([
                [
                    'num' => '01',
                    'kind' => 'Standing reception',
                    'title' => 'Cocktails at the bar',
                    'body' => "The bar at The District is the ideal gathering point for standing receptions. Premium finishes and warm ambiance — perfect for socializing and networking over tapas and cocktails.",
                    'cap' => 'Up to 65 guests',
                    'img' => '/images/Restaurant_food/int-4.jpg',
                    'flip' => false,
                ],
                [
                    'num' => '02',
                    'kind' => 'Seated private dining',
                    'title' => 'Family-style, multi-course',
                    'body' => "Our team will customize a private dining experience for you and your guests. Seated dinners are served family-style with multiple share-plate courses — a communal, intimate way to experience our menu.",
                    'cap' => 'Up to 25 seated',
                    'img' => '/images/Restaurant_food/int-3.jpg',
                    'flip' => true,
                ],
                [
                    'num' => '03',
                    'kind' => 'Midday reception',
                    'title' => 'Daytime buyouts',
                    'body' => "Available between 2:30pm – 4:30pm. Executive meetings, team-building events, customer appreciation receptions — our bright space and tailored service make the perfect daytime backdrop.",
                    'cap' => 'Up to 25 standing · 20 seated',
                    'img' => '/images/Restaurant_food/food-4.jpg',
                    'flip' => false,
                ],
            ] as $event)
                <article class="grid md:grid-cols-2 gap-10 md:gap-16 items-center">
                    <div class="{{ $event['flip'] ? 'md:order-2' : '' }}">
                        <img src="{{ $event['img'] }}" alt="{{ $event['title'] }}" class="w-full h-[460px] object-cover rounded-2xl shadow-lg">
                    </div>
                    <div>
                        <p class="numeral text-5xl text-brand">{{ $event['num'] }}</p>
                        <p class="eyebrow mt-4">{{ $event['kind'] }}</p>
                        <h2 class="font-display text-4xl md:text-5xl font-light italic mt-3 leading-tight">{{ $event['title'] }}</h2>
                        <p class="mt-5 text-ink-muted leading-relaxed">{{ $event['body'] }}</p>
                        <p class="mt-6 inline-flex items-center gap-2 chip chip--dark">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            {{ $event['cap'] }}
                        </p>
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    {{-- GALLERY --}}
    <section class="section-pad bg-paper-warm">
        <div class="container-ed">
            <div class="text-center mb-12">
                <p class="eyebrow">The space</p>
                <h2 class="font-display text-4xl md:text-5xl font-light mt-3">Take a look</h2>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                @foreach(['int-5.jpg', 'int-6.jpg', 'int-7.jpg', 'int-4.jpg'] as $img)
                    <div class="aspect-square overflow-hidden rounded-xl">
                        <img src="/images/Restaurant_food/{{ $img }}" alt="" class="w-full h-full object-cover hover:scale-105 transition duration-700" loading="lazy">
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- INQUIRY FORM --}}
    <section id="event-inquiry" class="section-pad">
        <div class="container-ed">
            <div class="grid lg:grid-cols-[1fr_1.3fr] gap-12 bg-ink text-paper rounded-2xl overflow-hidden">
                <div class="p-10 md:p-14 bg-ink-soft">
                    <p class="eyebrow text-brand-light">Inquiry</p>
                    <h2 class="font-display text-4xl md:text-5xl font-light mt-3 leading-tight">Let us help you plan</h2>
                    <p class="mt-6 text-paper/70 leading-relaxed">
                        Fill in the form and we'll use it as a starting point. Final details — date, menu, timing — we'll work out together.
                    </p>

                    <ul class="mt-10 space-y-6">
                        @foreach([
                            ['Customized menus', 'Our chef will craft a menu around your preferences and any dietary needs.'],
                            ['Bar packages', 'Premium open bars or curated cocktail menus — options for every event and budget.'],
                            ['Dedicated service', 'Our professional staff will ensure your event runs smoothly start to finish.'],
                        ] as $perk)
                            <li class="flex gap-4">
                                <div class="w-10 h-10 rounded-full bg-brand-light/15 text-brand-light flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <div>
                                    <h3 class="font-display text-xl">{{ $perk[0] }}</h3>
                                    <p class="text-sm text-paper/60 mt-1 leading-relaxed">{{ $perk[1] }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="p-10 md:p-14 bg-paper text-ink">
                    <h3 class="font-display text-2xl mb-8">Event inquiry form</h3>

                    <form id="eventForm" action="{{ route('events.inquiry') }}" method="POST" class="space-y-5">
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
                            <label for="company" class="label">Company</label>
                            <input type="text" id="company" name="company" value="{{ old('company') }}" class="field">
                        </div>

                        <div>
                            <label for="event_type" class="label">Type of event <span class="label-req">*</span></label>
                            <input type="text" id="event_type" name="event_type" placeholder="Birthday, corporate, wedding reception…" required value="{{ old('event_type') }}" class="field @error('event_type') field--error @enderror">
                            @error('event_type') <p class="field-error">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                            <div>
                                <label for="guest_count" class="label">Guests <span class="label-req">*</span></label>
                                <input type="number" id="guest_count" name="guest_count" placeholder="1 – 65" min="1" max="65" required value="{{ old('guest_count') }}" class="field @error('guest_count') field--error @enderror">
                                @error('guest_count') <p class="field-error">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="event_date" class="label">Date <span class="label-req">*</span></label>
                                <input type="date" id="event_date" name="event_date" required value="{{ old('event_date') }}" class="field @error('event_date') field--error @enderror">
                                @error('event_date') <p class="field-error">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="event_time" class="label">Time <span class="label-req">*</span></label>
                                <input type="text" id="event_time" name="event_time" placeholder="6pm – 10pm" required value="{{ old('event_time') }}" class="field @error('event_time') field--error @enderror">
                                @error('event_time') <p class="field-error">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <label for="details" class="label">Additional details</label>
                            <textarea id="details" name="details" rows="4" placeholder="Dietary needs, themes, timing — anything to help us plan." class="field">{{ old('details') }}</textarea>
                        </div>

                        <input type="hidden" name="admin_email" value="thedistricthamilton@gmail.com">

                        <button type="submit" class="btn btn-ember btn-lg btn-block">
                            Submit inquiry
                        </button>

                        <div id="eventFormResponse" class="hidden alert" role="status"></div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    {{-- TESTIMONIALS --}}
    <section class="section-pad bg-paper-deep">
        <div class="container-ed">
            <div class="text-center mb-12">
                <p class="eyebrow">Hosts</p>
                <h2 class="font-display text-4xl md:text-5xl font-light mt-3">What our hosts say</h2>
            </div>
            <div class="grid md:grid-cols-3 gap-6">
                @foreach([
                    ['"We hosted our company\'s annual dinner at The District and it was exceptional. The staff was attentive, the food was amazing — they took care of every detail."', 'Christina L.', 'Corporate dinner'],
                    ['"I celebrated my 40th with 25 friends. The tapas-style menu was perfect — everyone got to try different dishes. The atmosphere was exactly what I wanted."', 'James R.', 'Birthday celebration'],
                    ['"We had our engagement party at The District and couldn\'t have been happier. They helped us create a custom menu that honored both our backgrounds."', 'Sophia & Daniel', 'Engagement party'],
                ] as $t)
                    <figure class="bg-paper border border-line rounded-xl p-8 flex flex-col">
                        <svg class="w-7 h-7 text-brand mb-3" fill="currentColor" viewBox="0 0 24 24"><path d="M6 17h3l2-4V7H5v6h3zm8 0h3l2-4V7h-6v6h3z"/></svg>
                        <blockquote class="font-display italic text-lg leading-snug flex-grow">{{ $t[0] }}</blockquote>
                        <figcaption class="mt-6 pt-4 border-t border-line-soft" style="border-color: rgba(14,14,14,0.06);">
                            <p class="font-semibold">— {{ $t[1] }}</p>
                            <p class="eyebrow mt-1">{{ $t[2] }}</p>
                        </figcaption>
                    </figure>
                @endforeach
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="section-pad bg-ink text-paper">
        <div class="container-ed--narrow text-center">
            <p class="eyebrow text-brand-light">Get in touch</p>
            <h2 class="font-display text-5xl md:text-6xl font-light mt-4 leading-tight">Ready to plan?</h2>
            <div class="mt-10 flex flex-wrap gap-4 justify-center">
                <a href="#event-inquiry" class="btn btn-ember btn-lg">Inquiry form</a>
                <a href="tel:+19055222580" class="btn btn-outline-paper btn-lg">Call (905) 522-2580</a>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script>
    document.getElementById('eventForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        const form = this;
        const submitButton = form.querySelector('button[type="submit"]');
        const responseDiv = document.getElementById('eventFormResponse');
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
            if (window.Alpine?.store('ui')) window.Alpine.store('ui').flash('Inquiry sent');
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
