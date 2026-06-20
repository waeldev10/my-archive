import Alpine from 'alpinejs';

window.Alpine = Alpine;

/**
 * Theme store — shared across all Livewire components via Alpine.
 * Provides reactive theme state (light / dark / system) and a toggle method.
 */
document.addEventListener('alpine:init', () => {
    Alpine.store('theme', {
        mode: localStorage.getItem('theme') || 'system',

        get effective() {
            if (this.mode === 'dark') return 'dark';
            if (this.mode === 'light') return 'light';
            return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        },

        get isDark() {
            return this.effective === 'dark';
        },

        setTheme(mode) {
            this.mode = mode;
            localStorage.setItem('theme', mode);
            this.apply();
        },

        toggle() {
            this.setTheme(this.isDark ? 'light' : 'dark');
        },

        apply() {
            const isDark = this.isDark;
            const root = document.documentElement;

            // Enable smooth transition
            root.classList.add('theme-transitioning');

            if (isDark) {
                root.classList.add('dark');
            } else {
                root.classList.remove('dark');
            }

            // Remove transition class after animation completes
            setTimeout(() => root.classList.remove('theme-transitioning'), 300);
        },
    });
});

// Listen for system preference changes when in 'system' mode
window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
    const store = Alpine.store('theme');
    if (store.mode === 'system') {
        store.apply();
    }
});

// Sync theme across tabs
window.addEventListener('storage', (e) => {
    if (e.key === 'theme') {
        const store = Alpine.store('theme');
        store.mode = e.newValue || 'system';
        store.apply();
    }
});

Alpine.start();
