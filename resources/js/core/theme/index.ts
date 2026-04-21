import { computed, ref } from 'vue';

export const THEMES = ['light', 'dark'] as const;
export type Theme = (typeof THEMES)[number];

export const DEFAULT_THEME: Theme = 'light';
const THEME_STORAGE_KEY = 'theme';

function readStoredTheme(): Theme {
    if (typeof window === 'undefined') return DEFAULT_THEME;
    const stored = window.localStorage.getItem(THEME_STORAGE_KEY);
    return (THEMES as readonly string[]).includes(stored ?? '')
        ? (stored as Theme)
        : DEFAULT_THEME;
}

// Module-level ref — every useTheme() call shares the same state.
const theme = ref<Theme>(readStoredTheme());
const isDark = computed(() => theme.value === 'dark');

function applyTheme(): void {
    if (typeof document === 'undefined') return;
    document.documentElement.classList.toggle('dark', isDark.value);
    document.documentElement.style.colorScheme = isDark.value ? 'dark' : 'light';
}

export function setTheme(next: Theme): void {
    theme.value = next;
    if (typeof window !== 'undefined') {
        window.localStorage.setItem(THEME_STORAGE_KEY, next);
    }
    applyTheme();
}

export function toggleTheme(): void {
    setTheme(theme.value === 'dark' ? 'light' : 'dark');
}

export function useTheme() {
    return { theme, isDark, setTheme, toggleTheme };
}

/**
 * Called once from app.ts on boot so the style.colorScheme attribute
 * is kept in sync with the class the blade inline script already set.
 */
export function applyInitialTheme(): void {
    applyTheme();
}
