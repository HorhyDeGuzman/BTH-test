<script setup lang="ts">
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { pickLocalized } from '@/common/helpers';
import type { Category } from '@/modules/categories';

const props = defineProps<{
    categories: Category[];
    modelValue: number | null;
    disabled?: boolean;
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: number | null): void;
}>();

const { t, locale } = useI18n();

const selected = computed({
    get: () => props.modelValue,
    set: (value: number | null) => emit('update:modelValue', value),
});

function displayName(category: Category): string {
    return pickLocalized(category.name, category.name_en, locale.value);
}
</script>

<template>
    <div class="relative">
        <select
            v-model.number="selected"
            :disabled="disabled"
            :aria-label="t('public.filter.category_label')"
            class="field w-full appearance-none pl-4 pr-10 sm:w-52"
        >
            <option :value="null">{{ t('public.filter.all_categories') }}</option>
            <option v-for="category in categories" :key="category.id" :value="category.id">
                {{ displayName(category) }}
            </option>
        </select>
        <svg
            class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400 dark:text-slate-500"
            viewBox="0 0 20 20"
            fill="currentColor"
        >
            <path
                fill-rule="evenodd"
                d="M10 14a1 1 0 0 1-.707-.293l-4-4a1 1 0 1 1 1.414-1.414L10 11.586l3.293-3.293a1 1 0 0 1 1.414 1.414l-4 4A1 1 0 0 1 10 14Z"
                clip-rule="evenodd"
            />
        </svg>
    </div>
</template>
