<template>
  <main class="hero-page">
    <section class="hero-section">
      <h1 class="hero-title">
        Tanzen lernen mit <span class="highlight">OnlyDance</span>
      </h1>
      <p class="hero-subtitle">
        Online Tanzkurse für alle Levels. Lerne wann und wo du willst – alleine oder mit Freunden.
      </p>
      <RouterLink
          to="/mainpage"
          @click="startNow"
          class="start-button"
      >
        Starte jetzt mit OnlyDance
      </RouterLink>
    </section>
  </main>
</template>

<script setup>
</script>

<style scoped lang="scss">
.hero-page {
  min-height: 100vh;
  background: linear-gradient(to bottom right, #f3e8ff, #fce7f3, #ffffff);
  color: $colorVioletDark;
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
  color: $colorPurpleLight;
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
