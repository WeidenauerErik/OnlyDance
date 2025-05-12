<script setup lang="ts">


import {onMounted, ref} from "vue";
import axios from "axios";
import {useAuthStore} from "@/stores/auth.ts";
import router from "@/router";

const auth = useAuthStore();
const username = ref("");

onMounted(async () => {

  try {
    const response = await axios.get("https://localhost/user/get", {
      headers: {
        "Authorization": `Bearer ${auth.token}`,
      }
    })


    username.value = response.data.name;
  }catch (err){
    router.push("/login");
  }
})

</script>

<template>
  <main class="hero-page">
    <section class="hero-section">
      <h1 class="hero-title">
        Hallo {{username}}
      </h1>

      <RouterLink
          to="/mainpage"
          class="start-button"
      >
        zurück zur Starteseite
      </RouterLink>
    </section>
  </main>
</template>

<style scoped lang="scss">
.hero-page {
  min-height: 100vh;
  background: linear-gradient(to bottom right, #f3e8ff, #fce7f3, #ffffff);
  color: #2d3748;
  font-family: 'Helvetica Neue', sans-serif;
}

.hero-section {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
  padding: 5rem 1.5rem;
}

.hero-title {
  font-size: 3rem;
  font-weight: 800;
  line-height: 1.2;
  color: #7e22ce;
  margin-bottom: 1rem;
  text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);

  @media (min-width: 768px) {
    font-size: 3.75rem;
  }

  .highlight {
    color: $colorVioletLight;
  }
}

.hero-subtitle {
  font-size: 1.125rem;
  max-width: 40rem;
  color: #4b5563;
  margin-top: 1rem;

  @media (min-width: 768px) {
    font-size: 1.25rem;
  }
}

.start-button {
  margin-top: 2rem;
  background-color: $colorVioletLight;
  color: white;
  font-size: 1.125rem;
  font-weight: 600;
  padding: 0.75rem 2rem;
  border-radius: 9999px;
  box-shadow: 0 10px 15px -3px $colorPurpleLight;
  transition: background-color 0.3s;

  &:hover {
    background-color: $colorPurpleLight;
  }
}
</style>