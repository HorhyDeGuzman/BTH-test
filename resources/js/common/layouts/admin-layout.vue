<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import { ArrowRightOnRectangleIcon, CubeIcon } from '@heroicons/vue/24/outline';
import { useI18n } from 'vue-i18n';
import LocaleSwitcher from '@/common/components/locale-switcher.vue';
import { useAuth } from '@/modules/auth';

const { t } = useI18n();
const { user, isAuthenticated, logout } = useAuth();

// Captured synchronously: if there's no token at mount time we don't even
// render the admin chrome, we show a neutral loader while bouncing to /login.
const wasAuthenticatedOnMount = isAuthenticated.value;

onMounted(() => {
    if (!wasAuthenticatedOnMount) {
        router.visit('/login', { replace: true });
    }
});

async function handleLogout(): Promise<void> {
    await logout();
    router.visit('/login');
}

const initials = (name?: string) =>
    (name ?? '?')
        .split(' ')
        .map((p) => p[0]?.toUpperCase())
        .slice(0, 2)
        .join('');
</script>

<template>
    <!-- Silent loader while we bounce an unauthenticated visitor to /login -->
    <div
        v-if="!wasAuthenticatedOnMount"
        class="flex min-h-screen items-center justify-center bg-slate-50 text-slate-400"
    >
        <svg class="h-6 w-6 animate-spin" viewBox="0 0 24 24" fill="none">
            <circle
                class="opacity-25"
                cx="12"
                cy="12"
                r="10"
                stroke="currentColor"
                stroke-width="3"
            />
            <path
                class="opacity-75"
                fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.4 0 0 5.4 0 12h4z"
            />
        </svg>
    </div>

    <div
        v-else
        class="min-h-screen bg-slate-50 font-sans text-slate-900 antialiased"
    >
        <header
            class="sticky top-0 z-40 border-b border-slate-200/70 bg-white/80 backdrop-blur-xl"
        >
            <div class="container flex h-16 items-center justify-between">
                <div class="flex items-center gap-6">
                    <Link href="/admin/products" class="flex items-center gap-2.5">
                        <span
                            class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-900 text-sm font-bold text-white shadow-soft"
                        >
                            B
                        </span>
                        <div class="flex items-baseline gap-1.5">
                            <span class="font-semibold tracking-tight text-slate-900">
                                {{ t('public.layout.brand') }}
                            </span>
                            <span
                                class="rounded bg-slate-100 px-1.5 py-0.5 text-[10px] font-semibold uppercase tracking-wider text-slate-500"
                            >
                                {{ t('admin.layout.admin_pill') }}
                            </span>
                        </div>
                    </Link>

                    <nav class="hidden items-center gap-1 sm:flex">
                        <Link
                            href="/admin/products"
                            class="flex items-center gap-2 rounded-lg px-3 py-1.5 text-sm font-medium text-slate-700 transition hover:bg-slate-100"
                        >
                            <CubeIcon class="h-4 w-4 text-slate-500" />
                            {{ t('admin.layout.products') }}
                        </Link>
                    </nav>
                </div>

                <div class="flex items-center gap-3">
                    <LocaleSwitcher />
                    <Link
                        href="/"
                        class="hidden text-xs font-medium text-slate-500 transition hover:text-slate-900 sm:inline"
                    >
                        {{ t('admin.layout.view_public') }}
                    </Link>
                    <div
                        class="hidden items-center gap-2 rounded-full bg-slate-100 py-1 pl-1 pr-3 sm:flex"
                    >
                        <span
                            class="flex h-7 w-7 items-center justify-center rounded-full bg-slate-900 text-[11px] font-semibold text-white"
                        >
                            {{ initials(user?.name) }}
                        </span>
                        <span class="text-xs font-medium text-slate-700">
                            {{ user?.email ?? '' }}
                        </span>
                    </div>
                    <button type="button" class="btn-secondary" @click="handleLogout">
                        <ArrowRightOnRectangleIcon class="h-4 w-4" />
                        <span class="hidden sm:inline">{{ t('admin.layout.log_out') }}</span>
                    </button>
                </div>
            </div>
        </header>

        <main class="container animate-fade-in py-10">
            <slot />
        </main>
    </div>
</template>
