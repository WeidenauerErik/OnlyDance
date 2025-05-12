// src/stores/auth.js
import { defineStore } from 'pinia';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        token: localStorage.getItem('token') || null
    }),
    getters: {
        isAuthenticated: state => !!state.token
    },
    actions: {
        login(token : string) {
            this.token = token;
            localStorage.setItem('token', token);
        },
        logout() {
            this.token = null;
            localStorage.removeItem('token');
            window.location.reload();
        }
    }
});
