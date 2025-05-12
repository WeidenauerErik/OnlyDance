<template>
  <main class="auth-page">
    <section class="auth-section">
      <h1 class="auth-title">Willkommen zurück!</h1>
      <div class="auth-form">
        <input v-model="email" type="email" placeholder="E-Mail" required/>
        <input v-model="password" type="password" placeholder="Passwort" required/>
        <button type="submit" class="auth-button" @click="handleLogin">Einloggen</button>
      </div>
      <RouterLink to="/signup" class="switch-link">Noch keinen Account? Jetzt registrieren</RouterLink>
    </section>
  </main>
</template>

<script setup>

import {ref} from "vue";
import axios from "axios";
import {useAuthStore} from "@/stores/auth.js";
import router from "@/router/index.js";

const auth = useAuthStore();


const email = ref("");
const password = ref("");

async function handleLogin() {

  try {
    const response = await axios.post("https://localhost/api/login", {
      email: email.value,
      password: password.value
    })
    auth.login(response.data.token)
    router.push("/hello");
  }catch (error){
    alert("Email oder Passwort sind nicht korrekt");
  }


}
</script>

<style scoped lang="scss">
.auth-page {
  min-height: 100vh;
  background: linear-gradient(to bottom right, #f3e8ff, #fce7f3, #ffffff);
  display: flex;
  align-items: center;
  justify-content: center;
}

.auth-section {
  text-align: center;
  width: 100%;
  max-width: 400px;
  padding: 2rem;
  background: white;
  border-radius: 1.5rem;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

.auth-title {
  font-size: 2rem;
  color: $colorPurpleLight;
  margin-bottom: 1.5rem;
}

.auth-form {
  display: flex;
  flex-direction: column;
  gap: 1rem;

  input {
    padding: 0.75rem 1rem;
    border: 1px solid #d1d5db;
    border-radius: 9999px;
    font-size: 1rem;
    outline: none;

    &:focus {
      border-color: $colorPurpleLight;
    }
  }

  .auth-button {
    background-color: $colorVioletLight;
    color: white;
    font-weight: 600;
    padding: 0.75rem;
    border-radius: 9999px;
    box-shadow: 0 8px 15px -3px $colorPurpleLight;
    transition: background-color 0.3s;

    &:hover {
      background-color: $colorPurpleLight;
    }
  }
}

.switch-link {
  display: block;
  margin-top: 1rem;
  color: $colorPurpleLight;
  text-decoration: underline;
  font-size: 0.9rem;
}
</style>
