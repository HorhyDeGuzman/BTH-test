/**
 * Format a numeric price as a currency-like string.
 * Kept intentionally simple — no locale dependency for now.
 */
export function formatPrice(price: number | string): string {
    const value = typeof price === 'string' ? parseFloat(price) : price;
    if (!Number.isFinite(value)) return '—';
    return `$${value.toFixed(2)}`;
}
