<script setup lang="ts">
import { MoonIcon, SunIcon } from '@heroicons/vue/20/solid';
import { useI18n } from 'vue-i18n';
import { setTheme, useTheme, type Theme } from '@/core/theme';

const { t } = useI18n();
const { theme } = useTheme();

const items: Array<{ value: Theme; icon: typeof SunIcon }> = [
    { value: 'light', icon: SunIcon },
    { value: 'dark', icon: MoonIcon },
];

function handleChange(next: Theme): void {
    if (theme.value === next) return;
    setTheme(next);
}
</script>

<template>
    <div
        role="radiogroup"
        :aria-label="t('theme.label')"
        class="inline-flex items-center rounded-full border border-slate-200 bg-slate-100/70 p-0.5 text-slate-500 dark:border-slate-800 dark:bg-slate-800/70"
    >
        <button
            v-for="item in items"
            :key="item.value"
            type="button"
            role="radio"
            :aria-checked="theme === item.value"
            :title="t(`theme.${item.value}`)"
            :class="[
                'flex h-6 w-6 items-center justify-center rounded-full transition-colors',
                theme === item.value
                    ? 'bg-white text-slate-900 shadow-soft dark:bg-slate-950 dark:text-slate-100'
                    : 'hover:text-slate-900 dark:hover:text-slate-100',
            ]"
            @click="handleChange(item.value)"
        >
            <component :is="item.icon" class="h-3.5 w-3.5" />
        </button>
    </div>
</template>
