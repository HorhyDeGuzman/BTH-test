<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { onMounted, reactive, ref } from 'vue';
import GuestLayout from '@/common/layouts/guest-layout.vue';
import InputError from '@/common/components/input-error.vue';
import InputLabel from '@/common/components/input-label.vue';
import PrimaryButton from '@/common/components/primary-button.vue';
import TextInput from '@/common/components/text-input.vue';
import { extractApiError, extractValidationErrors } from '@/common/helpers';
import { useAuth } from '@/modules/auth';

const { isAuthenticated, login, loading } = useAuth();

const form = reactive({
    email: '',
    password: '',
});

const fieldErrors = ref<Record<string, string[]>>({});
const generalError = ref<string | null>(null);

// If a token is already stored, skip the form and go straight to admin.
onMounted(() => {
    if (isAuthenticated.value) {
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
            generalError.value = extractApiError(err, 'Login failed');
        }
        form.password = '';
    }
}
</script>

<template>
    <Head title="Log in" />

    <GuestLayout>
        <h1 class="mb-6 text-center text-xl font-semibold text-gray-900">Admin login</h1>

        <div
            v-if="generalError"
            class="mb-4 rounded-md border border-red-200 bg-red-50 p-3 text-sm text-red-700"
        >
            {{ generalError }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Email" />
                <TextInput
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="mt-1 block w-full"
                    required
                    autofocus
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="fieldErrors.email?.[0]" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Password" />
                <TextInput
                    id="password"
                    v-model="form.password"
                    type="password"
                    class="mt-1 block w-full"
                    required
                    autocomplete="current-password"
                />
                <InputError class="mt-2" :message="fieldErrors.password?.[0]" />
            </div>

            <div class="mt-6 flex items-center justify-end">
                <PrimaryButton
                    class="ms-4"
                    :class="{ 'opacity-25': loading }"
                    :disabled="loading"
                >
                    {{ loading ? 'Signing in…' : 'Log in' }}
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
