@php
    // Expects: $item (MenuItem), optional $showAddToCart (default true)
    $showAddToCart = $showAddToCart ?? true;
    $image = $item->image_path
        ? asset('storage/' . $item->image_path)
        : asset('images/placeholder-food.jpg');
@endphp
<article class="card-ed group flex flex-col h-full">
    <a href="{{ route('menu.item', $item->slug) }}" class="card-dish-img block">
        <img src="{{ $image }}" alt="{{ $item->name }}" loading="lazy" decoding="async" class="w-full h-full object-cover">
        @if ($item->is_featured)
            <span class="absolute top-3 left-3 chip chip--dark">Chef's pick</span>
        @endif
        @if (!$item->is_available)
            <div class="absolute inset-0 bg-ink/60 flex items-center justify-center">
                <span class="chip chip--ember">Unavailable</span>
            </div>
        @endif
    </a>
    <div class="p-6 flex flex-col flex-grow">
        <div class="flex justify-between items-baseline gap-4 mb-2">
            <h3 class="font-display text-xl leading-tight">
                <a href="{{ route('menu.item', $item->slug) }}" class="hover:text-ember transition">{{ $item->name }}</a>
            </h3>
            <span class="numeral text-brand-dark text-lg shrink-0">${{ number_format($item->price, 2) }}</span>
        </div>
        @if ($item->description)
            <p class="text-ink-muted text-sm leading-relaxed flex-grow">{{ \Illuminate\Support\Str::limit($item->description, 120) }}</p>
        @endif
        @if ($item->relationLoaded('tags') && $item->tags->count() > 0)
            <div class="flex flex-wrap gap-1.5 mt-4">
                @foreach ($item->tags->take(3) as $tag)
                    <span class="chip">{{ $tag->tag_name }}</span>
                @endforeach
            </div>
        @endif
        @if ($showAddToCart && $item->is_available)
            <div class="mt-6 flex items-center justify-between pt-4 border-t border-line-soft" style="border-color: rgba(14,14,14,0.06);">
                <a href="{{ route('menu.item', $item->slug) }}" class="arrow-link text-xs">
                    View dish
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
                <button type="button"
                        class="btn btn-ink btn-sm add-to-cart-btn"
                        data-id="{{ $item->id }}"
                        data-name="{{ $item->name }}"
                        data-price="{{ $item->price }}">
                    Add
                </button>
            </div>
        @endif
    </div>
</article>
