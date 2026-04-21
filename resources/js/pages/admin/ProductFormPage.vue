<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, onMounted, reactive, ref } from 'vue';
import { ArrowLeftIcon, ExclamationCircleIcon } from '@heroicons/vue/20/solid';
import { useI18n } from 'vue-i18n';
import AdminLayout from '@/common/layouts/admin-layout.vue';
import { extractApiError, extractValidationErrors } from '@/common/helpers';
import { useCategories } from '@/modules/categories';
import { useProductsApi } from '@/modules/products';
import type { ProductPayload } from '@/modules/products';

const props = defineProps<{
    id?: number;
}>();

const { t } = useI18n();

const isEdit = computed(() => typeof props.id === 'number');
const kicker = computed(() =>
    isEdit.value ? t('admin.form.kicker_edit') : t('admin.form.kicker_new'),
);
const title = computed(() =>
    isEdit.value ? t('admin.form.title_edit') : t('admin.form.title_new'),
);
const subtitle = computed(() =>
    isEdit.value ? t('admin.form.subtitle_edit') : t('admin.form.subtitle_new'),
);

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
            generalError.value = extractApiError(err, t('admin.form.load_failed'));
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
            generalError.value = extractApiError(err, t('admin.form.save_failed'));
        }
    }
}
</script>

<template>
    <Head :title="title" />

    <AdminLayout>
        <Link
            href="/admin/products"
            class="mb-6 inline-flex items-center gap-1 text-sm text-slate-500 transition hover:text-slate-900"
        >
            <ArrowLeftIcon class="h-4 w-4" />
            {{ t('admin.form.back_to_products') }}
        </Link>

        <div class="mb-8 max-w-2xl">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">
                {{ kicker }}
            </p>
            <h1 class="mt-1 font-display text-3xl font-bold tracking-tight text-slate-900">
                {{ title }}
            </h1>
            <p class="mt-1 text-sm text-slate-500">{{ subtitle }}</p>
        </div>

        <div
            v-if="generalError"
            class="mb-6 flex max-w-2xl items-start gap-3 rounded-xl border border-rose-200 bg-rose-50/70 p-4 text-sm text-rose-700"
        >
            <ExclamationCircleIcon class="mt-0.5 h-5 w-5 flex-shrink-0" />
            <span>{{ generalError }}</span>
        </div>

        <div v-if="loadingExisting" class="max-w-2xl space-y-4">
            <div class="skeleton h-36 w-full"></div>
            <div class="skeleton h-24 w-full"></div>
            <div class="skeleton h-20 w-full"></div>
        </div>

        <form v-else class="max-w-2xl space-y-6" @submit.prevent="submit">
            <section class="card p-6">
                <header class="mb-5">
                    <h2 class="text-sm font-semibold text-slate-900">
                        {{ t('admin.form.section_basic_title') }}
                    </h2>
                    <p class="text-xs text-slate-500">
                        {{ t('admin.form.section_basic_subtitle') }}
                    </p>
                </header>

                <div class="space-y-5">
                    <div>
                        <label for="name" class="text-xs font-semibold text-slate-700">
                            {{ t('admin.form.field_name') }}
                        </label>
                        <input
                            id="name"
                            v-model="form.name"
                            type="text"
                            required
                            maxlength="255"
                            autofocus
                            :placeholder="t('admin.form.field_name_placeholder')"
                            class="field mt-1.5"
                        />
                        <p v-if="fieldErrors.name?.[0]" class="mt-1.5 text-xs text-rose-600">
                            {{ fieldErrors.name[0] }}
                        </p>
                    </div>

                    <div>
                        <label for="category_id" class="text-xs font-semibold text-slate-700">
                            {{ t('admin.form.field_category') }}
                        </label>
                        <div class="relative mt-1.5">
                            <select
                                id="category_id"
                                v-model.number="form.category_id"
                                required
                                class="field appearance-none pr-10"
                            >
                                <option :value="0" disabled>
                                    {{ t('admin.form.field_category_placeholder') }}
                                </option>
                                <option
                                    v-for="category in categories"
                                    :key="category.id"
                                    :value="category.id"
                                >
                                    {{ category.name }}
                                </option>
                            </select>
                            <svg
                                class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M10 14a1 1 0 0 1-.707-.293l-4-4a1 1 0 1 1 1.414-1.414L10 11.586l3.293-3.293a1 1 0 0 1 1.414 1.414l-4 4A1 1 0 0 1 10 14Z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </div>
                        <p
                            v-if="fieldErrors.category_id?.[0]"
                            class="mt-1.5 text-xs text-rose-600"
                        >
                            {{ fieldErrors.category_id[0] }}
                        </p>
                    </div>

                    <div>
                        <label for="description" class="text-xs font-semibold text-slate-700">
                            {{ t('admin.form.field_description') }}
                            <span class="font-normal text-slate-400">
                                {{ t('admin.form.field_description_optional') }}
                            </span>
                        </label>
                        <textarea
                            id="description"
                            v-model="form.description"
                            rows="5"
                            :placeholder="t('admin.form.field_description_placeholder')"
                            class="field mt-1.5 resize-y"
                        ></textarea>
                        <p
                            v-if="fieldErrors.description?.[0]"
                            class="mt-1.5 text-xs text-rose-600"
                        >
                            {{ fieldErrors.description[0] }}
                        </p>
                    </div>
                </div>
            </section>

            <section class="card p-6">
                <header class="mb-5">
                    <h2 class="text-sm font-semibold text-slate-900">
                        {{ t('admin.form.section_pricing_title') }}
                    </h2>
                    <p class="text-xs text-slate-500">
                        {{ t('admin.form.section_pricing_subtitle') }}
                    </p>
                </header>

                <div>
                    <label for="price" class="text-xs font-semibold text-slate-700">
                        {{ t('admin.form.field_price') }}
                    </label>
                    <div class="relative mt-1.5">
                        <span
                            class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-sm font-medium text-slate-400"
                        >
                            $
                        </span>
                        <input
                            id="price"
                            v-model.number="form.price"
                            type="number"
                            step="0.01"
                            min="0.01"
                            required
                            placeholder="0.00"
                            class="field pl-7 font-mono"
                        />
                    </div>
                    <p v-if="fieldErrors.price?.[0]" class="mt-1.5 text-xs text-rose-600">
                        {{ fieldErrors.price[0] }}
                    </p>
                </div>
            </section>

            <footer class="flex items-center justify-end gap-3">
                <Link href="/admin/products" class="btn-ghost">
                    {{ t('admin.form.cancel') }}
                </Link>
                <button type="submit" :disabled="saving" class="btn-primary">
                    <svg
                        v-if="saving"
                        class="h-4 w-4 animate-spin"
                        viewBox="0 0 24 24"
                        fill="none"
                    >
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
                    <span>{{
                        saving
                            ? t('admin.form.saving')
                            : isEdit
                              ? t('admin.form.save_changes')
                              : t('admin.form.create')
                    }}</span>
                </button>
            </footer>
        </form>
    </AdminLayout>
</template>
