<script setup lang="ts">
import { ref, onMounted, onUnmounted } from "vue";
import { RouterLink, useRouter } from "vue-router";

import searchIcon from '@/assets/icons/searchIcon.svg';
import menuIcon from '@/assets/icons/menuIcon.svg';
import {useAuthStore} from "@/stores/auth.ts";

const router = useRouter();
const showMenu = ref(false);
const searchQuery = ref('');

const auth = useAuthStore();

const closeMenu = (event: MouseEvent) => {
  const menu = document.getElementById("menuDropdown");
  const menuIcon = document.getElementById("menuIconNavBar");
  if (menu && menuIcon && !menu.contains(event.target as Node) && !menuIcon.contains(event.target as Node))
    showMenu.value = false;
};

const handleSearch = () => {
  console.log('Searching for:', searchQuery.value);
};

onMounted(() => {
  document.addEventListener("click", closeMenu);
});

onUnmounted(() => {
  document.removeEventListener("click", closeMenu);
});
</script>

<template>
  <header class="navbar">
    <div class="navbar-container">
      <!--Brand Name -->
      <RouterLink to="/" class="navbar-brand">
        <h1 class="navbar-title">OnlyDance</h1>
      </RouterLink>

      <!-- Navigation Items -->
      <nav class="navbar-nav">
        <!-- Navigation Links for Desktop -->
        <div class="navbar-links desktop-only">
          <RouterLink to="/mainpage" class="nav-link">Hauptseite</RouterLink>
          <RouterLink to="/checklist" class="nav-link">Checkliste</RouterLink>
          <RouterLink to="/events" class="nav-link">Event Kalender</RouterLink>
        </div>

        <!-- Search Bar -->
        <div class="search-container" v-if="!(router.currentRoute.value.path === '/mainpage')">
          <input
              type="text"
              class="search-input"
              placeholder="Suchen..."
              v-model="searchQuery"
              @keyup.enter="handleSearch"
          >
          <button class="search-button" @click="handleSearch">
            <img :src="searchIcon" alt="Suchen">
          </button>
        </div>

        <!-- Authentication Buttons for Desktop -->
        <div v-if="!auth.isAuthenticated"  class="auth-buttons desktop-only">
          <RouterLink to="/login" class="login-btn">Anmelden</RouterLink>
          <RouterLink to="/signup" class="signup-btn">Registrieren</RouterLink>
        </div>
        <div v-if="auth.isAuthenticated"  class="auth-buttons desktop-only">
          <button @click="auth.logout()" class="signup-btn">Ausloggen</button>
        </div>

        <!-- Mobile Menu Toggle -->
        <button class="menu-toggle" @click="showMenu = !showMenu" aria-label="Toggle menu">
          <img :src="menuIcon" id="menuIconNavBar" alt="Menu">
        </button>
      </nav>
    </div>

    <!-- Mobile Menu Dropdown -->
    <div v-if="!auth.isAuthenticated" v-show="showMenu" class="mobile-menu" id="menuDropdown">
      <RouterLink to="/mainpage" class="mobile-link" @click="showMenu = false">Hauptseite</RouterLink>
      <RouterLink to="/checklist" class="mobile-link" @click="showMenu = false">Checkliste</RouterLink>
      <RouterLink to="/events" class="mobile-link" @click="showMenu = false">Event Kalender</RouterLink>
      <div class="mobile-divider"></div>
      <RouterLink to="/login" class="mobile-link" @click="showMenu = false">Anmelden</RouterLink>
      <RouterLink to="/signup" class="mobile-link" @click="showMenu = false">Registrieren</RouterLink>
    </div>
    <div v-if="auth.isAuthenticated" v-show="showMenu" class="mobile-menu" id="menuDropdown">
      <RouterLink to="/mainpage" class="mobile-link" @click="showMenu = false">Hauptseite</RouterLink>
      <RouterLink to="/checklist" class="mobile-link" @click="showMenu = false">Checkliste</RouterLink>
      <RouterLink to="/events" class="mobile-link" @click="showMenu = false">Event Kalender</RouterLink>
      <div class="mobile-divider"></div>
      <button @click="auth.logout()" class="mobile-link">Ausloggen</button>
    </div>
  </header>
