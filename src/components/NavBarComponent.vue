<script setup lang="ts">
import { ref, onMounted, onUnmounted, watch } from "vue";
import { RouterLink, useRouter } from "vue-router";
import searchIcon from '@/assets/icons/searchIcon.svg';
import menuIcon from '@/assets/icons/menuIcon.svg';
import { useAuthStore } from "@/stores/auth.ts";

const router = useRouter();
const showMenu = ref(false);
const searchQuery = ref('');
const searchResults = ref<{ id: number, name: string, type: 'Tanz' | 'Tanzschritt' }[]>([]);
const allDances = ref([]);
const allStepsequences = ref([]);
const auth = useAuthStore();

const closeMenu = (event: MouseEvent) => {
  const menu = document.getElementById("menuDropdown");
  const menuIcon = document.getElementById("menuIconNavBar");
  if (menu && menuIcon && !menu.contains(event.target as Node) && !menuIcon.contains(event.target as Node)) {
    showMenu.value = false;
  }
};

const fetchSearchData = async () => {
  try {
    const [dancesRes, stepsRes] = await Promise.all([
      fetch(import.meta.env.VITE_ServerIP + "/dance/dances"),
      fetch(import.meta.env.VITE_ServerIP + "/stepsequence/get"),
    ]);

    const [dancesData, stepsData] = await Promise.all([
      dancesRes.json(),
      stepsRes.json(),
    ]);

    allDances.value = dancesData;
    allStepsequences.value = stepsData;
  } catch (error) {
    console.error("Fehler beim Laden der Suchdaten:", error);
  }
};

const filterSearchResults = () => {
  const query = searchQuery.value.toLowerCase().trim();
  if (!query) {
    searchResults.value = [];
    return;
  }

  const danceMatches = allDances.value
      .filter((d: any) => d.name.toLowerCase().includes(query))
      .map((d: any) => ({ id: d.id, name: d.name, type: "Tanz" }));

  const stepMatches = allStepsequences.value
      .filter((s: any) => s.name.toLowerCase().includes(query))
      .map((s: any) => ({ id: s.id, name: s.name, type: "Tanzschritt" }));

  searchResults.value = [...danceMatches, ...stepMatches].slice(0, 5);
};

const handleSearchClick = (result: { id: number, type: string }) => {
  if (result.type === "Tanz") {
    router.push("/mainpage");
    setTimeout(() => location.reload(), 100); // oder über globalen Zustand triggern
  } else {
    router.push(`/danceView/${result.id}`);
  }

  searchQuery.value = '';
  searchResults.value = [];
};

onMounted(() => {
  document.addEventListener("click", closeMenu);
  fetchSearchData();
});

onUnmounted(() => {
  document.removeEventListener("click", closeMenu);
});

watch(searchQuery, filterSearchResults);
</script>

<template>
  <header class="navbar">
    <div class="navbar-container">
      <RouterLink to="/" class="navbar-brand">
        <h1 class="navbar-title">OnlyDance</h1>
      </RouterLink>

      <nav class="navbar-nav">
        <div class="navbar-links desktop-only">
          <RouterLink to="/mainpage" class="nav-link">Hauptseite</RouterLink>
          <RouterLink to="/checklists" class="nav-link" v-if="auth.isAuthenticated">Checklisten</RouterLink>
          <RouterLink v-if="auth.isAdmin" to="/enterdance" class="nav-link">Tanz Hinzufügen</RouterLink>

        </div>

        <!-- Search -->
        <div class="search-container" v-if="!(router.currentRoute.value.path === '/mainpage')">
          <input
              type="text"
              class="search-input"
              placeholder="Suchen..."
              v-model="searchQuery"
          >
          <button class="search-button">
            <img :src="searchIcon" alt="Suchen">
          </button>

          <!-- Ergebnisse -->
          <ul v-if="searchResults.length > 0" class="search-results">
            <li v-for="result in searchResults" :key="result.id" @click="handleSearchClick(result)">
              <span class="result-name">{{ result.name }}</span>
              <span class="result-type">({{ result.type }})</span>
            </li>
          </ul>
        </div>

        <div v-if="!auth.isAuthenticated" class="auth-buttons desktop-only">
          <RouterLink to="/login" class="login-btn">Anmelden</RouterLink>
          <RouterLink to="/signup" class="signup-btn">Registrieren</RouterLink>
        </div>
        <div v-if="auth.isAuthenticated" class="auth-buttons desktop-only">
          <button @click="auth.logout()" class="signup-btn">Ausloggen</button>
        </div>

        <button class="menu-toggle" @click="showMenu = !showMenu" aria-label="Toggle menu">
          <img :src="menuIcon" id="menuIconNavBar" alt="Menu">
        </button>
      </nav>
    </div>

    <!-- Mobile Menu -->
    <div v-if="!auth.isAuthenticated" v-show="showMenu" class="mobile-menu" id="menuDropdown">
      <RouterLink to="/mainpage" class="mobile-link" @click="showMenu = false">Hauptseite</RouterLink>
      <div class="mobile-divider"></div>
      <RouterLink to="/login" class="mobile-link" @click="showMenu = false">Anmelden</RouterLink>
      <RouterLink to="/signup" class="mobile-link" @click="showMenu = false">Registrieren</RouterLink>
    </div>
    <div v-if="auth.isAuthenticated" v-show="showMenu" class="mobile-menu" id="menuDropdown">
      <RouterLink to="/mainpage" class="mobile-link" @click="showMenu = false">Hauptseite</RouterLink>
      <RouterLink to="/checklists" class="mobile-link" @click="showMenu = false">Checklisten</RouterLink>
      <RouterLink v-if="auth.isAdmin" to="/enterdance" class="mobile-link" @click="showMenu = false">Tanz Hinzufügen</RouterLink>

      <div class="mobile-divider"></div>
      <button @click="auth.logout()" class="mobile-link">Ausloggen</button>
    </div>
  </header>
