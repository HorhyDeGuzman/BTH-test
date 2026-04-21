import { api } from '@/core/api';
import type { LoginCredentials, LoginResponse } from '../models';

export const authApi = {
    async login(credentials: LoginCredentials): Promise<LoginResponse> {
        const { data } = await api.post<LoginResponse>('/login', credentials);
        return data;
    },

    async logout(): Promise<void> {
        await api.post('/logout');
    },
};
