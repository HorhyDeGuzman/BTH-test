import { computed, ref } from 'vue';

export const THEMES = ['light', 'system', 'dark'] as const;
export type Theme = (typeof THEMES)[number];

export const DEFAULT_THEME: Theme = 'system';
const THEME_STORAGE_KEY = 'theme';

function readStoredTheme(): Theme {
    if (typeof window === 'undefined') return DEFAULT_THEME;
    const stored = window.localStorage.getItem(THEME_STORAGE_KEY);
    return (THEMES as readonly string[]).includes(stored ?? '')
        ? (stored as Theme)
        : DEFAULT_THEME;
}

function systemPrefersDark(): boolean {
    return (
        typeof window !== 'undefined' &&
        window.matchMedia('(prefers-color-scheme: dark)').matches
    );
}

// Module-level ref — every useTheme() call shares the same state.
const theme = ref<Theme>(readStoredTheme());

const isDark = computed(() =>
    theme.value === 'dark' || (theme.value === 'system' && systemPrefersDark()),
);

function applyTheme(): void {
    if (typeof document === 'undefined') return;
    document.documentElement.classList.toggle('dark', isDark.value);
    document.documentElement.style.colorScheme = isDark.value ? 'dark' : 'light';
}

// React to system-theme changes while the user is on the 'system' setting.
if (typeof window !== 'undefined') {
    const media = window.matchMedia('(prefers-color-scheme: dark)');
    const onChange = () => {
        if (theme.value === 'system') applyTheme();
    };
    if (typeof media.addEventListener === 'function') {
        media.addEventListener('change', onChange);
    } else if (typeof (media as MediaQueryList).addListener === 'function') {
        // Older Safari
        (media as MediaQueryList).addListener(onChange);
    }
}

export function setTheme(next: Theme): void {
    theme.value = next;
    if (typeof window !== 'undefined') {
        window.localStorage.setItem(THEME_STORAGE_KEY, next);
    }
    applyTheme();
}

export function useTheme() {
    return { theme, isDark, setTheme };
}

/**
 * Called once from app.ts on boot so the attribute matches the initial
 * computed value (the blade inline script already sets the class before
 * CSS loads — this keeps <style> color-scheme in sync too).
 */
export function applyInitialTheme(): void {
    applyTheme();
}
