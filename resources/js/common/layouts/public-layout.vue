<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen bg-gray-50 text-gray-900">
        <header class="border-b border-gray-200 bg-white">
            <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-4">
                <Link
                    href="/"
                    class="text-xl font-semibold text-gray-900 hover:text-indigo-600"
                >
                    BTH Catalog
                </Link>
                <nav class="flex items-center gap-4 text-sm">
                    <Link
                        href="/login"
                        class="rounded-md px-3 py-1.5 text-gray-600 hover:text-indigo-600"
                    >
                        Admin login
                    </Link>
                </nav>
            </div>
        </header>

        <main class="mx-auto max-w-6xl px-4 py-8">
            <slot />
        </main>

        <footer class="border-t border-gray-200 bg-white py-6 text-center text-sm text-gray-500">
            &copy; {{ new Date().getFullYear() }} BTH Catalog
        </footer>
    </div>
</template>
