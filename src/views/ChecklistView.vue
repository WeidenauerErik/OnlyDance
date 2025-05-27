<script setup lang="ts">
import playIcon from '@/assets/icons/playIcon.svg';
import filledFavoriteIcon from '@/assets/icons/filledHearthWhiteIcon.svg';
import {useAuthStore} from "@/stores/auth.ts";
import {onMounted, ref} from "vue";
import type {FavoriteStepsequences} from "@/tsTypes/interfaceChecklistView";
import {useRouter} from "vue-router";
import Swal from "sweetalert2";
import axios from "axios";

const $router = useRouter();
const url = import.meta.env.VITE_ServerIP + "/checklist/user/get";
//const url = import.meta.env.VITE_ServerIP + "/checklist/get";
const auth = useAuthStore();
const FavoriteStepSequences = ref<FavoriteStepsequences[]>([]);

onMounted(() => {
  fetch(url, {
    headers: {
      "Authorization": `Bearer ${auth.token}`,
    }
  })
      .then(response => response.json())
      .then(response => {
        FavoriteStepSequences.value = response[0].stepsequences;
      });

});

const removeFavoriteStepsequence = async (id: number) => {
  const confirm = await Swal.fire({
    title: 'Willst du die Figur wirklich von deiner Favoritenliste entfernen?',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Ja',
    confirmButtonColor: '#551167',
    cancelButtonText: 'Nein'
  });

  if (confirm.isConfirmed) {
    console.log(id)
    //TODO function has to be implemented
  }
}
</script>

<template>
  <div id="checklistUpperContainer">
    <div v-if="FavoriteStepSequences.length === 0">Du hast noch keine Figuren favorisiert :)</div>
    <div v-else id="checklistContainer">
      <div class="checklists" v-for="step in FavoriteStepSequences">
        <p>{{ step.name }}</p>
        <div class="innerChecklistContainer">
          <img class="image-icons" @click="$router.push('danceview/'+step.id)" :src="playIcon" alt="AbspielKnopf: leitet einen zu der Tanzanimation weiter">
          <img class="image-icons" @click="removeFavoriteStepsequence(step.id)" :src="filledFavoriteIcon" alt="Herz Emoji zum Favorisieren">
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped lang="scss">
#checklistUpperContainer {
  min-height: 90vh;
  display: flex;
  justify-content: center;
  margin-top: 5px;
  width: 100%;
}
#checklistContainer {
  width: 100%;
  display: flex;
  flex-direction: column;
  overflow: scroll;
  align-items: center;
}

.checklists {
  border-radius: 30px;
  width: 95%;
  background-color: $colorPurpleLight;
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  align-items: center;
  padding: 1rem;
  margin-bottom: 0.5rem;
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