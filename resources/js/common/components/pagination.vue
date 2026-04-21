<script setup lang="ts">
import { computed } from 'vue';

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
            class="rounded border border-gray-300 bg-white px-3 py-1.5 text-gray-700 hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50"
            :disabled="!hasPrev || disabled"
            @click="go(currentPage - 1)"
        >
            Prev
        </button>
        <span class="px-2 text-gray-600">
            Page {{ currentPage }} of {{ lastPage }}
        </span>
        <button
            type="button"
            class="rounded border border-gray-300 bg-white px-3 py-1.5 text-gray-700 hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50"
            :disabled="!hasNext || disabled"
            @click="go(currentPage + 1)"
        >
            Next
        </button>
    </nav>
</template>
