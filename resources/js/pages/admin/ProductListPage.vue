<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { onMounted, ref, watch } from 'vue';
import AdminLayout from '@/common/layouts/admin-layout.vue';
import Pagination from '@/common/components/pagination.vue';
import { useProductsApi } from '@/modules/products';
import type { Product } from '@/modules/products';
import DeleteProductModal from '@/modules/products/components/delete-product-modal.vue';
import { formatPrice } from '@/modules/products/helpers/format-price';

const {
    items: products,
    meta,
    loading,
    error,
    fetchList,
    remove,
} = useProductsApi();

const currentPage = ref(1);
const productToDelete = ref<Product | null>(null);
const deleting = ref(false);

onMounted(() => fetchList({ page: currentPage.value }));

watch(currentPage, (page) => fetchList({ page }));

function askDelete(product: Product): void {
    productToDelete.value = product;
}

function closeDeleteModal(): void {
    if (deleting.value) return;
    productToDelete.value = null;
}

async function confirmDelete(): Promise<void> {
    if (!productToDelete.value) return;
    deleting.value = true;
    try {
        await remove(productToDelete.value.id);
        productToDelete.value = null;
        // Step back a page if we removed the last item on page > 1.
        if (products.value.length === 0 && currentPage.value > 1) {
            currentPage.value -= 1;
        } else {
            await fetchList({ page: currentPage.value });
        }
    } finally {
        deleting.value = false;
    }
}
</script>

<template>
    <Head title="Manage products" />

    <AdminLayout>
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900">Products</h1>
            <Link
                href="/admin/products/create"
                class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500"
            >
                Add product
            </Link>
        </div>

        <div
            v-if="error"
            class="mb-6 rounded-md border border-red-200 bg-red-50 p-4 text-sm text-red-700"
        >
            {{ error }}
        </div>

        <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                            Name
                        </th>
                        <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                            Category
                        </th>
                        <th class="px-4 py-2 text-right text-xs font-medium uppercase tracking-wide text-gray-500">
                            Price
                        </th>
                        <th class="px-4 py-2 text-right text-xs font-medium uppercase tracking-wide text-gray-500">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    <tr v-if="loading && products.length === 0">
                        <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500">
                            Loading…
                        </td>
                    </tr>
                    <tr v-else-if="products.length === 0">
                        <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500">
                            No products yet.
                        </td>
                    </tr>
                    <tr v-for="product in products" :key="product.id" class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm font-medium text-gray-900">
                            {{ product.name }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">
                            {{ product.category?.name ?? '—' }}
                        </td>
                        <td class="px-4 py-3 text-right text-sm text-gray-900">
                            {{ formatPrice(product.price) }}
                        </td>
                        <td class="px-4 py-3 text-right text-sm">
                            <Link
                                :href="`/admin/products/${product.id}/edit`"
                                class="mr-3 text-indigo-600 hover:text-indigo-500"
                            >
                                Edit
                            </Link>
                            <button
                                type="button"
                                class="text-red-600 hover:text-red-500"
                                @click="askDelete(product)"
                            >
                                Delete
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div v-if="meta && meta.last_page > 1" class="mt-6">
            <Pagination
                v-model:current-page="currentPage"
                :last-page="meta.last_page"
                :disabled="loading"
            />
        </div>

        <DeleteProductModal
            :show="productToDelete !== null"
            :product="productToDelete"
            :processing="deleting"
            @close="closeDeleteModal"
            @confirm="confirmDelete"
        />
    </AdminLayout>
</template>
