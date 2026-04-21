import { computed, ref } from 'vue';
import { defineStore } from 'pinia';
import { extractApiError } from '@/common/helpers';
import type { PaginationMeta } from '@/core/types';
import type { Product, ProductListParams, ProductPayload } from '../models';
import { productsApi } from '../services/products-api';

interface ListCacheEntry {
    ids: number[];
    meta: PaginationMeta;
    fetchedAt: number;
}

/**
 * Cache key uniquely identifies a list request. Any param that changes the
 * returned rows must be included here.
 */
function cacheKey(params: ProductListParams): string {
    return [
        params.page ?? 1,
        params.per_page ?? '',
        params.category_id ?? '',
        (params.search ?? '').trim(),
    ].join('|');
}

export const useProductsStore = defineStore('products', () => {
    // Normalized product cache — single source of truth. Any mutation
    // updates this map and the list views re-read from it automatically.
    const byId = ref<Record<number, Product>>({});

    // Pointers for the "current" views rendered on screen.
    const currentListIds = ref<number[]>([]);
    const currentMeta = ref<PaginationMeta | null>(null);
    const currentOne = ref<Product | null>(null);

    // Page-level cache keyed by serialized list params.
    const listCache = ref<Record<string, ListCacheEntry>>({});

    const loading = ref(false);
    const error = ref<string | null>(null);

    // Current list as full Product objects; falls through to byId so the
    // row data stays fresh after an item is edited.
    const items = computed<Product[]>(() =>
        currentListIds.value
            .map((id) => byId.value[id])
            .filter((p): p is Product => p !== undefined),
    );

    async function fetchList(
        params: ProductListParams = {},
        options: { force?: boolean } = {},
    ): Promise<void> {
        const key = cacheKey(params);
        const cached = listCache.value[key];

        if (cached && !options.force) {
            // Cache hit — flip the current pointer synchronously. No network
            // request, no loading spinner, instant render.
            currentListIds.value = cached.ids;
            currentMeta.value = cached.meta;
            error.value = null;
            return;
        }

        loading.value = true;
        error.value = null;
        try {
            const result = await productsApi.fetchList(params);
            const ids = result.data.map((p) => p.id);
            for (const p of result.data) {
                byId.value[p.id] = p;
            }
            listCache.value[key] = { ids, meta: result.meta, fetchedAt: Date.now() };
            currentListIds.value = ids;
            currentMeta.value = result.meta;
        } catch (e) {
            error.value = extractApiError(e);
            throw e;
        } finally {
            loading.value = false;
        }
    }

    async function fetchOne(
        id: number,
        options: { force?: boolean } = {},
    ): Promise<Product> {
        const cached = byId.value[id];
        if (cached && !options.force) {
            currentOne.value = cached;
            error.value = null;
            return cached;
        }

        loading.value = true;
        error.value = null;
        try {
            const product = await productsApi.fetchOne(id);
            byId.value[id] = product;
            currentOne.value = product;
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
            const product = await productsApi.create(payload);
            byId.value[product.id] = product;
            invalidateLists();
            return product;
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
            const product = await productsApi.update(id, payload);
            byId.value[id] = product;
            if (currentOne.value?.id === id) currentOne.value = product;
            // An edit can change category / sort order, so lists are stale.
            invalidateLists();
            return product;
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
            delete byId.value[id];
            if (currentOne.value?.id === id) currentOne.value = null;
            invalidateLists();
        } catch (e) {
            error.value = extractApiError(e);
            throw e;
        } finally {
            loading.value = false;
        }
    }

    function invalidateLists(): void {
        listCache.value = {};
    }

    return {
        // state
        byId,
        currentListIds,
        currentMeta,
        currentOne,
        listCache,
        loading,
        error,
        // getters
        items,
        // actions
        fetchList,
        fetchOne,
        create,
        update,
        remove,
        invalidateLists,
    };
});
