<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ArrowRightIcon } from '@heroicons/vue/20/solid';
import { useI18n } from 'vue-i18n';
import LocaleSwitcher from '@/common/components/locale-switcher.vue';

const { t } = useI18n();
const year = new Date().getFullYear();
</script>

<template>
    <div class="min-h-screen bg-slate-50 font-sans text-slate-900 antialiased">
        <header
            class="sticky top-0 z-40 border-b border-slate-200/70 bg-white/75 backdrop-blur-xl"
        >
            <div class="container flex h-16 items-center justify-between">
                <Link href="/" class="group flex items-center gap-2.5">
                    <span
                        class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-gradient text-sm font-bold text-white shadow-soft"
                    >
                        B
                    </span>
                    <div class="flex items-baseline gap-1.5">
                        <span class="font-semibold tracking-tight text-slate-900">
                            {{ t('public.layout.brand') }}
                        </span>
                        <span class="text-sm text-slate-500">
                            {{ t('public.layout.catalog') }}
                        </span>
                    </div>
                </Link>

                <nav class="flex items-center gap-2">
                    <LocaleSwitcher />
                    <Link href="/login" class="btn-ghost">
                        <span>{{ t('public.layout.admin') }}</span>
                        <ArrowRightIcon class="h-4 w-4" />
                    </Link>
                </nav>
            </div>
        </header>

        <main class="animate-fade-in">
            <slot />
        </main>

        <footer class="border-t border-slate-200 py-10">
            <div
                class="container flex flex-col items-center justify-between gap-3 text-sm text-slate-500 sm:flex-row"
            >
                <p>{{ t('public.layout.copyright', { year }) }}</p>
                <p class="text-xs">{{ t('public.layout.built_with') }}</p>
            </div>
        </footer>
    </div>
</template>
