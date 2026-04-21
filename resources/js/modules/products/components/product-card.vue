<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import type { Product } from '../models';
import { formatPrice } from '../helpers/format-price';

const props = defineProps<{
    product: Product;
}>();

const { t } = useI18n();

const gradient = computed(() => {
    const palettes = [
        'from-indigo-500/90 via-violet-500/80 to-fuchsia-500/70',
        'from-sky-500/90 via-cyan-500/80 to-teal-500/70',
        'from-amber-500/90 via-orange-500/80 to-rose-500/70',
        'from-emerald-500/90 via-teal-500/80 to-cyan-500/70',
        'from-rose-500/90 via-pink-500/80 to-fuchsia-500/70',
        'from-violet-500/90 via-purple-500/80 to-indigo-500/70',
    ];
    return palettes[props.product.id % palettes.length];
});

const initial = computed(() => props.product.name.charAt(0).toUpperCase());
</script>

<template>
    <Link
        :href="`/product/${product.id}`"
        class="group relative flex h-full flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-soft transition-colors duration-150 hover:border-slate-300 hover:shadow-lift"
    >
        <div
            :class="[
                'relative flex aspect-[4/3] items-center justify-center bg-gradient-to-br overflow-hidden',
                gradient,
            ]"
        >
            <div
                class="absolute inset-0 bg-[radial-gradient(circle_at_30%_20%,rgba(255,255,255,0.35),transparent_55%)]"
            />
            <span
                class="font-display text-6xl font-bold tracking-tightest text-white/90 drop-shadow-sm"
            >
                {{ initial }}
            </span>
            <span
                v-if="product.category"
                class="absolute left-3 top-3 rounded-full bg-white/90 px-2.5 py-0.5 text-[10px] font-semibold uppercase tracking-wider text-slate-700 backdrop-blur"
            >
                {{ product.category.name }}
            </span>
        </div>

        <div class="flex flex-1 flex-col gap-2 p-4">
            <h3
                class="line-clamp-2 text-base font-semibold text-slate-900 transition group-hover:text-brand-700"
            >
                {{ product.name }}
            </h3>
            <p v-if="product.description" class="line-clamp-2 text-sm text-slate-500">
                {{ product.description }}
            </p>
            <div class="mt-auto flex items-end justify-between pt-2">
                <span class="font-display text-xl font-bold tracking-tight text-slate-900">
                    {{ formatPrice(product.price) }}
                </span>
                <span
                    class="text-xs font-medium text-slate-400 transition group-hover:text-brand-500"
                >
                    {{ t('public.card.view') }}
                </span>
            </div>
        </div>
    </Link>
</template>
