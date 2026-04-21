<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import PublicLayout from '@/common/layouts/public-layout.vue';
import { useProductsApi } from '@/modules/products';
import { formatPrice } from '@/modules/products/helpers/format-price';

const props = defineProps<{
    id: number;
}>();

const { current: product, loading, error, fetchOne } = useProductsApi();

onMounted(() => {
    fetchOne(Number(props.id)).catch(() => {
        // Error state is already reflected via `error`.
    });
});
</script>

<template>
    <Head :title="product?.name ?? 'Product'" />

    <PublicLayout>
        <Link
            href="/"
            class="mb-6 inline-flex items-center gap-1 text-sm text-gray-500 hover:text-indigo-600"
        >
            &larr; Back to catalog
        </Link>

        <div v-if="loading && !product" class="py-12 text-center text-gray-500">
            Loading product…
        </div>

        <div
            v-else-if="error"
            class="rounded-md border border-red-200 bg-red-50 p-6 text-center text-red-700"
        >
            <p class="font-medium">{{ error }}</p>
            <p class="mt-2 text-sm">
                The product may have been removed or the link is invalid.
            </p>
        </div>

        <article
            v-else-if="product"
            class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm"
        >
            <p
                v-if="product.category"
                class="text-xs uppercase tracking-wide text-indigo-600"
            >
                {{ product.category.name }}
            </p>
            <h1 class="mt-2 text-3xl font-bold text-gray-900">
                {{ product.name }}
            </h1>
            <p class="mt-4 text-2xl font-semibold text-indigo-600">
                {{ formatPrice(product.price) }}
            </p>
            <p v-if="product.description" class="mt-6 whitespace-pre-line text-gray-700">
                {{ product.description }}
            </p>
            <p v-else class="mt-6 italic text-gray-400">No description provided.</p>
        </article>
    </PublicLayout>
</template>
