<template>
  <main class="auth-page">
    <section class="auth-section">
      <h1 class="auth-title">Erstelle deinen Account</h1>
      <div  class="auth-form">

        <input v-model="state.name" type="text" @blur="v$.name.$touch" placeholder="Name" required />
        <p v-if="v$.name.$error" class="error-text">Name darf nur aus Buchstaben bestehen</p>

        <input v-model="state.email" type="email" @blur="v$.email.$touch" placeholder="E-Mail" required />
        <p v-if="v$.email.$error" class="error-text">Email muss das richtige Format haben</p>

        <input v-model="state.password" type="password" @blur="v$.password.$touch" placeholder="Passwort" required />
        <p v-if="v$.password.$error" class="error-text">Fehler beim Passwort (mindestens 3 Zeichen)</p>

        <input v-model="state.passwordRepeat" type="password" @blur="v$.passwordRepeat.$touch" placeholder="Passwort wiederholen" required />
        <p v-if="v$.passwordRepeat.$error" class="error-text">Passwort stimmt nicht überein</p>

        <button :disabled="v$.$invalid" @click="submitForm" class="auth-button">Registrieren</button>
      </div>
      <RouterLink to="/login" class="switch-link">Schon registriert? Jetzt einloggen</RouterLink>
    </section>
  </main>
</template>

<script setup>
import {computed, reactive, ref} from 'vue'
import {useVuelidate} from '@vuelidate/core'
import {required, email, numeric, sameAs, alpha, minLength} from '@vuelidate/validators'
import axios from "axios";
import router from "@/router/index.js";


const state = reactive({
  name: '',
  email: '',
  password: '',
  passwordRepeat: '',
})

const rules = {
  name: {required,alpha},
  email: {required, email},
  password: {minLength: minLength(3), required},
  passwordRepeat: {
    sameAs: sameAs(computed(() => state.password)),
    required,
  },
}

const v$ = useVuelidate(rules, state);

async function submitForm() {
  v$.value.$validate();
  if (v$.value.$error) {
    alert("eingabe nicht Korrekt")
  } else {

    try {
      const response = await axios.post("https://localhost/user/register", {
        name: state.name,
        email: state.email,
        password: state.password,
      });
      alert(response.data.status);
      setTimeout(() => router.push("/login"),100)
    }catch (AxiosError){
      if( AxiosError.response.data.error.type === "email"){
        alert("Es gibt bereits einen Account mit dieser Email Adresse")
      }
    }



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
  font-family: 'Helvetica Neue', sans-serif;
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
  .error-text{
    color: $colorVioletLight;
    font-size: 0.75rem;
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
