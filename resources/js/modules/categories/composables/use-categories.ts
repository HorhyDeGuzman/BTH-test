import { ref } from 'vue';
import { extractApiError } from '@/common/helpers';
import type { Category } from '../models';
import { categoriesApi } from '../services/categories-api';

let sharedCategories: Category[] | null = null;
let sharedPromise: Promise<Category[]> | null = null;

/**
 * Lightweight composable for the categories list. Because the set is small,
 * rarely changes and is requested by multiple pages (HomePage, admin forms),
 * we cache the resolved list at module scope and share the in-flight promise.
 */
export function useCategories() {
    const items = ref<Category[]>(sharedCategories ?? []);
    const loading = ref(false);
    const error = ref<string | null>(null);

    async function fetchAll(force = false): Promise<Category[]> {
        if (!force && sharedCategories) {
            items.value = sharedCategories;
            return sharedCategories;
        }

        loading.value = true;
        error.value = null;

        if (!sharedPromise) {
            sharedPromise = categoriesApi
                .fetchAll()
                .then((list) => {
                    sharedCategories = list;
                    return list;
                })
                .finally(() => {
                    sharedPromise = null;
                });
        }

        try {
            const list = await sharedPromise;
            items.value = list;
            return list;
        } catch (e) {
            error.value = extractApiError(e);
            throw e;
        } finally {
            loading.value = false;
        }
    }

    return { items, loading, error, fetchAll };
}
