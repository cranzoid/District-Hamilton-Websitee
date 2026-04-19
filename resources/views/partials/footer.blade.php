@php
    $hours = [
        ['Monday', 'Closed'],
        ['Tue – Fri', '5:00pm – 11:00pm'],
        ['Saturday', '11:00am – 10:00pm'],
        ['Sunday',   '11:00am – 9:00pm'],
    ];
@endphp
<footer class="footer-ed">
    <div class="container-ed">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-10 md:gap-8">
            <div class="md:col-span-5">
                <p class="eyebrow text-paper/60">The District</p>
                <h3 class="font-display text-4xl md:text-5xl font-light leading-tight mt-4 text-paper">
                    Bold <em class="text-brand-light italic">tapas</em> <br> in downtown Hamilton.
                </h3>
                <p class="mt-6 text-paper/70 max-w-md leading-relaxed">
                    Share plates, signature cocktails, and a room built for lingering. Weekly specials change every night — come by, stay late.
                </p>
                <div class="flex items-center gap-4 mt-8">
                    <a href="https://www.facebook.com/profile.php?id=61576713382021" aria-label="Facebook" class="w-10 h-10 rounded-full border border-paper/20 flex items-center justify-center hover:border-brand-light hover:text-brand-light transition">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/></svg>
                    </a>
                    <a href="https://www.instagram.com/districttapasbarhamilton/" aria-label="Instagram" class="w-10 h-10 rounded-full border border-paper/20 flex items-center justify-center hover:border-brand-light hover:text-brand-light transition">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 1.366.062 2.633.334 3.608 1.308.975.975 1.247 2.242 1.308 3.608.058 1.266.07 1.646.07 4.85s-.012 3.584-.07 4.85c-.062 1.366-.334 2.633-1.308 3.608-.975.975-2.242 1.247-3.608 1.308-1.266.058-1.646.07-4.85.07s-3.584-.012-4.85-.07c-1.366-.062-2.633-.334-3.608-1.308-.975-.975-1.247-2.242-1.308-3.608C2.175 15.747 2.163 15.367 2.163 12s.012-3.584.07-4.85c.062-1.366.334-2.633 1.308-3.608C4.516 2.567 5.783 2.295 7.15 2.233 8.416 2.175 8.796 2.163 12 2.163zm0 1.838c-3.141 0-3.49.011-4.72.068-1.018.046-1.572.215-1.94.357-.488.19-.836.416-1.2.782-.366.366-.592.712-.782 1.2-.142.368-.31.922-.357 1.94C3.011 8.51 3 8.859 3 12s.011 3.49.068 4.72c.046 1.018.215 1.572.357 1.94.19.488.416.836.782 1.2.366.366.712.592 1.2.782.368.142.922.31 1.94.357C8.51 20.989 8.859 21 12 21s3.49-.011 4.72-.068c1.018-.046 1.572-.215 1.94-.357.488-.19.836-.416 1.2-.782.366-.366.592-.712.782-1.2.142-.368.31-.922.357-1.94.057-1.23.068-1.579.068-4.72s-.011-3.49-.068-4.72c-.046-1.018-.215-1.572-.357-1.94a3.23 3.23 0 00-.782-1.2 3.23 3.23 0 00-1.2-.782c-.368-.142-.922-.31-1.94-.357C15.49 3.011 15.141 3 12 3zm0 3.135A5.865 5.865 0 1117.865 12 5.872 5.872 0 0112 6.135zm0 9.668A3.803 3.803 0 1015.803 12 3.807 3.807 0 0012 15.803zm7.468-9.9a1.368 1.368 0 11-1.368-1.368 1.37 1.37 0 011.368 1.368z"/></svg>
                    </a>
                </div>
            </div>

            <div class="md:col-span-3">
                <p class="eyebrow text-paper/60">Visit</p>
                <address class="not-italic mt-4 text-paper/80 leading-relaxed">
                    61 Barton Street East<br>
                    Hamilton, ON L8L 2V7
                </address>
                <p class="mt-4 text-paper/80">
                    <a href="tel:+19055222580" class="hover:text-brand-light">(905) 522-2580</a><br>
                    <a href="mailto:thedistricthamilton@gmail.com" class="hover:text-brand-light">thedistricthamilton@gmail.com</a>
                </p>
            </div>

            <div class="md:col-span-4">
                <p class="eyebrow text-paper/60">Hours</p>
                <ul class="mt-4 space-y-1 text-paper/80">
                    @foreach ($hours as [$day, $time])
                        <li class="flex justify-between gap-4 border-b border-paper/10 py-1.5">
                            <span class="uppercase text-xs tracking-[0.18em] font-semibold">{{ $day }}</span>
                            <span class="numeral text-sm">{{ $time }}</span>
                        </li>
                    @endforeach
                </ul>
                <a href="{{ route('daily-offers') }}" class="mt-4 inline-flex items-center gap-2 text-brand-light text-sm font-semibold hover:gap-3 transition-all">
                    See weekly specials
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>
        </div>

        <div class="mt-16 pt-6 border-t border-paper/10 flex flex-col md:flex-row justify-between gap-4 text-xs text-paper/50 uppercase tracking-[0.18em]">
            <p>© {{ date('Y') }} The District Tapas + Bar</p>
            <div class="flex gap-6">
                <a href="{{ route('privacy') }}">Privacy</a>
                <a href="{{ route('terms') }}">Terms</a>
            </div>
        </div>
    </div>
</footer>
