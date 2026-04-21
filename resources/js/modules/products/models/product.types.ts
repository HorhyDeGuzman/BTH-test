import type { Category } from '@/modules/categories';

export interface Product {
    id: number;
    name: string;
    name_en: string | null;
    description: string | null;
    description_en: string | null;
    price: number;
    image_url: string | null;
    category_id: number;
    category?: Category;
    created_at: string;
    updated_at: string;
}

export interface ProductPayload {
    name: string;
    name_en?: string | null;
    description: string | null;
    description_en?: string | null;
    price: number;
    image_url?: string | null;
    category_id: number;
}

export interface ProductListParams {
    page?: number;
    per_page?: number;
    category_id?: number | null;
    search?: string;
}
