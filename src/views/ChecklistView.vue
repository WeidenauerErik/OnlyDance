<script setup lang="ts">
import playIcon from '@/assets/icons/playIcon.svg';
import filledFavoriteIcon from '@/assets/icons/filledHearthWhiteIcon.svg';
import {useAuthStore} from "@/stores/auth.ts";

const url = import.meta.env.VITE_ServerIP + "/user/get";
const auth = useAuthStore();
console.log(auth.token)

fetch(url, {
  headers: {
    "Authorization": "Bearer ${auth.token}",
  }
})
  .then(response => response.json())
  .then(response => console.log(response));

</script>

<template>
  <div id="checklistContainer">
    <div class="checklists">
      <p>Walzer</p>
      <div class="innerChecklistContainer">
        <img class="image-icons" :src="playIcon" alt="AbspielKnopf: leitet einen zu der Tanzanimation weiter">
        <img class="image-icons" :src="filledFavoriteIcon" alt="Herz Emoji zum Favorisieren">
      </div>
    </div>
  </div>
</template>

<style scoped lang="scss">
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