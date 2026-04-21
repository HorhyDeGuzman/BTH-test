import { storeToRefs } from 'pinia';
import { useCategoriesStore } from '../store/categories.store';

/**
 * Thin wrapper over the categories Pinia store. The store caches the
 * full list at module scope, so consumers can call fetchAll() freely —
 * subsequent calls return the cached list without hitting the network.
 */
export function useCategories() {
    const store = useCategoriesStore();
    const { items, loading, error } = storeToRefs(store);

    return {
        items,
        loading,
        error,
        fetchAll: store.fetchAll,
    };
}
