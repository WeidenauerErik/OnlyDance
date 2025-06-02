<script setup lang="ts">
import {RouterView} from 'vue-router'
import NavBarComponent from './components/NavBarComponent.vue';
import FooterComponent from './components/FooterComponent.vue';
import {useAuthStore} from "@/stores/auth.ts";
import {onMounted} from "vue";
const authStore = useAuthStore();

onMounted(() => {
  authStore.checkAuth();
})


</script>

<template>
  <div id="pageContainer">

    <div id="contentWrap">
      <header>
        <NavBarComponent/>
      </header>

      <main>
      <RouterView/>
      </main>
    </div>

    <footer id="footer">
      <FooterComponent/>
    </footer>
  </div>
</template>

<style scoped lang="scss">
</style>