</template>

<style scoped lang="scss">
// Farben
$color-purple-primary: #8A2BE2;
$color-purple-light: #9D4EDD;
$color-purple-dark: #6A0DAD;
$color-white: #FFFFFF;
$color-gray-light: #F5F5F5;
$color-gray: #E0E0E0;
$color-text-dark: #333333;

// Navbar-Grundstruktur
.navbar {
  position: sticky;
  top: 0;
  width: 100%;
  background-color: $colorVioletLight;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  z-index: 1000;
}

.navbar-container {
  margin: 0 auto;
  padding: 0 1rem;
  height: 4.5rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

// Brand
.navbar-brand {
  display: flex;
  align-items: center;
  text-decoration: none;
  color: $color-white;
  cursor: pointer;
}

.navbar-title {
  font-size: 1.5rem;
  font-weight: 700;
  letter-spacing: 0.5px;
  margin: 0;
}

// Links
.navbar-nav {
  display: flex;
  align-items: center;
  gap: 1.5rem;
}

.navbar-links {
  display: flex;
  gap: 1.5rem;
}

.nav-link {
  color: $color-white;
  text-decoration: none;
  font-weight: 500;
  padding: 0.5rem 0.75rem;
  border-radius: 0.25rem;
  position: relative;

  &:hover,
  &.router-link-active {
    background-color: rgba(255, 255, 255, 0.1);
  }
}

// Suche
.search-container {
  position: relative;
  width: 240px;
}

.search-input {
  width: 100%;
  height: 2.5rem;
  padding: 0 2.5rem 0 1rem;
  border: none;
  border-radius: 1.25rem;
  background: linear-gradient(to bottom right, #f3e8ff, #fce7f3, #ffffff);
  color: $color-text-dark;
  font-size: 0.9rem;
}

.search-button {
  position: absolute;
  right: 0.5rem;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  cursor: pointer;

  img {
    width: 1.25rem;
    height: 1.25rem;
  }
}

// Suchergebnisse
.search-results {
  position: absolute;
  top: 100%;
  margin-top: 0.5rem;
  width: 100%;
  background-color: $color-white;
  border: 1px solid $color-gray;
  border-radius: 0.75rem;
  max-height: 250px;
  overflow-y: auto;
  z-index: 1002;
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);

  li {
    padding: 0.75rem 1rem;
    cursor: pointer;
    color: $color-text-dark;
    display: flex;
    flex-direction: column;
    border-bottom: 1px solid $color-gray-light;
    transition: background-color 0.2s ease;

    &:last-child {
      border-bottom: none;
    }

    &:hover {
      background-color: $color-gray-light;
    }

    .result-name {
      font-size: 1rem;
      font-weight: 600;
      margin-bottom: 0.25rem;
    }

    .result-type {
      font-size: 0.85rem;
      font-style: italic;
      color: $color-purple-dark;
      opacity: 0.8;
    }
  }
}

// Auth
.auth-buttons {
  display: flex;
  gap: 1rem;
  align-items: center;
}

.login-btn {
  color: $color-white;
  text-decoration: none;
  font-weight: 500;
}

.signup-btn {
  color: $color-purple-dark;
  background: $color-white;
  font-weight: 600;
  padding: 0.5rem 1rem;
  border-radius: 0.25rem;
}

// Mobile
.menu-toggle {
  display: none;
  background: none;
  border: none;
  cursor: pointer;

  img {
    width: 1.5rem;
  }
}

.mobile-menu {
  position: absolute;
  top: 4.5rem;
  right: 1rem;
  background: linear-gradient(to bottom right, #f3e8ff, #fce7f3, #ffffff);
  border-radius: 0.5rem;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  padding: 1rem 0;
  width: 200px;
  z-index: 1001;
}

.mobile-link {
  display: block;
  padding: 0.75rem 1.5rem;
  color: $color-text-dark;
  text-decoration: none;
  font-weight: 500;
}

.mobile-divider {
  height: 1px;
  background: $color-gray;
  margin: 0.5rem 0;
}

// Responsiveness
@media (max-width: 1424px) {
  .desktop-only {
    display: none;
  }

  .menu-toggle {
    display: block;
  }

  .search-container {
    width: 180px;
  }

  .navbar-title {
    font-size: 1.25rem;
  }
}

@media (max-width: 480px) {
  .navbar-title {
    display: none;
  }

  .search-container {
    width: 150px;
  }
}
</style>