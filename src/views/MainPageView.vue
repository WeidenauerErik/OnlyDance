<script setup lang="ts">
import searchIcon from '@/assets/icons/searchIcon.svg';
import {type DanceTypes} from '@/tsTypes/interfacesMainPageView.ts';
import {nextTick, onMounted, ref, computed} from "vue";
import router from "@/router";
import goldIcon from '@/assets/icons/Gold.png';
import silberIcon from '@/assets/icons/Silber.png';
import bronzeIcon from '@/assets/icons/Bronze.png';

const url = import.meta.env.VITE_ServerIP + "/dance/dances";
const dances = ref<DanceTypes[]>([]);
const stepsequences = ref<[]>([]);
let filterStepsequences = ref<[]>([]);
const isFiltered = ref(false);
const isStepsequence = ref(false);
const searchQuery = ref('');
const activeFilter = ref<number | null>(null);

onMounted(() => {
  fetch(url)
      .then(response => response.json())
      .then((data: DanceTypes[]) => {
        dances.value = data;
        nextTick();
      });
});

const filteredDances = computed(() => {
  return dances.value.filter((dance) =>
      dance.name.toLowerCase().includes(searchQuery.value.toLowerCase())
  );
});

const filteredStepsequences = computed(() => {
  const list = isFiltered.value ? filterStepsequences.value : stepsequences.value;

  return list.filter((sequence) =>
      sequence.name.toLowerCase().includes(searchQuery.value.toLowerCase())
  );
});

const getStepsequencesFromDance = (danceId: number) => {
  fetch(import.meta.env.VITE_ServerIP + `/stepsequence/dance/${danceId}`)
      .then(response => response.json())
      .then((data) => {
        stepsequences.value = data;
        nextTick();
        isStepsequence.value = true;
      });
}

const getAllStepsequences = () => {
  fetch(import.meta.env.VITE_ServerIP + '/stepsequence/get')
      .then(response => response.json())
      .then((data) => {
        stepsequences.value = data;
        nextTick();
        isStepsequence.value = true;
      })
}

const openDanceView = (stepsequenceId: string) => {
  router.push(`/danceView/${stepsequenceId}`);
}

const filter = (badge: number) => {
  filterStepsequences.value = [];
  isFiltered.value = true;
  activeFilter.value = badge;

  for (let i = 0; i < stepsequences.value.length; i++) {
    if (stepsequences.value[i].badge.id === badge) {
      filterStepsequences.value.push(stepsequences.value[i]);
    }
  }
}

const resetFilters = () => {
  isFiltered.value = false;
  activeFilter.value = null;
}
</script>

<template>
  <div class="main-page">

    <!-- Header Part -->
    <div class="header-section">

      <!-- Back Button -->
      <button
          v-if="isStepsequence"
          @click="isStepsequence = false"
          class="main-button"
      >
        ← Zurück zu den Tänzen
      </button>
      <div v-else></div>

      <!-- Search Section -->
      <div class="search-section">
        <div class="search-container">
          <input v-if="isStepsequence"
              type="text"
              v-model="searchQuery"
              class="search-input"
              placeholder="Suchen Sie nach Figuren ..."
          >
          <input v-else
                 type="text"
                 v-model="searchQuery"
                 class="search-input"
                 placeholder="Suchen Sie nach Tänzen ..."
          >
          <button class="search-button">
            <img :src="searchIcon" alt="Suchen">
          </button>
        </div>
      </div>

      <!--Get all Stepsequences --->
      <div v-if="!isStepsequence">
        <button @click="getAllStepsequences" v-if="!isStepsequence" class="main-button">Alle Tanzschritte</button>
      </div>

      <!--Filters for the Stepsequences -->
      <div v-else id="filter-container">
        <div id="filter-buttons">
          <button class="filter-button" :class="{ active: activeFilter === 3 }" @click="filter(3)">
            <img :src="goldIcon" alt="Filterfunktion für Gold">
          </button>
          <button class="filter-button" :class="{ active: activeFilter === 2 }" @click="filter(2)">
            <img :src="silberIcon" alt="Filterfunktion für Silber">
          </button>
          <button class="filter-button" :class="{ active: activeFilter === 1 }" @click="filter(1)">
            <img :src="bronzeIcon" alt="Filterfunktion für Bronze">
          </button>
        </div>
        <button @click="resetFilters" id="reset-filter">Filter zurücksetzen</button>
      </div>
    </div>

    <!-- Dance Grid -->
    <div class="dance-grid">
      <!--Dances shown-->
      <template v-if="!isStepsequence">
        <div v-if="filteredDances.length !== 0"
            v-for="dance in filteredDances"
            :key="dance.id"
            class="dance-card"
            @click="getStepsequencesFromDance(dance.id)"
        >
          <h3 class="dance-title">{{ dance.name }}</h3>
          <div class="difficulty-indicator">
            <span
                v-for="index in dance.difficulty"
                :key="index"
                class="difficulty-star"
            >★</span>
          </div>
          <span class="bpm-indicator">{{ dance.defaultBPM }} BPM</span>
        </div>
        <span v-else>
          Es gibt keine Tänze nach diese Prompt oder sie werden noch geladen :)
        </span>
      </template>

      <!-- Stepsequences unfiltered -->
      <template v-else-if="isStepsequence && !isFiltered">
        <div

            v-for="stepsequence in filteredStepsequences"
            :key="stepsequence.id"
            class="dance-card"
            @click="openDanceView(stepsequence.id)"
        >
          <h3 class="dance-title">{{ stepsequence.name }}</h3>
          <div class="difficulty-indicator">
            <span
                v-for="index in stepsequence.difficulty"
                :key="index"
                class="difficulty-star"
            >★</span>
          </div>
          <span class="bpm-indicator">{{ stepsequence.dance.defaultBPM }} BPM</span>
          <span v-if="stepsequence.badge.name === 'Bronze'"><img :src="bronzeIcon" class="badges-icons"></span>
          <span v-else-if="stepsequence.badge.name === 'Silber'"><img :src="silberIcon" class="badges-icons"></span>
          <span v-else-if="stepsequence.badge.name === 'Gold'"><img :src="goldIcon" class="badges-icons"></span>
          <span v-else>{{ stepsequence.badge.name }}</span>
        </div>
      </template>

      <!-- Stepsequences filtered -->
      <template v-else>
        <div v-if="filteredStepsequences.length === 0">Es gibt keine Tanzschritte mit diesem Abzeichen.</div>
        <div
            v-if="filterStepsequences.length !== 0"
            v-for="stepsequence in filteredStepsequences"
            :key="stepsequence.id"
            class="dance-card"
            @click="openDanceView(stepsequence.id)"
        >
          <h3 class="dance-title">{{ stepsequence.name }}</h3>
          <div class="difficulty-indicator">
            <span
                v-for="index in stepsequence.difficulty"
                :key="index"
                class="difficulty-star"
            >★</span>
          </div>
          <span class="bpm-indicator">{{ stepsequence.dance.defaultBPM }} BPM</span>
          <span v-if="stepsequence.badge.name === 'Bronze'"><img :src="bronzeIcon" class="badges-icons"></span>
          <span v-else-if="stepsequence.badge.name === 'Silber'"><img :src="silberIcon" class="badges-icons"></span>
          <span v-else-if="stepsequence.badge.name === 'Gold'"><img :src="goldIcon" class="badges-icons"></span>
          <span v-else>{{ stepsequence.badge.name }}</span>
        </div>
        <span v-else>
          Es gibt keine Figuren mit diesem Prompt!
        </span>
      </template>
    </div>
  </div>
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

