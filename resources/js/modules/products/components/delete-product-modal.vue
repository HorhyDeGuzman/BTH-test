<script setup lang="ts">
import { computed } from 'vue';
import { ExclamationTriangleIcon } from '@heroicons/vue/24/outline';
import { useI18n } from 'vue-i18n';
import { pickLocalized } from '@/common/helpers';
import Modal from '@/common/components/modal.vue';
import type { Product } from '../models';

const props = defineProps<{
    show: boolean;
    product: Product | null;
    processing?: boolean;
}>();

const emit = defineEmits<{
    (e: 'close'): void;
    (e: 'confirm'): void;
}>();

const { t, locale } = useI18n();

const displayName = computed(() =>
    props.product
        ? pickLocalized(props.product.name, props.product.name_en, locale.value)
        : t('admin.delete_modal.product_fallback'),
);

const confirmMessage = computed(() =>
    t('admin.delete_modal.confirm', { name: displayName.value }),
);
</script>

<template>
    <Modal :show="show" max-width="md" @close="emit('close')">
        <div class="p-6">
            <div class="flex items-start gap-4">
                <div
                    class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-rose-100 text-rose-600"
                >
                    <ExclamationTriangleIcon class="h-5 w-5" />
                </div>
                <div class="min-w-0 flex-1">
                    <h2 class="text-base font-semibold text-slate-900">
                        {{ t('admin.delete_modal.title') }}
                    </h2>
                    <p class="mt-1 text-sm text-slate-600">
                        {{ confirmMessage }}
                    </p>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button
                    type="button"
                    class="btn-secondary"
                    :disabled="processing"
                    @click="emit('close')"
                >
                    {{ t('admin.delete_modal.cancel') }}
                </button>
                <button
                    type="button"
                    class="btn-danger"
                    :disabled="processing"
                    @click="emit('confirm')"
                >
                    <svg
                        v-if="processing"
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
                            d="M4 12a8 8 0 018-8V0C5.4 0 0 5.4 0 12h4z"
                        />
                    </svg>
                    <span>
                        {{
                            processing
                                ? t('admin.delete_modal.deleting')
                                : t('admin.delete_modal.delete')
                        }}
                    </span>
                </button>
            </div>
        </div>
    </Modal>
</template>
