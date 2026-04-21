import { computed, ref } from 'vue';
import { defineStore } from 'pinia';
import { extractApiError } from '@/common/helpers';
import { AUTH_TOKEN_KEY, AUTH_USER_KEY } from '../consts/storage-keys';
import type { AuthUser, LoginCredentials } from '../models';
import { authApi } from '../services/auth-api';

function readStoredUser(): AuthUser | null {
    try {
        const raw = localStorage.getItem(AUTH_USER_KEY);
        return raw ? (JSON.parse(raw) as AuthUser) : null;
    } catch {
        return null;
    }
}

export const useAuthStore = defineStore('auth', () => {
    // Initialise from localStorage so the store is correct on first tick.
    const token = ref<string | null>(localStorage.getItem(AUTH_TOKEN_KEY));
    const user = ref<AuthUser | null>(readStoredUser());
    const loading = ref(false);
    const error = ref<string | null>(null);

    const isAuthenticated = computed(() => token.value !== null);

    function persistSession(newToken: string, newUser: AuthUser): void {
        token.value = newToken;
        user.value = newUser;
        localStorage.setItem(AUTH_TOKEN_KEY, newToken);
        localStorage.setItem(AUTH_USER_KEY, JSON.stringify(newUser));
    }

    function clearSession(): void {
        token.value = null;
        user.value = null;
        localStorage.removeItem(AUTH_TOKEN_KEY);
        localStorage.removeItem(AUTH_USER_KEY);
    }

    async function login(credentials: LoginCredentials): Promise<void> {
        loading.value = true;
        error.value = null;
        try {
            const response = await authApi.login(credentials);
            persistSession(response.token, response.user);
        } catch (e) {
            error.value = extractApiError(e, 'Login failed');
            throw e;
        } finally {
            loading.value = false;
        }
    }

    async function logout(): Promise<void> {
        loading.value = true;
        try {
            await authApi.logout();
        } catch {
            // Even if the server call fails (expired token, network), the
            // local session must still be cleared to avoid a stuck admin UI.
        } finally {
            clearSession();
            loading.value = false;
        }
    }

    return {
        token,
        user,
        loading,
        error,
        isAuthenticated,
        login,
        logout,
        clearSession,
    };
});
