<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import { useAuth } from '@/modules/auth';

const { user, isAuthenticated, logout } = useAuth();

// Client-side auth guard: if there's no token in localStorage, kick the
// visitor back to the public login screen. A real app would pair this with
// a server check on admin pages, but per the task spec the source of truth
// for "is authenticated" is the localStorage token.
onMounted(() => {
    if (!isAuthenticated.value) {
        router.visit('/login', { replace: true });
    }
});

async function handleLogout(): Promise<void> {
    await logout();
    router.visit('/login');
}
</script>

<template>
    <div v-if="isAuthenticated" class="min-h-screen bg-gray-50 text-gray-900">
        <header class="border-b border-gray-200 bg-white">
            <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-4">
                <Link
                    href="/admin/products"
                    class="text-xl font-semibold text-gray-900 hover:text-indigo-600"
                >
                    BTH Admin
                </Link>
                <nav class="flex items-center gap-4 text-sm">
                    <Link
                        href="/admin/products"
                        class="rounded-md px-3 py-1.5 text-gray-600 hover:text-indigo-600"
                    >
                        Manage products
                    </Link>
                    <span v-if="user" class="text-xs text-gray-500">
                        {{ user.email }}
                    </span>
                    <button
                        type="button"
                        class="rounded-md border border-gray-300 bg-white px-3 py-1.5 text-gray-700 hover:bg-gray-50"
                        @click="handleLogout"
                    >
                        Log out
                    </button>
                </nav>
            </div>
        </header>

        <main class="mx-auto max-w-6xl px-4 py-8">
            <slot />
        </main>
    </div>
</template>
