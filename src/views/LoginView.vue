<template>
  <main class="auth-page">
    <section class="auth-section">
      <h1 class="auth-title">Willkommen zurück!</h1>
      <div class="auth-form">
        <input v-model="state.email" type="email" @blur="v$.email.$touch" placeholder="E-Mail" required />
        <p v-if="v$.email.$error" class="error-text">Email muss das richtige Format haben</p>
        <input v-model="state.password" type="password" @blur="v$.password.$touch" placeholder="Passwort" required />
        <p v-if="v$.password.$error" class="error-text">Passwort muss das richtige Format haben</p>
        <button :disabled="v$.$invalid" type="submit" class="auth-button" @click="handleLogin">Einloggen</button>
      </div>
      <RouterLink to="/signup" class="switch-link">Noch keinen Account? Jetzt registrieren</RouterLink>
    </section>
  </main>
</template>

<script setup>
import Swal from 'sweetalert2'
import {computed, reactive, ref} from "vue";
import axios from "axios";
import {useAuthStore} from "@/stores/auth.js";
import router from "@/router/index.js";
import {alpha, email, maxLength, minLength, required, sameAs} from "@vuelidate/validators";
import {useVuelidate} from "@vuelidate/core";

const auth = useAuthStore();
;

const state = reactive({
  email: '',
  password: '',
})

const rules = {
  email: {required, email,maxLength: maxLength(40)},
  password: {minLength: minLength(3), required,maxLength: maxLength(50)},
}

const v$ = useVuelidate(rules, state);

async function handleLogin() {

  const Toast = Swal.mixin({
    toast: true,
    position: "top-end",
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
      toast.onmouseenter = Swal.stopTimer;
      toast.onmouseleave = Swal.resumeTimer;
    }
  });
  try {
    const response = await axios.post("https://localhost/api/login", {
      email: state.email.trim(),
      password: state.password.trim()
    })
    auth.login(response.data.token);
    Toast.fire({
      icon: "success",
      title: "Hervorragend du bist eingelogged!"
    });
    router.push("/mainpage");
  }catch (error){
    Toast.fire({
      icon: "error",
      title: "E-Mail oder Passwort ist falsch!"
    });
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
  background: $colorWhite;
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
    &:disabled{
      background-color: grey;

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
