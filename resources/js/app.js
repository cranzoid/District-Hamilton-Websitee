import './bootstrap';

import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';

Alpine.plugin(collapse);
window.Alpine = Alpine;

/* ---------- Global Alpine store: cart count + toast ------------------------ */
Alpine.store('ui', {
    cartCount: 0,
    toast: { show: false, message: '', tone: 'success' },
    scrolled: false,

    init() {
        const badge = document.querySelector('[data-cart-count]');
        if (badge) this.cartCount = parseInt(badge.dataset.cartCount || '0', 10);

        this.scrolled = window.scrollY > 8;
        window.addEventListener('scroll', () => {
            this.scrolled = window.scrollY > 8;
        }, { passive: true });
    },

    flash(message, tone = 'success', duration = 3200) {
        this.toast = { show: true, message, tone };
        clearTimeout(this._t);
        this._t = setTimeout(() => { this.toast.show = false; }, duration);
    },

    bumpCart(delta = 1) {
        this.cartCount = Math.max(0, this.cartCount + delta);
    },
});

/* ---------- Intersection-based reveal --------------------------------------- */
Alpine.directive('reveal', (el) => {
    el.style.opacity = '0';
    el.style.transform = 'translateY(16px)';
    el.style.transition = 'opacity .7s cubic-bezier(.2,.8,.2,1), transform .7s cubic-bezier(.2,.8,.2,1)';
    const io = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                el.style.opacity = '1';
                el.style.transform = 'translateY(0)';
                io.unobserve(el);
            }
        });
    }, { threshold: 0.12 });
    io.observe(el);
});

Alpine.start();
