<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { SUPPORTED_LOCALES, setLocale, type SupportedLocale } from '@/core/i18n';

const { locale, t } = useI18n();

function handleChange(next: SupportedLocale): void {
    if (locale.value === next) return;
    setLocale(next);
}
</script>

<template>
    <div
        role="radiogroup"
        :aria-label="'Language'"
        class="inline-flex items-center rounded-full border border-slate-200 bg-slate-100/70 p-0.5 text-xs font-semibold"
    >
        <button
            v-for="code in SUPPORTED_LOCALES"
            :key="code"
            type="button"
            role="radio"
            :aria-checked="locale === code"
            :class="[
                'rounded-full px-2.5 py-1 transition-colors',
                locale === code
                    ? 'bg-white text-slate-900 shadow-soft'
                    : 'text-slate-500 hover:text-slate-900',
            ]"
            @click="handleChange(code)"
        >
            {{ t(`locale.${code}`) }}
        </button>
    </div>
</template>
