import { createI18n, type I18n } from 'vue-i18n';
import type { Ref } from 'vue';
import en from './locales/en.json';
import ru from './locales/ru.json';

export const SUPPORTED_LOCALES = ['ru', 'en'] as const;
export type SupportedLocale = (typeof SUPPORTED_LOCALES)[number];

export const DEFAULT_LOCALE: SupportedLocale = 'ru';
const LOCALE_STORAGE_KEY = 'locale';

function readStoredLocale(): SupportedLocale {
    if (typeof window === 'undefined') return DEFAULT_LOCALE;
    const stored = window.localStorage.getItem(LOCALE_STORAGE_KEY);
    return (SUPPORTED_LOCALES as readonly string[]).includes(stored ?? '')
        ? (stored as SupportedLocale)
        : DEFAULT_LOCALE;
}

export const i18n: I18n = createI18n({
    legacy: false,
    locale: readStoredLocale(),
    fallbackLocale: 'en',
    messages: { ru, en },
});

// In composition mode (legacy: false), i18n.global.locale is a writable ref
// but the library's public type includes the legacy string form too. Cast to
// the composition shape for comfortable access.
function localeRef(): Ref<SupportedLocale> {
    return i18n.global.locale as unknown as Ref<SupportedLocale>;
}

export function setLocale(locale: SupportedLocale): void {
    localeRef().value = locale;
    if (typeof window !== 'undefined') {
        window.localStorage.setItem(LOCALE_STORAGE_KEY, locale);
        document.documentElement.setAttribute('lang', locale);
    }
}

export function applyInitialLocaleAttribute(): void {
    if (typeof document !== 'undefined') {
        document.documentElement.setAttribute('lang', localeRef().value);
    }
}
