import { api } from '@/core/api';
import type { ApiResource } from '@/core/types';
import type { Category } from '../models';

interface CategoryListResponse {
    data: Category[];
}

export const categoriesApi = {
    async fetchAll(): Promise<Category[]> {
        const { data } = await api.get<CategoryListResponse>('/categories');
        return data.data;
    },

    async fetchOne(id: number): Promise<Category> {
        const { data } = await api.get<ApiResource<Category>>(`/categories/${id}`);
        return data.data;
    },
};
