import axios from 'axios';

const AUTH_TOKEN_KEY = 'auth_token';
const AUTH_USER_KEY = 'auth_user';

export const api = axios.create({
    baseURL: '/api',
    headers: {
        Accept: 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
    },
});

// Attach the stored bearer token on every outgoing request. Reading from
// localStorage directly (rather than importing the auth composable) avoids
// an import cycle: the auth composable also uses `api` to call the backend.
api.interceptors.request.use((config) => {
    const token = localStorage.getItem(AUTH_TOKEN_KEY);
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
});

// On 401 from a protected endpoint, wipe the session and — if we're inside
// the admin area — bounce to /login. Public pages just see the error.
api.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response?.status === 401) {
            localStorage.removeItem(AUTH_TOKEN_KEY);
            localStorage.removeItem(AUTH_USER_KEY);
            if (
                typeof window !== 'undefined' &&
                window.location.pathname.startsWith('/admin')
            ) {
                window.location.href = '/login';
            }
        }
        return Promise.reject(error);
    },
);
