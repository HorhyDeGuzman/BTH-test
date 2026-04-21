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
    <label class="flex items-center gap-2 text-sm">
        <span class="text-gray-600">Category:</span>
        <select
            v-model.number="selected"
            :disabled="disabled"
            class="rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:bg-gray-100"
        >
            <option :value="null">All categories</option>
            <option v-for="category in categories" :key="category.id" :value="category.id">
                {{ category.name }}
            </option>
        </select>
    </label>
</template>
