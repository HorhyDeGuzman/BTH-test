import { ref } from 'vue';
import type { Paginated, PaginationMeta } from '@/core/types';
import type { Product, ProductListParams, ProductPayload } from '../models';
import { productsApi } from '../services/products-api';
import { extractApiError } from '@/common/helpers';

/**
 * Composable wrapping productsApi with reactive loading / error / data state.
 * Each caller gets its own state; use this inside the component where the
 * data is rendered.
 */
export function useProductsApi() {
    const items = ref<Product[]>([]);
    const meta = ref<PaginationMeta | null>(null);
    const current = ref<Product | null>(null);
    const loading = ref(false);
    const error = ref<string | null>(null);

    async function fetchList(params: ProductListParams = {}): Promise<Paginated<Product>> {
        loading.value = true;
        error.value = null;
        try {
            const result = await productsApi.fetchList(params);
            items.value = result.data;
            meta.value = result.meta;
            return result;
        } catch (e) {
            error.value = extractApiError(e);
            throw e;
        } finally {
            loading.value = false;
        }
    }

    async function fetchOne(id: number): Promise<Product> {
        loading.value = true;
        error.value = null;
        try {
            const product = await productsApi.fetchOne(id);
            current.value = product;
            return product;
        } catch (e) {
            error.value = extractApiError(e);
            throw e;
        } finally {
            loading.value = false;
        }
    }

    async function create(payload: ProductPayload): Promise<Product> {
        loading.value = true;
        error.value = null;
        try {
            return await productsApi.create(payload);
        } catch (e) {
            error.value = extractApiError(e);
            throw e;
        } finally {
            loading.value = false;
        }
    }

    async function update(id: number, payload: ProductPayload): Promise<Product> {
        loading.value = true;
        error.value = null;
        try {
            return await productsApi.update(id, payload);
        } catch (e) {
            error.value = extractApiError(e);
            throw e;
        } finally {
            loading.value = false;
        }
    }

    async function remove(id: number): Promise<void> {
        loading.value = true;
        error.value = null;
        try {
            await productsApi.remove(id);
            items.value = items.value.filter((p) => p.id !== id);
        } catch (e) {
            error.value = extractApiError(e);
            throw e;
        } finally {
            loading.value = false;
        }
    }

    return {
        items,
        meta,
        current,
        loading,
        error,
        fetchList,
        fetchOne,
        create,
        update,
        remove,
    };
}
