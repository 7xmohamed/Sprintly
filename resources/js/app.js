import './bootstrap';

// Only import and initialize Alpine if it's not already loaded by Livewire
if (!window.Alpine) {
    import('alpinejs').then((module) => {
        window.Alpine = module.default;
        window.Alpine.start();
    });
}

// Initialize theme based on user preference
document.addEventListener('DOMContentLoaded', () => {
    const savedTheme = localStorage.getItem('theme');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const defaultTheme = savedTheme || (prefersDark ? 'dark' : 'light');
    document.documentElement.setAttribute('data-theme', defaultTheme);
});
