<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { onMounted, ref, watch } from 'vue';
import PublicLayout from '@/common/layouts/public-layout.vue';
import Pagination from '@/common/components/pagination.vue';
import { useProductsApi } from '@/modules/products';
import { useCategories } from '@/modules/categories';
import ProductCard from '@/modules/products/components/product-card.vue';
import CategoryFilter from '@/modules/products/components/category-filter.vue';

const {
    items: products,
    meta,
    loading: productsLoading,
    error: productsError,
    fetchList,
} = useProductsApi();

const { items: categories, fetchAll: fetchCategories } = useCategories();

const selectedCategoryId = ref<number | null>(null);
const currentPage = ref(1);

onMounted(() => {
    fetchCategories();
    fetchList({ page: currentPage.value });
});

// Refetch whenever the filter or page changes. The category filter and
// pagination are independent inputs; watching both in one watcher keeps the
// behaviour simple: any change re-issues a request with the latest state.
watch([selectedCategoryId, currentPage], ([categoryId, page]) => {
    fetchList({ category_id: categoryId, page });
});

// When user picks a new category, always reset to page 1 to avoid landing
// on an out-of-range page for the filtered subset.
watch(selectedCategoryId, () => {
    if (currentPage.value !== 1) currentPage.value = 1;
});
</script>

<template>
    <Head title="Products" />

    <PublicLayout>
        <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
            <h1 class="text-2xl font-bold text-gray-900">Product catalog</h1>
            <CategoryFilter
                v-model="selectedCategoryId"
                :categories="categories"
                :disabled="productsLoading"
            />
        </div>

        <div
            v-if="productsError"
            class="mb-6 rounded-md border border-red-200 bg-red-50 p-4 text-sm text-red-700"
        >
            {{ productsError }}
        </div>

        <div v-if="productsLoading && products.length === 0" class="py-12 text-center text-gray-500">
            Loading products…
        </div>

        <div
            v-else-if="products.length === 0"
            class="rounded-md border border-dashed border-gray-300 bg-white py-12 text-center text-gray-500"
        >
            No products match the current filter.
        </div>

        <div v-else class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            <ProductCard v-for="product in products" :key="product.id" :product="product" />
        </div>

        <div v-if="meta && meta.last_page > 1" class="mt-8">
            <Pagination
                v-model:current-page="currentPage"
                :last-page="meta.last_page"
                :disabled="productsLoading"
            />
        </div>
    </PublicLayout>
</template>
