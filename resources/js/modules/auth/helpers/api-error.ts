import axios from 'axios';
import type { ValidationErrorResponse } from '@/core/types';

/**
 * Best-effort extraction of a human-readable message from an unknown error.
 * Handles Laravel's standard error envelopes plus plain axios/network errors.
 */
export function extractApiError(err: unknown, fallback = 'Something went wrong'): string {
    if (axios.isAxiosError(err)) {
        const data = err.response?.data as Partial<ValidationErrorResponse> | undefined;
        if (data?.message) return data.message;
        if (err.message) return err.message;
    }
    if (err instanceof Error) return err.message;
    return fallback;
}

/**
 * Returns the field -> messages map from a Laravel 422 response, if present.
 */
export function extractValidationErrors(err: unknown): Record<string, string[]> {
    if (axios.isAxiosError(err) && err.response?.status === 422) {
        const data = err.response.data as Partial<ValidationErrorResponse>;
        return data.errors ?? {};
    }
    return {};
}
