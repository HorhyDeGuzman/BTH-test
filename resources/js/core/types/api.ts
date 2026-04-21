/**
 * Generic envelope returned by Laravel API resources with `::collection()`
 * when the underlying source is a LengthAwarePaginator.
 */
export interface Paginated<T> {
    data: T[];
    links: PaginationLinks;
    meta: PaginationMeta;
}

export interface PaginationLinks {
    first: string | null;
    last: string | null;
    prev: string | null;
    next: string | null;
}

export interface PaginationMeta {
    current_page: number;
    from: number | null;
    last_page: number;
    links: Array<{ url: string | null; label: string; active: boolean }>;
    path: string;
    per_page: number;
    to: number | null;
    total: number;
}

/**
 * Envelope for a single resource returned by `new Resource(...)`
 */
export interface ApiResource<T> {
    data: T;
}

/**
 * Laravel 422 validation error payload shape.
 */
export interface ValidationErrorResponse {
    message: string;
    errors: Record<string, string[]>;
}
