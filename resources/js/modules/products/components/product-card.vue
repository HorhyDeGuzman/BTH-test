<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import type { Product } from '../models';
import { formatPrice } from '../helpers/format-price';

defineProps<{
    product: Product;
}>();
</script>

<template>
    <Link
        :href="`/product/${product.id}`"
        class="group flex h-full flex-col justify-between rounded-lg border border-gray-200 bg-white p-4 shadow-sm transition hover:border-indigo-400 hover:shadow-md"
    >
        <div>
            <h3
                class="text-base font-semibold text-gray-900 group-hover:text-indigo-600 line-clamp-2"
            >
                {{ product.name }}
            </h3>
            <p v-if="product.category" class="mt-1 text-xs uppercase tracking-wide text-gray-500">
                {{ product.category.name }}
            </p>
            <p v-if="product.description" class="mt-3 text-sm text-gray-600 line-clamp-3">
                {{ product.description }}
            </p>
        </div>
        <div class="mt-4 text-lg font-semibold text-indigo-600">
            {{ formatPrice(product.price) }}
        </div>
    </Link>
</template>
