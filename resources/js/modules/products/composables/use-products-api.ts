import { storeToRefs } from 'pinia';
import { useProductsStore } from '../store/products.store';

/**
 * Thin wrapper over the products Pinia store kept for backwards
 * compatibility with pages that consume the composable. The underlying
 * state is shared and cached globally, so navigating away and back
 * doesn't re-hit the API.
 */
export function useProductsApi() {
    const store = useProductsStore();
    const { items, currentMeta, currentOne, loading, error } = storeToRefs(store);

    return {
        items,
        meta: currentMeta,
        current: currentOne,
        loading,
        error,
        fetchList: store.fetchList,
        fetchOne: store.fetchOne,
        create: store.create,
        update: store.update,
        remove: store.remove,
        invalidateLists: store.invalidateLists,
    };
}
