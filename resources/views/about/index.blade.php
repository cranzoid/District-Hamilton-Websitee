@extends('layouts.app')

@section('title', 'About The District')
@section('description', 'The District Tapas + Bar — bold global flavours in the heart of downtown Hamilton at 61 Barton Street East.')

@section('content')
    <section class="subhero">
        <div class="container-ed">
            <p class="eyebrow">About</p>
            <h1 class="font-display text-6xl md:text-8xl font-light leading-[1.02] mt-3">
                Bold flavours. <br><em class="italic text-brand">Hamilton</em> roots.
            </h1>
            <p class="lede mt-8 max-w-2xl">
                Located at 61 Barton East, we're redefining Hamilton's dining scene with a creative, shareable menu that invites conversation and connection.
            </p>
        </div>
    </section>

    {{-- STORY --}}
    <section class="section-pad">
        <div class="container-ed grid md:grid-cols-2 gap-12 md:gap-20 items-center">
            <div class="order-2 md:order-1">
                <p class="eyebrow">Chapter one</p>
                <h2 class="font-display text-4xl md:text-5xl font-light italic mt-3">Our story</h2>
                <p class="mt-6 text-ink-muted leading-relaxed">
                    Welcome to The District Tapas + Bar, where bold global flavours meet Hamilton's vibrant spirit. We're redefining the local dining experience with a creative, shareable menu built for conversation.
                </p>
                <p class="mt-4 text-ink-muted leading-relaxed">
                    At The District, food is more than sustenance — it's a celebration. Our internationally inspired tapas are crafted to delight. Whether you're in for brunch, dinner, or drinks, every dish is designed to excite your palate and spark discovery.
                </p>
            </div>
            <div class="order-1 md:order-2 relative">
                <img src="{{ asset('images/Restaurant_food/int-2.jpg') }}" alt="The District interior" class="rounded-2xl w-full h-[540px] object-cover shadow-lg">
            </div>
        </div>
    </section>

    {{-- PHILOSOPHY --}}
    <section class="section-pad bg-paper-warm">
        <div class="container-ed">
            <div class="text-center max-w-2xl mx-auto mb-14">
                <p class="eyebrow">How we cook</p>
                <h2 class="font-display text-4xl md:text-5xl font-light mt-3">Three things we care about</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach([
                    ['num' => '01', 'title' => 'Fresh, local ingredients', 'body' => "We source from Hamilton-area producers when we can. Shorter supply chains mean better flavour and stronger community."],
                    ['num' => '02', 'title' => 'Authentic technique', 'body' => "Our menu stays true to Spanish and Mediterranean cooking, while leaning into creative twists that are distinctly ours."],
                    ['num' => '03', 'title' => 'A room for gathering', 'body' => "Food brings people together. Our space is designed for long dinners, loud laughs, and meeting strangers at the bar."],
                ] as $p)
                    <article class="bg-paper rounded-2xl p-8 border border-line">
                        <p class="numeral text-5xl text-brand">{{ $p['num'] }}</p>
                        <h3 class="font-display text-2xl mt-5 leading-tight">{{ $p['title'] }}</h3>
                        <p class="mt-3 text-ink-muted leading-relaxed text-sm">{{ $p['body'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- VISIT --}}
    <section class="section-pad">
        <div class="container-ed grid md:grid-cols-2 gap-12 md:gap-16 items-stretch">
            <div>
                <p class="eyebrow">Visit</p>
                <h2 class="font-display text-4xl md:text-5xl font-light mt-3">Find us on Barton</h2>

                <dl class="mt-10 space-y-8">
                    <div>
                        <dt class="eyebrow">Location</dt>
                        <dd class="mt-2 text-lg">61 Barton Street East<br>Hamilton, ON L8L 2V7</dd>
                    </div>
                    <div>
                        <dt class="eyebrow">Hours</dt>
                        <dd class="mt-2 grid grid-cols-[auto_1fr] gap-x-6 gap-y-1 text-sm max-w-sm">
                            <span class="uppercase tracking-wider font-semibold">Monday</span><span class="numeral text-ink-muted">Closed</span>
                            <span class="uppercase tracking-wider font-semibold">Tue – Fri</span><span class="numeral text-ink-muted">5:00pm – 11:00pm</span>
                            <span class="uppercase tracking-wider font-semibold">Saturday</span><span class="numeral text-ink-muted">11:00am – 10:00pm</span>
                            <span class="uppercase tracking-wider font-semibold">Sunday</span><span class="numeral text-ink-muted">11:00am – 9:00pm</span>
                        </dd>
                    </div>
                    <div>
                        <dt class="eyebrow">Contact</dt>
                        <dd class="mt-2 text-lg">
                            <a href="tel:+19055222580" class="numeral hover:text-brand">(905) 522-2580</a><br>
                            <a href="mailto:thedistricthamilton@gmail.com" class="hover:text-brand">thedistricthamilton@gmail.com</a>
                        </dd>
                    </div>
                </dl>

                <div class="mt-10 flex flex-wrap gap-3">
                    <a href="#reservation-widget" class="btn btn-ember">Reserve</a>
                    <a href="{{ route('contact') }}" class="btn btn-outline">Contact us</a>
                </div>
            </div>
            <div class="rounded-2xl overflow-hidden shadow-lg min-h-[420px]">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2907.2631553510034!2d-79.8776914843762!3d43.258605779137074!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x882c9b83efa7fb19%3A0xb3bb81c8b578c6a!2s61%20Barton%20St%20E%2C%20Hamilton%2C%20ON%20L8L%202V7!5e0!3m2!1sen!2sca!4v1623331088837!5m2!1sen!2sca"
                    width="100%" height="100%" style="border:0; min-height: 420px;" allowfullscreen loading="lazy" title="District map"></iframe>
            </div>
        </div>
    </section>
@endsection
