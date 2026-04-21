<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, onMounted, reactive, ref } from 'vue';
import { ArrowLeftIcon, LockClosedIcon } from '@heroicons/vue/20/solid';
import { useI18n } from 'vue-i18n';
import LocaleSwitcher from '@/common/components/locale-switcher.vue';
import { extractApiError, extractValidationErrors } from '@/common/helpers';
import { useAuth } from '@/modules/auth';

const { t } = useI18n();
const { isAuthenticated, login, loading } = useAuth();

// Captured synchronously before the template renders, so authenticated
// visitors never see the login UI — we show a neutral loading screen
// while the redirect completes.
const wasAuthenticatedOnMount = isAuthenticated.value;

const form = reactive({
    email: '',
    password: '',
});

const fieldErrors = ref<Record<string, string[]>>({});
const generalError = ref<string | null>(null);

const pageTitle = computed(() => t('auth.login.page_title'));
const year = new Date().getFullYear();

onMounted(() => {
    if (wasAuthenticatedOnMount) {
        router.visit('/admin/products', { replace: true });
    }
});

async function submit(): Promise<void> {
    fieldErrors.value = {};
    generalError.value = null;

    try {
        await login({ email: form.email, password: form.password });
        router.visit('/admin/products');
    } catch (err) {
        const validation = extractValidationErrors(err);
        if (Object.keys(validation).length > 0) {
            fieldErrors.value = validation;
        } else {
            generalError.value = extractApiError(err, t('auth.login.login_failed'));
        }
        form.password = '';
    }
}
</script>

<template>
    <Head :title="pageTitle" />

    <!-- Silent loading screen while we redirect an already-authenticated admin -->
    <div
        v-if="wasAuthenticatedOnMount"
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

    <div v-else class="min-h-screen bg-slate-50 font-sans text-slate-900 antialiased">
        <div class="grid min-h-screen lg:grid-cols-2">
            <aside
                class="relative hidden overflow-hidden bg-slate-950 text-white lg:flex lg:flex-col lg:justify-between lg:p-12"
            >
                <div
                    class="absolute inset-0 bg-brand-gradient opacity-30 [mask-image:radial-gradient(ellipse_at_top_left,#000_40%,transparent_75%)]"
                />
                <div class="absolute inset-0 bg-grid-slate opacity-5 [background-size:32px_32px]" />

                <div class="relative flex items-center gap-3">
                    <span
                        class="flex h-9 w-9 items-center justify-center rounded-lg bg-brand-gradient text-sm font-bold shadow-soft"
                    >
                        B
                    </span>
                    <div class="flex items-baseline gap-1.5">
                        <span class="font-semibold tracking-tight">
                            {{ t('public.layout.brand') }}
                        </span>
                        <span class="text-sm text-white/60">
                            {{ t('auth.login.brand_admin') }}
                        </span>
                    </div>
                </div>

                <div class="relative">
                    <h1
                        class="font-display text-4xl font-bold leading-tight tracking-tightest text-white xl:text-5xl"
                    >
                        {{ t('auth.login.left_title_1') }}
                        <span class="bg-brand-gradient bg-clip-text text-transparent">
                            {{ t('auth.login.left_title_highlight') }}
                        </span>
                        {{ t('auth.login.left_title_2') }}
                    </h1>
                    <p class="mt-6 max-w-md text-base leading-relaxed text-white/70">
                        {{ t('auth.login.left_subtitle') }}
                    </p>

                    <ul class="mt-10 space-y-3 text-sm text-white/70">
                        <li class="flex items-center gap-3">
                            <span class="h-1.5 w-1.5 rounded-full bg-brand-400"></span>
                            {{ t('auth.login.feature_1') }}
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="h-1.5 w-1.5 rounded-full bg-brand-400"></span>
                            {{ t('auth.login.feature_2') }}
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="h-1.5 w-1.5 rounded-full bg-brand-400"></span>
                            {{ t('auth.login.feature_3') }}
                        </li>
                    </ul>
                </div>

                <p class="relative text-xs text-white/40">
                    {{ t('public.layout.copyright', { year }) }}
                </p>
            </aside>

            <main class="flex flex-col px-6 py-12 sm:px-12 lg:px-16">
                <div class="flex justify-end">
                    <LocaleSwitcher />
                </div>
                <div class="flex flex-1 items-center justify-center">
                    <div class="w-full max-w-sm animate-fade-in">
                        <Link
                            href="/"
                            class="mb-10 flex w-fit items-center gap-1 text-sm text-slate-500 transition hover:text-slate-900"
                        >
                            <ArrowLeftIcon class="h-4 w-4" />
                            {{ t('auth.login.back_to_catalog') }}
                        </Link>

                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-900 text-white"
                        >
                            <LockClosedIcon class="h-5 w-5" />
                        </div>
                        <h2 class="mt-4 font-display text-2xl font-bold tracking-tight text-slate-900">
                            {{ t('auth.login.welcome') }}
                        </h2>
                        <p class="mt-1 text-sm text-slate-500">
                            {{ t('auth.login.welcome_subtitle') }}
                        </p>

                        <div
                            v-if="generalError"
                            class="mt-6 rounded-lg border border-rose-200 bg-rose-50 p-3 text-sm text-rose-700"
                        >
                            {{ generalError }}
                        </div>

                        <form class="mt-6 space-y-4" @submit.prevent="submit">
                            <div>
                                <label for="email" class="text-xs font-semibold text-slate-700">
                                    {{ t('auth.login.email') }}
                                </label>
                                <input
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    required
                                    autofocus
                                    autocomplete="username"
                                    :placeholder="t('auth.login.email_placeholder')"
                                    class="field mt-1.5"
                                />
                                <p v-if="fieldErrors.email?.[0]" class="mt-1.5 text-xs text-rose-600">
                                    {{ fieldErrors.email[0] }}
                                </p>
                            </div>

                            <div>
                                <label for="password" class="text-xs font-semibold text-slate-700">
                                    {{ t('auth.login.password') }}
                                </label>
                                <input
                                    id="password"
                                    v-model="form.password"
                                    type="password"
                                    required
                                    autocomplete="current-password"
                                    :placeholder="t('auth.login.password_placeholder')"
                                    class="field mt-1.5"
                                />
                                <p
                                    v-if="fieldErrors.password?.[0]"
                                    class="mt-1.5 text-xs text-rose-600"
                                >
                                    {{ fieldErrors.password[0] }}
                                </p>
                            </div>

                            <button type="submit" :disabled="loading" class="btn-primary w-full">
                                <span v-if="loading" class="flex items-center gap-2">
                                    <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
                                        <circle
                                            class="opacity-25"
                                            cx="12"
                                            cy="12"
                                            r="10"
                                            stroke="currentColor"
                                            stroke-width="4"
                                        />
                                        <path
                                            class="opacity-75"
                                            fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.4 0 0 5.4 0 12h4zm2 5.3A8 8 0 014 12H0c0 3 1.1 5.8 3 7.9l3-2.6z"
                                        />
                                    </svg>
                                    {{ t('auth.login.signing_in') }}
                                </span>
                                <span v-else>{{ t('auth.login.sign_in') }}</span>
                            </button>
                        </form>

                        <div
                            class="mt-8 rounded-lg border border-slate-200 bg-slate-50 p-3 text-xs text-slate-500"
                        >
                            <span class="font-medium text-slate-700">
                                {{ t('auth.login.demo_credentials') }}
                            </span>
                            · <code class="font-mono text-slate-900">admin@admin.test</code>
                            / <code class="font-mono text-slate-900">password</code>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</template>
