<script setup lang="ts">
// Dumb skeleton that mirrors product-card dimensions so the grid layout
// doesn't jump when real cards appear.
</script>

<template>
    <div
        class="flex h-full flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white"
    >
        <div class="skeleton aspect-[4/3] rounded-none"></div>
        <div class="flex flex-1 flex-col gap-3 p-4">
            <div class="skeleton h-4 w-3/4"></div>
            <div class="skeleton h-3 w-full"></div>
            <div class="skeleton h-3 w-5/6"></div>
            <div class="mt-auto flex items-end justify-between pt-2">
                <div class="skeleton h-6 w-20"></div>
                <div class="skeleton h-3 w-10"></div>
            </div>
        </div>
    </div>
</template>
