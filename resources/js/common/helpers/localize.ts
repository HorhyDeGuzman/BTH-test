import { computed, type ComputedRef } from 'vue';
import { useI18n } from 'vue-i18n';

/**
 * Picks the right localized string from a pair of fields.
 *
 * Russian is the canonical / required value. English is optional, so
 * when the locale is 'en' we fall back to the Russian value if the
 * English translation is empty.
 */
export function useLocalized(
    ru: () => string | null | undefined,
    en: () => string | null | undefined,
): ComputedRef<string> {
    const { locale } = useI18n();
    return computed(() => {
        if (locale.value === 'en') {
            const enValue = en();
            if (enValue) return enValue;
        }
        return ru() ?? '';
    });
}

/**
 * Eager variant — call sites that already have reactive values and don't
 * need the lazy getter pattern can use this one-shot helper.
 */
export function pickLocalized(
    ru: string | null | undefined,
    en: string | null | undefined,
    locale: string,
): string {
    if (locale === 'en' && en) return en;
    return ru ?? '';
}
