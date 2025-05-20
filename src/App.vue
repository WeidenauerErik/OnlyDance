<script setup lang="ts">
import {RouterView} from 'vue-router'
import NavBarComponent from './components/NavBarComponent.vue';
import FooterComponent from './components/FooterComponent.vue';
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
main {
  overflow: scroll;
}
</style>
