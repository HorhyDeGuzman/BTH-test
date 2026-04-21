import { storeToRefs } from 'pinia';
import { useAuthStore } from '../store/auth.store';

/**
 * Thin wrapper over the auth Pinia store. Kept for backwards compatibility —
 * pages can continue to `const { isAuthenticated, login } = useAuth()` while
 * the state lives in a single shared store.
 */
export function useAuth() {
    const store = useAuthStore();
    const { token, user, loading, error, isAuthenticated } = storeToRefs(store);

    return {
        token,
        user,
        loading,
        error,
        isAuthenticated,
        login: store.login,
        logout: store.logout,
        clearSession: store.clearSession,
    };
}
