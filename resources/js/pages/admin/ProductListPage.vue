<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { computed, onMounted, ref, watch } from 'vue';
import {
    CubeIcon,
    ExclamationCircleIcon,
    PencilSquareIcon,
    PlusIcon,
    TrashIcon,
} from '@heroicons/vue/24/outline';
import { useI18n } from 'vue-i18n';
import AdminLayout from '@/common/layouts/admin-layout.vue';
import Pagination from '@/common/components/pagination.vue';
import { pickLocalized } from '@/common/helpers';
import { useProductsApi } from '@/modules/products';
import type { Product } from '@/modules/products';
import DeleteProductModal from '@/modules/products/components/delete-product-modal.vue';
import { formatPrice } from '@/modules/products/helpers/format-price';

const { t, locale } = useI18n();

const {
    items: products,
    meta,
    loading,
    error,
    fetchList,
    remove,
} = useProductsApi();

function readInitialPage(): number {
    if (typeof window === 'undefined') return 1;
    const raw = Number(new URLSearchParams(window.location.search).get('page'));
    return Number.isFinite(raw) && raw > 0 ? raw : 1;
}

const currentPage = ref(readInitialPage());
const productToDelete = ref<Product | null>(null);
const deleting = ref(false);

const pageTitle = computed(() => t('admin.list.page_title'));

function productName(product: Product): string {
    return pickLocalized(product.name, product.name_en, locale.value);
}

function categoryName(product: Product): string {
    if (!product.category) return '—';
    return pickLocalized(product.category.name, product.category.name_en, locale.value);
}

function syncUrl() {
    if (typeof window === 'undefined') return;
    const url = new URL(window.location.href);
    if (currentPage.value > 1) url.searchParams.set('page', String(currentPage.value));
    else url.searchParams.delete('page');
    window.history.replaceState(window.history.state, '', url.toString());
}

onMounted(() => {
    syncUrl();
    fetchList({ page: currentPage.value });
});

watch(currentPage, (page) => {
    syncUrl();
    fetchList({ page });
});

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
    <Head :title="pageTitle" />

    <AdminLayout>
        <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">
                    {{ t('admin.list.kicker') }}
                </p>
                <h1 class="mt-1 font-display text-3xl font-bold tracking-tight text-slate-900">
                    {{ t('admin.list.title') }}
                </h1>
                <p v-if="meta" class="mt-1 text-sm text-slate-500">
                    {{ t('admin.list.total', { count: meta.total }) }}
                </p>
            </div>
            <Link href="/admin/products/create" class="btn-primary">
                <PlusIcon class="h-4 w-4" />
                {{ t('admin.list.add') }}
            </Link>
        </div>

        <div
            v-if="error"
            class="mb-6 flex items-start gap-3 rounded-xl border border-rose-200 bg-rose-50/70 p-4 text-sm text-rose-700"
        >
            <ExclamationCircleIcon class="mt-0.5 h-5 w-5 flex-shrink-0" />
            <span>{{ error }}</span>
        </div>

        <div class="card overflow-hidden">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50/70">
                    <tr>
                        <th
                            class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-500"
                        >
                            {{ t('admin.list.col_product') }}
                        </th>
                        <th
                            class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-500"
                        >
                            {{ t('admin.list.col_category') }}
                        </th>
                        <th
                            class="px-5 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-slate-500"
                        >
                            {{ t('admin.list.col_price') }}
                        </th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    <template v-if="loading && products.length === 0">
                        <tr v-for="i in 6" :key="`s-${i}`">
                            <td class="px-5 py-4">
                                <div class="skeleton h-4 w-40"></div>
                            </td>
                            <td class="px-5 py-4">
                                <div class="skeleton h-4 w-20"></div>
                            </td>
                            <td class="px-5 py-4">
                                <div class="skeleton ml-auto h-4 w-16"></div>
                            </td>
                            <td class="px-5 py-4">
                                <div class="skeleton ml-auto h-4 w-12"></div>
                            </td>
                        </tr>
                    </template>

                    <tr v-else-if="products.length === 0">
                        <td colspan="4" class="px-5 py-16">
                            <div class="mx-auto flex max-w-sm flex-col items-center text-center">
                                <div
                                    class="flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100 text-slate-400"
                                >
                                    <CubeIcon class="h-6 w-6" />
                                </div>
                                <h3 class="mt-4 text-base font-semibold text-slate-900">
                                    {{ t('admin.list.empty_title') }}
                                </h3>
                                <p class="mt-1 text-sm text-slate-500">
                                    {{ t('admin.list.empty_description') }}
                                </p>
                                <Link href="/admin/products/create" class="btn-primary mt-6">
                                    <PlusIcon class="h-4 w-4" />
                                    {{ t('admin.list.empty_action') }}
                                </Link>
                            </div>
                        </td>
                    </tr>

                    <tr
                        v-for="product in products"
                        :key="product.id"
                        class="group transition hover:bg-slate-50/60"
                    >
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-3">
                                <img
                                    v-if="product.image_url"
                                    :src="product.image_url"
                                    :alt="productName(product)"
                                    loading="lazy"
                                    class="h-10 w-10 flex-shrink-0 rounded-lg object-cover shadow-soft"
                                />
                                <span
                                    v-else
                                    class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-brand-gradient text-sm font-bold text-white shadow-soft"
                                >
                                    {{ productName(product).charAt(0).toUpperCase() }}
                                </span>
                                <div class="min-w-0">
                                    <Link
                                        :href="`/product/${product.id}`"
                                        class="block truncate text-sm font-semibold text-slate-900 transition group-hover:text-brand-700"
                                    >
                                        {{ productName(product) }}
                                    </Link>
                                    <p class="truncate text-xs text-slate-500">
                                        #{{ product.id }}
                                    </p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="chip">
                                {{ categoryName(product) }}
                            </span>
                        </td>
                        <td
                            class="px-5 py-3.5 text-right font-mono text-sm font-semibold text-slate-900"
                        >
                            {{ formatPrice(product.price) }}
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="flex justify-end gap-1">
                                <Link
                                    :href="`/admin/products/${product.id}/edit`"
                                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 transition hover:bg-slate-100 hover:text-slate-900"
                                    :title="t('admin.list.action_edit')"
                                >
                                    <PencilSquareIcon class="h-4 w-4" />
                                </Link>
                                <button
                                    type="button"
                                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 transition hover:bg-rose-50 hover:text-rose-600"
                                    :title="t('admin.list.action_delete')"
                                    @click="askDelete(product)"
                                >
                                    <TrashIcon class="h-4 w-4" />
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div v-if="meta && meta.last_page > 1" class="mt-8 flex justify-center">
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
