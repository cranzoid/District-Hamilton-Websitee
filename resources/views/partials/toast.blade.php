{{-- Global toast, driven by Alpine.store('ui').toast --}}
<div x-data
     x-show="$store.ui.toast.show"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 translate-y-4"
     x-transition:enter-end="opacity-100 translate-y-0"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 translate-y-0"
     x-transition:leave-end="opacity-0 translate-y-4"
     class="fixed bottom-6 right-6 z-50 max-w-sm"
     x-cloak>
    <div class="bg-ink text-paper rounded-xl shadow-2xl px-5 py-4 flex items-center gap-3"
         :class="$store.ui.toast.tone === 'error' ? 'bg-ember' : ''">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        <p class="text-sm font-medium" x-text="$store.ui.toast.message"></p>
        <button @click="$store.ui.toast.show = false" class="ml-auto opacity-60 hover:opacity-100">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
</div>
