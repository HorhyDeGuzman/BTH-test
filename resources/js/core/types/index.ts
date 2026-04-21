export type {
    ApiResource,
    Paginated,
    PaginationLinks,
    PaginationMeta,
    ValidationErrorResponse,
} from './api';

export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at?: string;
}

/**
 * Shape of the default props every Inertia page can rely on. We don't
 * share any auth from the server (admin auth is a Sanctum token held in
 * the client), so the base type is just a free-form record — pages add
 * their own page-specific props via the generic.
 */
export type PageProps<T extends Record<string, unknown> = Record<string, unknown>> = T;
