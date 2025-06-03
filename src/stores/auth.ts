// src/stores/auth.js
import { defineStore } from 'pinia';
import router from "@/router";

export const useAuthStore = defineStore('auth', {
    state: () => ({
        token: localStorage.getItem('token') || null,
        isAdmin: false,
    }),
    getters: {
        isAuthenticated: state => !!state.token
    },
    actions: {
        async login(token : string) {
            this.token = token;
            localStorage.setItem('token', token);

           await this.checkAdmin();
        },
        logout() {
            this.token = null;
            this.isAdmin = false;
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
        async checkAdmin() {
            const url = import.meta.env.VITE_ServerIP + `/user/get`;

            if (!this.isAuthenticated) {
                return false;
            }

            try {
                const response = await fetch(url, {
                    headers: {
                        "Authorization": `Bearer ${this.token}`,
                    }
                });

                const data = await response.json();

                if (data.code === 401 && data.message === "Expired JWT Token") {
                    this.logout();
                    this.isAdmin = false;
                }
                this.isAdmin =  !!data.roles?.includes("ROLE_ADMIN");
            } catch (error) {
                console.error("Fehler bei isAdmin:", error);
                this.isAdmin = false;
            }
        }

    }
});