</template>

<style scoped lang="scss">
// Variables
$color-purple-primary: #8A2BE2; // Primary purple
$color-purple-light: #9D4EDD;   // Lighter purple
$color-purple-dark: #6A0DAD;    // Darker purple
$color-white: #FFFFFF;
$color-gray-light: #F5F5F5;
$color-gray: #E0E0E0;
$color-text-dark: #333333;

// Breakpoints
$breakpoint-mobile: 768px;
$breakpoint-tablet: 1424px;

// Base styles
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

// Brand Section
.navbar-brand {
  display: flex;
  align-items: center;
  text-decoration: none;
  color: $color-white;
  transition: opacity 0.2s ease;
  cursor: pointer;

  &:hover {
    opacity: 0.9;
  }
}

.navbar-logo {
  height: 2.5rem;
  margin-right: 0.75rem;
}

.navbar-title {
  font-size: 1.5rem;
  font-weight: 700;
  letter-spacing: 0.5px;
  margin: 0;
}

// Navigation Section
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
  transition: all 0.2s ease;
  position: relative;

  &::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 0;
    height: 2px;
    background: linear-gradient(to bottom right, #f3e8ff, #fce7f3, #ffffff);
    transition: width 0.2s ease;
  }

  &:hover, &.router-link-active {
    background-color: rgba(255, 255, 255, 0.1);

    &::after {
      width: 80%;
    }
  }
}

// Search Bar
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
  transition: all 0.2s ease;

  &:focus {
    outline: none;
    box-shadow: 0 0 0 1px rgba(255, 255, 255, 0.5);
    background: linear-gradient(to bottom right, #f3e8ff, #fce7f3, #ffffff);
  }

  &::placeholder {
    color: #999;
  }
}

.search-button {
  position: absolute;
  right: 0.5rem;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  cursor: pointer;
  padding: 0.25rem;
  display: flex;
  align-items: center;
  justify-content: center;

  img {
    width: 1.25rem;
    height: 1.25rem;
  }
}

// Auth Buttons
.auth-buttons {
  display: flex;
  gap: 1rem;
  align-items: center;
}

.login-btn {
  color: $color-white;
  text-decoration: none;
  font-weight: 500;
  padding: 0.5rem 1rem;
  border-radius: 0.25rem;
  transition: background-color 0.2s ease;

  &:hover {
    background-color: rgba(255, 255, 255, 0.1);
  }
}

.signup-btn {
  color: $colorVioletLight;
  background: linear-gradient(to bottom right, #f3e8ff, #fce7f3, #ffffff);
  text-decoration: none;
  font-weight: 600;
  padding: 0.5rem 1rem;
  border-radius: 0.25rem;
  transition: all 0.2s ease;

  &:hover {
    background-color: $color-gray-light;
    transform: translateY(-1px);
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
  }
}

// Mobile Menu
.menu-toggle {
  display: none;
  background: none;
  border: none;
  cursor: pointer;
  padding: 0.5rem;

  img {
    width: 1.5rem;
    height: 1.5rem;
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
  transform-origin: top right;
  animation: slideDown 0.2s ease-out;
}

.mobile-link {
  display: block;
  padding: 0.75rem 1.5rem;
  color: $color-text-dark;
  text-decoration: none;
  font-weight: 500;
  transition: background-color 0.2s ease;

  &:hover {
    background-color: $color-gray-light;
    color: $color-purple-primary;
  }
}

.mobile-divider {
  height: 1px;
  background: $color-gray;
  margin: 0.5rem 0;
}

// Responsive Design
@media (max-width: $breakpoint-tablet) {
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

// Animations
@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>