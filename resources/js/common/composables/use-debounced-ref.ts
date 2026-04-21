import { customRef, type Ref } from 'vue';

/**
 * Returns a reactive ref whose writes are debounced. Reads are immediate,
 * but each set(value) resets a timer and the subscribers are notified only
 * once the timer elapses without further writes.
 *
 * Usage:
 *   const search = useDebouncedRef('', 400);
 *   watch(search, (v) => fetchList({ search: v }));
 */
export function useDebouncedRef<T>(initial: T, delay = 300): Ref<T> {
    let timer: ReturnType<typeof setTimeout> | null = null;
    let value = initial;

    return customRef<T>((track, trigger) => ({
        get() {
            track();
            return value;
        },
        set(next: T) {
            if (timer) clearTimeout(timer);
            timer = setTimeout(() => {
                value = next;
                trigger();
            }, delay);
        },
    }));
}
