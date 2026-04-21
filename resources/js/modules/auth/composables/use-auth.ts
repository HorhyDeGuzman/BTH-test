import { computed, readonly, ref } from 'vue';
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

// Module-level refs so every useAuth() call sees the same state.
const token = ref<string | null>(localStorage.getItem(AUTH_TOKEN_KEY));
const user = ref<AuthUser | null>(readStoredUser());
const loading = ref(false);
const error = ref<string | null>(null);

function persistSession(newToken: string, newUser: AuthUser) {
    token.value = newToken;
    user.value = newUser;
    localStorage.setItem(AUTH_TOKEN_KEY, newToken);
    localStorage.setItem(AUTH_USER_KEY, JSON.stringify(newUser));
}

function clearSession() {
    token.value = null;
    user.value = null;
    localStorage.removeItem(AUTH_TOKEN_KEY);
    localStorage.removeItem(AUTH_USER_KEY);
}

export function useAuth() {
    const isAuthenticated = computed(() => token.value !== null);

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
        token: readonly(token),
        user: readonly(user),
        loading: readonly(loading),
        error: readonly(error),
        isAuthenticated,
        login,
        logout,
        clearSession,
    };
}
