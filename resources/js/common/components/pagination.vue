<script setup lang="ts">
import { computed } from 'vue';
import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/20/solid';

const props = defineProps<{
    currentPage: number;
    lastPage: number;
    disabled?: boolean;
}>();

const emit = defineEmits<{
    (e: 'update:currentPage', value: number): void;
}>();

const hasPrev = computed(() => props.currentPage > 1);
const hasNext = computed(() => props.currentPage < props.lastPage);

function go(page: number) {
    if (props.disabled) return;
    if (page < 1 || page > props.lastPage) return;
    emit('update:currentPage', page);
}
</script>

<template>
    <nav
        v-if="lastPage > 1"
        class="flex items-center justify-center gap-2 text-sm"
        aria-label="Pagination"
    >
        <button
            type="button"
            class="btn-secondary h-9 w-9 !p-0"
            :disabled="!hasPrev || disabled"
            @click="go(currentPage - 1)"
        >
            <ChevronLeftIcon class="h-4 w-4" />
        </button>
        <div class="px-2 font-medium text-slate-600">
            Page <span class="text-slate-900">{{ currentPage }}</span>
            <span class="text-slate-400"> / {{ lastPage }}</span>
        </div>
        <button
            type="button"
            class="btn-secondary h-9 w-9 !p-0"
            :disabled="!hasNext || disabled"
            @click="go(currentPage + 1)"
        >
            <ChevronRightIcon class="h-4 w-4" />
        </button>
    </nav>
</template>
