import { api } from '@/core/api';
import type { ApiResource, Paginated } from '@/core/types';
import type { Product, ProductListParams, ProductPayload } from '../models';

export const productsApi = {
    async fetchList(params: ProductListParams = {}): Promise<Paginated<Product>> {
        const { data } = await api.get<Paginated<Product>>('/products', {
            params: {
                page: params.page,
                per_page: params.per_page,
                category_id: params.category_id ?? undefined,
                search: params.search?.trim() || undefined,
            },
        });
        return data;
    },

    async fetchOne(id: number): Promise<Product> {
        const { data } = await api.get<ApiResource<Product>>(`/products/${id}`);
        return data.data;
    },

    async create(payload: ProductPayload): Promise<Product> {
        const { data } = await api.post<ApiResource<Product>>('/products', payload);
        return data.data;
    },

    async update(id: number, payload: ProductPayload): Promise<Product> {
        const { data } = await api.put<ApiResource<Product>>(`/products/${id}`, payload);
        return data.data;
    },

    async remove(id: number): Promise<void> {
        await api.delete(`/products/${id}`);
    },
};
