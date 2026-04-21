<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, onMounted } from 'vue';
import { ArrowLeftIcon, ExclamationCircleIcon } from '@heroicons/vue/20/solid';
import { useI18n } from 'vue-i18n';
import PublicLayout from '@/common/layouts/public-layout.vue';
import { useProductsApi } from '@/modules/products';
import { formatPrice } from '@/modules/products/helpers/format-price';

const props = defineProps<{
    id: number;
}>();

const { t, locale } = useI18n();
const { current: product, loading, error, fetchOne } = useProductsApi();

function goBack(): void {
    if (typeof window !== 'undefined' && window.history.length > 1) {
        window.history.back();
    } else {
        router.visit('/');
    }
}

const gradient = computed(() => {
    const palettes = [
        'from-indigo-500 via-violet-500 to-fuchsia-500',
        'from-sky-500 via-cyan-500 to-teal-500',
        'from-amber-500 via-orange-500 to-rose-500',
        'from-emerald-500 via-teal-500 to-cyan-500',
        'from-rose-500 via-pink-500 to-fuchsia-500',
        'from-violet-500 via-purple-500 to-indigo-500',
    ];
    return palettes[Number(props.id) % palettes.length];
});

const initial = computed(() => product.value?.name.charAt(0).toUpperCase() ?? '?');

const createdDate = computed(() => {
    if (!product.value?.created_at) return null;
    return new Date(product.value.created_at).toLocaleDateString(
        locale.value === 'ru' ? 'ru-RU' : 'en-US',
        {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
        },
    );
});

const pageTitle = computed(() => product.value?.name ?? t('public.detail.page_title_fallback'));

onMounted(() => {
    fetchOne(Number(props.id)).catch(() => {
        // state reflected via `error`
    });
});
</script>

<template>
    <Head :title="pageTitle" />

    <PublicLayout>
        <div class="container py-10">
            <nav class="mb-8 flex items-center gap-2 text-sm text-slate-500">
                <Link href="/" class="transition hover:text-slate-900">
                    {{ t('public.detail.breadcrumb_catalog') }}
                </Link>
                <span class="text-slate-300">/</span>
                <span class="truncate text-slate-900">
                    {{ product?.name ?? t('public.detail.product_fallback') }}
                </span>
            </nav>

            <div
                v-if="loading && !product"
                class="grid gap-10 animate-fade-in lg:grid-cols-5"
            >
                <div class="skeleton aspect-square rounded-2xl lg:col-span-2"></div>
                <div class="flex flex-col gap-4 lg:col-span-3">
                    <div class="skeleton h-4 w-24"></div>
                    <div class="skeleton h-10 w-3/4"></div>
                    <div class="skeleton h-8 w-28"></div>
                    <div class="skeleton h-24 w-full"></div>
                </div>
            </div>

            <div
                v-else-if="error"
                class="mx-auto max-w-xl rounded-2xl border border-rose-200 bg-rose-50/70 p-8 text-center"
            >
                <div
                    class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-rose-100 text-rose-600"
                >
                    <ExclamationCircleIcon class="h-6 w-6" />
                </div>
                <h2 class="mt-4 text-lg font-semibold text-rose-900">
                    {{ error }}
                </h2>
                <p class="mt-1 text-sm text-rose-700">
                    {{ t('public.detail.error_hint') }}
                </p>
                <button type="button" class="btn-secondary mt-6" @click="goBack">
                    <ArrowLeftIcon class="h-4 w-4" />
                    {{ t('public.detail.go_back') }}
                </button>
            </div>

            <article
                v-else-if="product"
                class="grid gap-10 animate-fade-in lg:grid-cols-5"
            >
                <div class="lg:col-span-2">
                    <div
                        :class="[
                            'relative flex aspect-square items-center justify-center overflow-hidden rounded-2xl bg-gradient-to-br shadow-lift',
                            gradient,
                        ]"
                    >
                        <div
                            class="absolute inset-0 bg-[radial-gradient(circle_at_30%_20%,rgba(255,255,255,0.35),transparent_55%)]"
                        />
                        <span
                            class="font-display text-[10rem] font-bold leading-none tracking-tightest text-white/90 drop-shadow"
                        >
                            {{ initial }}
                        </span>
                    </div>
                </div>

                <div class="flex flex-col gap-6 lg:col-span-3">
                    <div>
                        <span v-if="product.category" class="chip-brand">
                            {{ product.category.name }}
                        </span>
                        <h1
                            class="mt-4 font-display text-3xl font-bold tracking-tightest text-slate-900 sm:text-4xl"
                        >
                            {{ product.name }}
                        </h1>
                    </div>

                    <div class="flex items-baseline gap-3">
                        <span
                            class="font-display text-4xl font-bold tracking-tight text-slate-900"
                        >
                            {{ formatPrice(product.price) }}
                        </span>
                        <span class="text-sm text-slate-400">
                            {{ t('public.detail.incl_taxes') }}
                        </span>
                    </div>

                    <div class="card p-6">
                        <h2 class="text-xs font-semibold uppercase tracking-wider text-slate-500">
                            {{ t('public.detail.description') }}
                        </h2>
                        <p
                            v-if="product.description"
                            class="mt-3 whitespace-pre-line text-base leading-relaxed text-slate-700"
                        >
                            {{ product.description }}
                        </p>
                        <p v-else class="mt-3 italic text-slate-400">
                            {{ t('public.detail.no_description') }}
                        </p>
                    </div>

                    <dl class="grid grid-cols-2 gap-4 text-sm">
                        <div class="card p-4">
                            <dt class="text-xs font-medium uppercase tracking-wider text-slate-500">
                                {{ t('public.detail.product_id') }}
                            </dt>
                            <dd class="mt-1 font-mono text-slate-900">#{{ product.id }}</dd>
                        </div>
                        <div v-if="createdDate" class="card p-4">
                            <dt class="text-xs font-medium uppercase tracking-wider text-slate-500">
                                {{ t('public.detail.added') }}
                            </dt>
                            <dd class="mt-1 text-slate-900">{{ createdDate }}</dd>
                        </div>
                    </dl>

                    <button type="button" class="btn-ghost self-start" @click="goBack">
                        <ArrowLeftIcon class="h-4 w-4" />
                        {{ t('public.detail.go_back') }}
                    </button>
                </div>
            </article>
        </div>
    </PublicLayout>
</template>
