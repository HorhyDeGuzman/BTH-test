import { ref } from 'vue';
import { defineStore } from 'pinia';
import { extractApiError } from '@/common/helpers';
import type { Category } from '../models';
import { categoriesApi } from '../services/categories-api';

export const useCategoriesStore = defineStore('categories', () => {
    const items = ref<Category[]>([]);
    const loaded = ref(false);
    const loading = ref(false);
    const error = ref<string | null>(null);

    // In-flight dedup: multiple callers on a single tick share one request.
    let inflight: Promise<Category[]> | null = null;

    async function fetchAll(options: { force?: boolean } = {}): Promise<Category[]> {
        if (!options.force && loaded.value) return items.value;
        if (inflight) return inflight;

        loading.value = true;
        error.value = null;

        inflight = categoriesApi
            .fetchAll()
            .then((list) => {
                items.value = list;
                loaded.value = true;
                return list;
            })
            .catch((e) => {
                error.value = extractApiError(e);
                throw e;
            })
            .finally(() => {
                loading.value = false;
                inflight = null;
            });

        return inflight;
    }

    return { items, loaded, loading, error, fetchAll };
});
