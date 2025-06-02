// src/stores/auth.js
import { defineStore } from 'pinia';
import router from "@/router";

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
        },
        checkAuth() {
            let url = import.meta.env.VITE_ServerIP + `/api/check`;
            if (this.isAuthenticated) {
                fetch(url, {
                    headers: {
                        "Authorization": `Bearer ${this.token}`,
                    }
                })
                    .then(response => response.json())
                    .then(response => {
                        if (response.code === 401 && response.message === "Expired JWT Token") {
                            this.logout();
                        }
                    });
            }


        },
    }
});
