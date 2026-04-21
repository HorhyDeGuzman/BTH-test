<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, onMounted, reactive, ref } from 'vue';
import AdminLayout from '@/common/layouts/admin-layout.vue';
import InputError from '@/common/components/input-error.vue';
import InputLabel from '@/common/components/input-label.vue';
import PrimaryButton from '@/common/components/primary-button.vue';
import SecondaryButton from '@/common/components/secondary-button.vue';
import TextInput from '@/common/components/text-input.vue';
import { extractApiError, extractValidationErrors } from '@/common/helpers';
import { useCategories } from '@/modules/categories';
import { useProductsApi } from '@/modules/products';
import type { ProductPayload } from '@/modules/products';

const props = defineProps<{
    id?: number;
}>();

const isEdit = computed(() => typeof props.id === 'number');
const title = computed(() => (isEdit.value ? 'Edit product' : 'Create product'));

const { items: categories, fetchAll: fetchCategories } = useCategories();
const { loading: saving, fetchOne, create, update } = useProductsApi();

const form = reactive<ProductPayload>({
    name: '',
    description: '',
    price: 0,
    category_id: 0,
});

const fieldErrors = ref<Record<string, string[]>>({});
const generalError = ref<string | null>(null);
const loadingExisting = ref(false);

onMounted(async () => {
    fetchCategories();

    if (isEdit.value && props.id) {
        loadingExisting.value = true;
        try {
            const product = await fetchOne(props.id);
            form.name = product.name;
            form.description = product.description ?? '';
            form.price = Number(product.price);
            form.category_id = product.category_id;
        } catch (err) {
            generalError.value = extractApiError(err, 'Failed to load product');
        } finally {
            loadingExisting.value = false;
        }
    }
});

async function submit(): Promise<void> {
    fieldErrors.value = {};
    generalError.value = null;

    const payload: ProductPayload = {
        name: form.name.trim(),
        description: form.description?.toString().trim() || null,
        price: Number(form.price),
        category_id: Number(form.category_id),
    };

    try {
        if (isEdit.value && props.id) {
            await update(props.id, payload);
        } else {
            await create(payload);
        }
        router.visit('/admin/products');
    } catch (err) {
        const validation = extractValidationErrors(err);
        if (Object.keys(validation).length > 0) {
            fieldErrors.value = validation;
        } else {
            generalError.value = extractApiError(err, 'Save failed');
        }
    }
}
</script>

<template>
    <Head :title="title" />

    <AdminLayout>
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900">{{ title }}</h1>
            <Link href="/admin/products" class="text-sm text-gray-500 hover:text-indigo-600">
                &larr; Back to list
            </Link>
        </div>

        <div
            v-if="generalError"
            class="mb-6 rounded-md border border-red-200 bg-red-50 p-4 text-sm text-red-700"
        >
            {{ generalError }}
        </div>

        <div v-if="loadingExisting" class="py-12 text-center text-gray-500">Loading…</div>

        <form
            v-else
            class="max-w-2xl rounded-lg border border-gray-200 bg-white p-6 shadow-sm"
            @submit.prevent="submit"
        >
            <div>
                <InputLabel for="name" value="Name" />
                <TextInput
                    id="name"
                    v-model="form.name"
                    type="text"
                    class="mt-1 block w-full"
                    required
                    maxlength="255"
                    autofocus
                />
                <InputError class="mt-2" :message="fieldErrors.name?.[0]" />
            </div>

            <div class="mt-4">
                <InputLabel for="category_id" value="Category" />
                <select
                    id="category_id"
                    v-model.number="form.category_id"
                    required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
                    <option :value="0" disabled>— Select a category —</option>
                    <option
                        v-for="category in categories"
                        :key="category.id"
                        :value="category.id"
                    >
                        {{ category.name }}
                    </option>
                </select>
                <InputError class="mt-2" :message="fieldErrors.category_id?.[0]" />
            </div>

            <div class="mt-4">
                <InputLabel for="description" value="Description" />
                <textarea
                    id="description"
                    v-model="form.description"
                    rows="5"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                ></textarea>
                <InputError class="mt-2" :message="fieldErrors.description?.[0]" />
            </div>

            <div class="mt-4">
                <InputLabel for="price" value="Price" />
                <input
                    id="price"
                    v-model.number="form.price"
                    type="number"
                    step="0.01"
                    min="0.01"
                    required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                />
                <InputError class="mt-2" :message="fieldErrors.price?.[0]" />
            </div>

            <div class="mt-6 flex items-center justify-end gap-3">
                <Link href="/admin/products">
                    <SecondaryButton type="button">Cancel</SecondaryButton>
                </Link>
                <PrimaryButton :class="{ 'opacity-25': saving }" :disabled="saving">
                    {{ saving ? 'Saving…' : isEdit ? 'Save changes' : 'Create product' }}
                </PrimaryButton>
            </div>
        </form>
    </AdminLayout>
</template>
