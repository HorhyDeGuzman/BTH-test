<script setup lang="ts">
import DangerButton from '@/common/components/danger-button.vue';
import Modal from '@/common/components/modal.vue';
import SecondaryButton from '@/common/components/secondary-button.vue';
import type { Product } from '../models';

defineProps<{
    show: boolean;
    product: Product | null;
    processing?: boolean;
}>();

const emit = defineEmits<{
    (e: 'close'): void;
    (e: 'confirm'): void;
}>();
</script>

<template>
    <Modal :show="show" max-width="md" @close="emit('close')">
        <div class="p-6">
            <h2 class="text-lg font-semibold text-gray-900">Delete product</h2>
            <p class="mt-2 text-sm text-gray-600">
                Are you sure you want to delete
                <span class="font-medium text-gray-900">{{ product?.name ?? 'this product' }}</span
                >? This cannot be undone.
            </p>

            <div class="mt-6 flex justify-end gap-3">
                <SecondaryButton type="button" :disabled="processing" @click="emit('close')">
                    Cancel
                </SecondaryButton>
                <DangerButton type="button" :disabled="processing" @click="emit('confirm')">
                    {{ processing ? 'Deleting…' : 'Delete' }}
                </DangerButton>
            </div>
        </div>
    </Modal>
</template>
