<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { computed, onMounted, ref, watch } from 'vue';
import { MagnifyingGlassIcon } from '@heroicons/vue/20/solid';
import { useI18n } from 'vue-i18n';
import PublicLayout from '@/common/layouts/public-layout.vue';
import Pagination from '@/common/components/pagination.vue';
import { useDebouncedRef } from '@/common/composables';
import { useProductsApi } from '@/modules/products';
import { useCategories } from '@/modules/categories';
import ProductCard from '@/modules/products/components/product-card.vue';
import ProductCardSkeleton from '@/modules/products/components/product-card-skeleton.vue';
import CategoryFilter from '@/modules/products/components/category-filter.vue';

const { t } = useI18n();

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
const searchInput = ref('');
const debouncedSearch = useDebouncedRef('', 400);

const hasActiveFilters = computed(
    () => selectedCategoryId.value !== null || debouncedSearch.value !== '',
);

const isInitialLoad = computed(
    () => productsLoading.value && products.value.length === 0 && meta.value === null,
);

const pageTitle = computed(() => t('public.home.page_title'));

watch(searchInput, (value) => {
    debouncedSearch.value = value;
});

onMounted(() => {
    fetchCategories();
    fetchList({ page: currentPage.value });
});

watch([selectedCategoryId, currentPage, debouncedSearch], ([categoryId, page, search]) => {
    fetchList({ category_id: categoryId, page, search });
});

watch([selectedCategoryId, debouncedSearch], () => {
    if (currentPage.value !== 1) currentPage.value = 1;
});

function clearFilters() {
    selectedCategoryId.value = null;
    searchInput.value = '';
}
</script>

<template>
    <Head :title="pageTitle" />

    <PublicLayout>
        <!-- Hero -->
        <section class="relative overflow-hidden border-b border-slate-200 bg-white">
            <div
                class="absolute inset-0 bg-grid-slate [background-size:24px_24px] [mask-image:radial-gradient(ellipse_at_top,#000_30%,transparent_75%)] opacity-60"
            />
            <div
                class="absolute -top-24 left-1/2 h-72 w-[56rem] -translate-x-1/2 rounded-full bg-brand-gradient opacity-10 blur-3xl"
            />
            <div class="container relative py-16 sm:py-24">
                <div class="mx-auto max-w-2xl text-center">
                    <span class="chip-brand">{{ t('public.home.kicker') }}</span>
                    <h1
                        class="mt-4 font-display text-4xl font-bold tracking-tightest text-slate-900 sm:text-6xl"
                    >
                        {{ t('public.home.title_part_1') }}
                        <span class="bg-brand-gradient bg-clip-text text-transparent">
                            {{ t('public.home.title_highlight') }}
                        </span>
                    </h1>
                    <p class="mt-4 text-base leading-relaxed text-slate-600 sm:text-lg">
                        {{ t('public.home.subtitle') }}
                    </p>
                </div>
            </div>
        </section>

        <!-- Search + filter bar -->
        <section class="sticky top-16 z-30 border-b border-slate-200 bg-slate-50/80 backdrop-blur">
            <div class="container flex flex-col gap-3 py-4 sm:flex-row sm:items-center">
                <div class="relative flex-1">
                    <MagnifyingGlassIcon
                        class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"
                    />
                    <input
                        v-model="searchInput"
                        type="search"
                        :placeholder="t('public.home.search_placeholder')"
                        class="field pl-10"
                    />
                </div>
                <CategoryFilter
                    v-model="selectedCategoryId"
                    :categories="categories"
                    :disabled="productsLoading"
                />
                <button
                    v-if="hasActiveFilters"
                    type="button"
                    class="btn-ghost"
                    @click="clearFilters"
                >
                    {{ t('public.home.reset') }}
                </button>
            </div>
        </section>

        <!-- Content -->
        <section class="container py-10">
            <div
                v-if="productsError"
                class="mb-6 rounded-xl border border-rose-200 bg-rose-50/70 p-4 text-sm text-rose-700"
            >
                {{ productsError }}
            </div>

            <div
                v-if="isInitialLoad"
                class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
            >
                <ProductCardSkeleton v-for="i in 8" :key="i" />
            </div>

            <div
                v-else-if="products.length === 0"
                class="rounded-2xl border border-dashed border-slate-300 bg-white/60 px-6 py-20 text-center"
            >
                <div
                    class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100 text-slate-400"
                >
                    <MagnifyingGlassIcon class="h-6 w-6" />
                </div>
                <h3 class="mt-4 text-base font-semibold text-slate-900">
                    {{ t('public.home.empty_title') }}
                </h3>
                <p class="mt-1 text-sm text-slate-500">
                    {{ t('public.home.empty_description') }}
                </p>
                <button
                    v-if="hasActiveFilters"
                    type="button"
                    class="btn-secondary mt-6"
                    @click="clearFilters"
                >
                    {{ t('public.home.empty_action') }}
                </button>
            </div>

            <div
                v-else
                :class="[
                    'grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 transition-opacity',
                    productsLoading ? 'opacity-60' : 'opacity-100',
                ]"
            >
                <ProductCard v-for="product in products" :key="product.id" :product="product" />
            </div>

            <div v-if="meta && meta.total > 0" class="mt-10 flex flex-col items-center gap-3">
                <p class="text-xs text-slate-500">
                    {{
                        t('public.home.showing', {
                            from: meta.from ?? 0,
                            to: meta.to ?? 0,
                            total: meta.total,
                        })
                    }}
                </p>
                <Pagination
                    v-model:current-page="currentPage"
                    :last-page="meta.last_page"
                    :disabled="productsLoading"
                />
            </div>
        </section>
    </PublicLayout>
</template>
