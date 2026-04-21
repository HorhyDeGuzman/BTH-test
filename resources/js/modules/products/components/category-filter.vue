<script setup lang="ts">
import { computed } from 'vue';
import type { Category } from '@/modules/categories';

const props = defineProps<{
    categories: Category[];
    modelValue: number | null;
    disabled?: boolean;
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: number | null): void;
}>();

const selected = computed({
    get: () => props.modelValue,
    set: (value: number | null) => emit('update:modelValue', value),
});
</script>

<template>
    <div class="relative">
        <select
            v-model.number="selected"
            :disabled="disabled"
            class="field w-full appearance-none pl-4 pr-10 sm:w-52"
        >
            <option :value="null">All categories</option>
            <option v-for="category in categories" :key="category.id" :value="category.id">
                {{ category.name }}
            </option>
        </select>
        <svg
            class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"
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
