<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ArrowRightIcon } from '@heroicons/vue/20/solid';
import { useI18n } from 'vue-i18n';
import LocaleSwitcher from '@/common/components/locale-switcher.vue';
import ThemeToggle from '@/common/components/theme-toggle.vue';
import { useAuth } from '@/modules/auth';

const { t } = useI18n();
const { isAuthenticated } = useAuth();

const year = new Date().getFullYear();
</script>

<template>
    <div
        class="min-h-screen bg-slate-50 font-sans text-slate-900 antialiased dark:bg-slate-950 dark:text-slate-100"
    >
        <header
            class="sticky top-0 z-40 border-b border-slate-200/70 bg-white/75 backdrop-blur-xl dark:border-slate-800/70 dark:bg-slate-950/75"
        >
            <div class="container flex h-16 items-center justify-between">
                <Link href="/" class="group flex items-center gap-2.5">
                    <span
                        class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-gradient text-sm font-bold text-white shadow-soft"
                    >
                        B
                    </span>
                    <div class="flex items-baseline gap-1.5">
                        <span class="font-semibold tracking-tight text-slate-900 dark:text-slate-100">
                            {{ t('public.layout.brand') }}
                        </span>
                        <span class="text-sm text-slate-500 dark:text-slate-400">
                            {{ t('public.layout.catalog') }}
                        </span>
                    </div>
                </Link>

                <nav class="flex items-center gap-2">
                    <ThemeToggle />
                    <LocaleSwitcher />
                    <Link
                        :href="isAuthenticated ? '/admin/products' : '/login'"
                        class="btn-ghost"
                    >
                        <span>
                            {{
                                isAuthenticated
                                    ? t('public.layout.admin_panel')
                                    : t('public.layout.admin')
                            }}
                        </span>
                        <ArrowRightIcon class="h-4 w-4" />
                    </Link>
                </nav>
            </div>
        </header>

        <main class="animate-fade-in">
            <slot />
        </main>

        <footer class="border-t border-slate-200 py-10 dark:border-slate-800">
            <div
                class="container flex flex-col items-center justify-between gap-3 text-sm text-slate-500 dark:text-slate-400 sm:flex-row"
            >
                <p>{{ t('public.layout.copyright', { year }) }}</p>
                <p class="text-xs">{{ t('public.layout.built_with') }}</p>
            </div>
        </footer>
    </div>
</template>
