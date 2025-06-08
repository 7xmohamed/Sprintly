const THEME_KEY = 'dashboard-theme';
const DARK_THEME = 'dark';
const LIGHT_THEME = 'light';

// Get theme from localStorage or system preference
const getTheme = () => {
    const savedTheme = localStorage.getItem(THEME_KEY);
    if (savedTheme && [DARK_THEME, LIGHT_THEME].includes(savedTheme)) {
        return savedTheme;
    }
    return window.matchMedia('(prefers-color-scheme: dark)').matches ? DARK_THEME : LIGHT_THEME;
};

// Apply theme to document
const applyTheme = (theme) => {
    document.documentElement.setAttribute('data-theme', theme);
    localStorage.setItem(THEME_KEY, theme);
};

// Toggle theme function
const toggleTheme = () => {
    const currentTheme = document.documentElement.getAttribute('data-theme');
    const newTheme = currentTheme === LIGHT_THEME ? DARK_THEME : LIGHT_THEME;
    applyTheme(newTheme);
};

// Initialize theme system
const initializeTheme = () => {
    // Apply initial theme
    applyTheme(getTheme());

    // Add toggle function to window
    window.toggleTheme = toggleTheme;

    // Listen for system theme changes
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
        if (!localStorage.getItem(THEME_KEY)) {
            applyTheme(e.matches ? DARK_THEME : LIGHT_THEME);
        }
    });
};

export { initializeTheme, toggleTheme }; 