<script setup lang="ts">
import {useAuthStore} from "@/stores/auth.ts";
import {onMounted, ref, watch, computed} from "vue";
import router from "@/router";
import searchIcon from "@/assets/icons/searchIcon.svg";

const auth = useAuthStore();
const url = ref("");
const checklists = ref([]);
const isGlobal = ref(false);
const searchQuery = ref("");

// Daten laden (initial "meine Checklisten")
onMounted(() => {
  if (auth.isAuthenticated) {
    url.value = import.meta.env.VITE_ServerIP + `/checklist/user/get/`;
  }

  fetch(url.value, {
    headers: {
      "Authorization": `Bearer ${auth.token}`,
    }
  })
      .then(response => response.json())
      .then(response => {
        if (response.code === 401 && response.message === "Expired JWT Token") {
          alert("Ihre Session ist abgelaufen, bitte loggen Sie sich erneut ein");
          router.push("/login");
        }
        checklists.value = response;
      });
});

watch(isGlobal, () => {
  url.value = import.meta.env.VITE_ServerIP + (isGlobal.value
      ? `/checklist/get`
      : `/checklist/user/get/`);

  fetch(url.value, {
    headers: {
      "Authorization": `Bearer ${auth.token}`,
    }
  })
      .then(response => response.json())
      .then(response => {
        if (response.code === 401 && response.message === "Expired JWT Token") {
          alert("Ihre Session ist abgelaufen, bitte loggen Sie sich erneut ein");
          router.push("/login");
        }
        checklists.value = response;
      });
});

// Filterlogik
const filteredChecklists = computed(() => {
  return checklists.value.filter((checklist) =>
      checklist.name.toLowerCase().includes(searchQuery.value.toLowerCase())
  );
});


function addChecklist() {
  router.push("/checklist/new");
}
</script>

<template>
  <div class="checklist-page">
    <div class="header-section">
      <div class="title-container">
        <h1
            @click="isGlobal = false"
            class="page-title"
            :class="{ active: !isGlobal }"
        >
          Meine Checklisten
        </h1>
        <span class="separator">|</span>
        <h1
            @click="isGlobal = true"
            class="page-title"
            :class="{ active: isGlobal }"
        >
          Alle Checklisten
        </h1>
      </div>
    </div>

    <div class="search-section">
      <div class="search-container-with-button">
        <div class="search-container">
          <input
              type="text"
              v-model="searchQuery"
              class="search-input"
              placeholder="Suchen Sie nach Checklisten ..."
          >
          <button class="search-button">
            <img :src="searchIcon" alt="Suchen">
          </button>
        </div>
        <button @click="addChecklist" class="main-button">+</button>
      </div>
    </div>





    <template v-if="filteredChecklists.length > 0">
      <div class="checklist-grid">
        <div
            v-for="checklist in filteredChecklists"
            :key="checklist.id"
            class="checklist-card"
            @click="router.push(`/checklist/${checklist.id}`)"
        >
          <h3 class="checklist-title">{{ checklist.name }}</h3>
          <div class="checklist-meta">
            <span class="stepsequence-count">{{ checklist.stepsequences.length }} Figuren</span>
            <span class="checklist-id">ID: {{ checklist.id }}</span>
          </div>
          <div v-if="isGlobal">Von: <span class="stepsequence-count">{{ checklist.user_id.name }}</span></div>
        </div>
      </div>
    </template>

    <template v-else>
      <div class="no-checklists-found">
        Es wurden keine Checklisten gefunden.
      </div>
    </template>
  </div>

</template>

<style scoped lang="scss">
.search-section {
  margin-bottom: 2rem;
}

.search-container {
  position: relative;
  max-width: 600px;
  margin: 0 auto;
}

.header-section {
  display: flex;
  flex-direction: row;
  justify-content: space-between;
}

.search-input {
  width: 100%;
  height: 3rem;
  padding: 0 3rem 0 1.5rem;
  border: 2px solid $colorVioletLight;
  border-radius: 1.5rem;
  font-size: 1rem;
  background-color: $colorWhite;
  transition: all 0.2s ease;

  &:focus {
    outline: none;
    border-color: $colorVioletDark;
    box-shadow: 0 0 0 3px rgba($colorVioletDark, 0.1);
  }

  &::placeholder {
    color: #999;
  }
}

.search-button {
  position: absolute;
  right: 1rem;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  cursor: pointer;
  padding: 0.5rem;
  display: flex;
  align-items: center;
  justify-content: center;

  img {
    width: 1.5rem;
    height: 1.5rem;
  }
}

.checklist-page {
  padding: 2rem;
  margin: 0 auto;
  background: linear-gradient(to bottom right, #f3e8ff, #fce7f3, #ffffff);
  min-height: 100vh;
}

.header-section {
  margin-bottom: 2rem;
  display: flex;
  justify-content: center;
  align-items: center;
}

.title-container {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.page-title {
  font-size: 2rem;
  color: $colorVioletDark;
  margin: 0;
  cursor: pointer;
  transition: all 0.2s ease;
  padding: 0.5rem 1rem;
  border-radius: 0.5rem;

  &:hover {
    background-color: rgba($colorVioletLight, 0.2);
  }

  &.active {
    color: $colorWhite;
    background-color: $colorVioletLight;
    box-shadow: 0 2px 2px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
  }
}

.separator {
  font-size: 2rem;
  color: $colorVioletDark;
  opacity: 0.5;
}

.checklist-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 2rem;
  padding: 1rem;
}

.checklist-card {
  background-color: $colorVioletLight;
  border-radius: 1rem;
  padding: 2rem;
  color: $colorWhite;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  min-height: 150px;

  &:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    background-color: $colorPurpleLight;
  }
}

.checklist-title {
  font-size: 1.5rem;
  margin: 0 0 1rem;
  font-weight: 600;
}

.checklist-meta {
  display: flex;
  justify-content: space-between;
  width: 100%;
  font-size: 0.9rem;
  opacity: 0.8;
  margin-top: auto;
}

.stepsequence-count {
  font-weight: 500;
}

.checklist-id {
  font-style: italic;
}

@media (max-width: 768px) {
  .checklist-page {
    padding: 1rem;
  }

  .checklist-grid {
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 1rem;
  }

  .checklist-card {
    padding: 1.5rem;
    min-height: 120px;
  }

  .page-title {
    font-size: 1.5rem;
  }

  .separator {
    font-size: 1.5rem;
  }
}

@media (max-width: 480px) {
  .checklist-grid {
    grid-template-columns: 1fr;
  }

  .page-title {
    font-size: 1.25rem;
    padding: 0.3rem 0.6rem;
  }

  .title-container {
    gap: 0.5rem;
  }

  .separator {
    font-size: 1.25rem;
  }
}

.no-checklists-found {
  text-align: center;
  font-size: 1.2rem;
  color: $colorVioletDark;
  opacity: 0.8;
  padding: 2rem;
  width: 100%;
}
.main-button {
  width: 3rem;
  height: 3rem;
  border-radius: 50%;
  background-color: $colorVioletLight;
  color: $colorWhite;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0;
  border: 2px solid $colorVioletDark;



  &:hover {
    background-color: $colorPurpleLight;
    transform: translateY(-1px);
  }
}
.button-wrapper {
  display: flex;
  justify-content: flex-end;
}
.search-container-with-button {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 1rem;
  max-width: 700px;
  margin: 0 auto;
}

.search-container {
  position: relative;
  flex-grow: 1;
}




</style>
