<script setup lang="ts">
import playIcon from '@/assets/icons/playIcon.svg';
import filledFavoriteIcon from '@/assets/icons/filledHearthWhiteIcon.svg';
import {useAuthStore} from "@/stores/auth.ts";
import {ref} from "vue";
import router from "@/router";

const props = defineProps({
  id : String //id of the checklist displayed
})
const url = import.meta.env.VITE_ServerIP + `/checklist/get/${props.id}`;
const auth = useAuthStore();
console.log(auth.token)
const checklist = ref({});
fetch(url, {
  headers: {
    "Authorization": `Bearer ${auth.token}`,
  }
})
  .then(response => response.json())
  .then(response => checklist.value = response);

const backbtn = () => {
  router.push("/checklists");
}
</script>

<template>
  <!--<router-link to="/checklists" class="main-button">Back</router-link>-->
  <button @click="backbtn" class="main-button">Zurück zu den Checklisten</button>
  <div id="checklistContainer">
    <div v-for="stepsequence in checklist.stepsequences" :key="stepsequence.id" class="checklists">
      <p>{{ stepsequence.name }}</p>
      <div class="innerChecklistContainer">
        <img class="image-icons" @click="router.push('/danceview/'+stepsequence.id)" :src="playIcon" alt="AbspielKnopf: leitet einen zu der Tanzanimation weiter">
        <img class="image-icons" :src="filledFavoriteIcon" alt="Herz Emoji zum Favorisieren">
      </div>
    </div>
  </div>
</template>

<style scoped lang="scss">
.main-button {
  margin-top: 20px;
  margin-left: 20px;
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 0.5rem;
  background-color: $colorVioletLight;
  color: $colorWhite;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;

  &:hover {
    background-color: $colorPurpleLight;
    transform: translateY(-1px);
  }
}
#checklistContainer {
  display: flex;
  flex-direction: column;
  min-height: 90vh;
  overflow: scroll;
  align-items: center;
  margin-top: 5px;
}

.checklists {
  width: 100%;
  border-radius: 30px;
  margin: 0.5rem;
}

.checklists {
  width: 95%;
  background-color: $colorPurpleLight;
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  align-items: center;
  padding: 1rem;
  color: $colorWhite;
}

.innerChecklistContainer {
  display: flex;
  flex-direction: row;
  gap: 2rem;
  justify-content: space-between;
}
.image-icons {
  width: 20px;
  height: 20px;
  cursor: pointer;
}
</style>