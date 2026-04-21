<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { useI18n } from 'vue-i18n';
import { pickLocalized } from '@/common/helpers';
import type { Product } from '../models';
import { formatPrice } from '../helpers/format-price';

const props = defineProps<{
    product: Product;
}>();

const { t, locale } = useI18n();

const name = computed(() => pickLocalized(props.product.name, props.product.name_en, locale.value));
const description = computed(() =>
    pickLocalized(props.product.description, props.product.description_en, locale.value),
);
const categoryName = computed(() =>
    props.product.category
        ? pickLocalized(
              props.product.category.name,
              props.product.category.name_en,
              locale.value,
          )
        : null,
);

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

const initial = computed(() => name.value.charAt(0).toUpperCase() || '?');

const imageFailed = ref(false);
const showImage = computed(() => !!props.product.image_url && !imageFailed.value);
</script>

<template>
    <Link
        :href="`/product/${product.id}`"
        class="group relative flex h-full flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-soft transition-colors duration-150 hover:border-slate-300 hover:shadow-lift dark:border-slate-800 dark:bg-slate-900 dark:shadow-none dark:hover:border-slate-700"
    >
        <div
            :class="[
                'relative aspect-[4/3] overflow-hidden',
                showImage
                    ? 'bg-slate-100 dark:bg-slate-800'
                    : `bg-gradient-to-br ${gradient}`,
            ]"
        >
            <img
                v-if="showImage"
                :src="product.image_url ?? ''"
                :alt="name"
                loading="lazy"
                class="h-full w-full object-cover"
                @error="imageFailed = true"
            />
            <template v-else>
                <div
                    class="absolute inset-0 bg-[radial-gradient(circle_at_30%_20%,rgba(255,255,255,0.35),transparent_55%)]"
                />
                <span
                    class="flex h-full w-full items-center justify-center font-display text-6xl font-bold tracking-tightest text-white/90 drop-shadow-sm"
                >
                    {{ initial }}
                </span>
            </template>
            <span
                v-if="categoryName"
                class="absolute left-3 top-3 rounded-full bg-white/90 px-2.5 py-0.5 text-[10px] font-semibold uppercase tracking-wider text-slate-700 backdrop-blur dark:bg-slate-900/80 dark:text-slate-200"
            >
                {{ categoryName }}
            </span>
        </div>

        <div class="flex flex-1 flex-col gap-2 p-4">
            <h3
                class="line-clamp-2 text-base font-semibold text-slate-900 transition group-hover:text-brand-700 dark:text-slate-100 dark:group-hover:text-brand-300"
            >
                {{ name }}
            </h3>
            <p
                v-if="description"
                class="line-clamp-2 text-sm text-slate-500 dark:text-slate-400"
            >
                {{ description }}
            </p>
            <div class="mt-auto flex items-end justify-between pt-2">
                <span
                    class="font-display text-xl font-bold tracking-tight text-slate-900 dark:text-slate-100"
                >
                    {{ formatPrice(product.price) }}
                </span>
                <span
                    class="text-xs font-medium text-slate-400 transition group-hover:text-brand-500 dark:text-slate-500 dark:group-hover:text-brand-400"
                >
                    {{ t('public.card.view') }}
                </span>
            </div>
        </div>
    </Link>
</template>