#reset-filter {
  margin-left: 20px;

  &:hover {
    text-decoration: underline;
  }
}

.main-page {
  padding: 2rem;
  margin: 0 auto;
  background: linear-gradient(to bottom right, #f3e8ff, #fce7f3, #ffffff);
  height: 100vh;
}

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
  border: 2px solid $color-purple-light;
  border-radius: 1.5rem;
  font-size: 1rem;
  color: $color-text-dark;
  background-color: $color-white;
  transition: all 0.2s ease;

  &:focus {
    outline: none;
    border-color: $color-purple-primary;
    box-shadow: 0 0 0 3px rgba($color-purple-primary, 0.1);
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

.main-button {
  margin-bottom: 2rem;
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 0.5rem;
  background-color: $color-purple-dark;
  color: $color-white;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;

  &:hover {
    background-color: $color-purple-primary;
    transform: translateY(-1px);
  }
}

.dance-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 2rem;
  padding: 1rem;
}

.dance-card {
  background-color: $color-purple-dark;
  border-radius: 1rem;
  padding: 2rem;
  color: $color-white;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 200px;
  text-align: center;

  &:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    background-color: $color-purple-primary;
  }
}

.dance-title {
  font-size: 1.5rem;
  margin: 0 0 1rem;
  font-weight: 600;
}

.difficulty-indicator {
  margin-bottom: 1rem;

  .difficulty-star {
    color: $color-white;
    font-size: 1.2rem;
    margin: 0 0.2rem;
  }
}

.bpm-indicator {
  font-size: 1.1rem;
  font-weight: 500;
  opacity: 0.9;
}

.badges-icons {
  width: 5rem;
  margin-top: 5px;
}

#filter-buttons {
  background-color: $color-purple-dark;
  box-shadow: none;
  height: 100%;
  padding: 0.5rem;
  border-radius: 20px;
  width: 15rem;
  filter: none;

  display: flex;
  flex-direction: row;
  justify-content: space-around;

  .filter-button {
    background: none;
    border: none;
    padding: 0;
    border-radius: 50%;

    display: flex;
    justify-content: center;
    align-items: center;

    &.active {
      border: 2px solid $color-purple-primary;
      border-radius: 50%;
      box-shadow: 0 0 5px rgba(138, 43, 226, 0.5);
    }

    img {
      margin: 10px;
      width: 3rem;
      transition: transform 0.2s ease;
    }

    &:hover img {
      transform: translateY(-1px);
    }
  }
}


#filter-container {
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  align-items: end;
}

@media (max-width: 768px) {
  .header-section {
    flex-direction: column;
    align-items: center;
  }

  .main-page {
    padding: 1rem;
  }

  .dance-grid {
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 1rem;
  }

  .dance-card {
    padding: 1.5rem;
    min-height: 180px;
  }

  .dance-title {
    font-size: 1.25rem;
  }
}

@media (max-width: 480px) {
  .search-input {
    height: 2.5rem;
    font-size: 0.9rem;
  }

  .dance-grid {
    grid-template-columns: 1fr;
  }
}

template {
  overflow: scroll;
}
</style>
