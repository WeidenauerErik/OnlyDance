<script setup lang="ts">
import playIcon from '@/assets/icons/playIcon.svg';
import filledFavoriteIcon from '@/assets/icons/filledHearthWhiteIcon.svg';
import {useAuthStore} from "@/stores/auth.ts";
import {type Ref, ref} from "vue";
import router from "@/router";

const props = defineProps({
  id: String //id of the checklist displayed
})
const url = import.meta.env.VITE_ServerIP + `/checklist/get/${props.id}`;
const url2 = import.meta.env.VITE_ServerIP + `/api/check`;
const auth = useAuthStore();
console.log(auth.token)
const checklist = ref({});
const user = ref({});
const isOwner = ref({});
fetch(url, {
  headers: {
    "Authorization": `Bearer ${auth.token}`,
  }
})
    .then(response => response.json())
    .then(response => checklist.value = response).then(() => {
  fetch(url2, {
    headers: {
      "Authorization": `Bearer ${auth.token}`,
    }
  })
      .then(response => response.json())
      .then(response => user.value = response).then(
      () => {

        if (user.value.user === checklist.value.user_id.email) {
          isOwner.value = true;
          console.log("he is the owner")
        } else {
          isOwner.value = false;
          console.log("he is not the owner")

        }
      }
  );
})

const isCopying = ref(false);
const copyError :  Ref<null, null> = ref(null);
const newChecklistName = ref('');
const showNameDialog = ref(false);
const copyCheckList = () => {
  showNameDialog.value = true;
};

const confirmCopy = () => {
  if (!newChecklistName.value.trim()) {
    copyError.value = 'Please enter a name for the copied checklist';
    return;
  }

  isCopying.value = true;
  copyError.value = null;

  const url = import.meta.env.VITE_ServerIP + '/checklist/clone';

  fetch(url, {
    method: 'POST',
    headers: {
      "Authorization": `Bearer ${auth.token}`,
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      checklist_id: props.id,
      name: newChecklistName.value // Send the new name to the backend
    })
  })
      .then(response => {
        if (!response.ok) {
          throw new Error('Failed to copy checklist');
        }
        return response.json();
      })
      .then(data => {
        console.log('Checklist copied successfully:', data);
        // Reset and close dialog
        newChecklistName.value = '';
        showNameDialog.value = false;
        // Redirect to checklists page after successful copy
        router.push('/checklists');
      })
      .catch(error => {
        console.error('Error copying checklist:', error);
        copyError.value = error.message;
      })
      .finally(() => {
        isCopying.value = false;
      });
};

const cancelCopy = () => {
  showNameDialog.value = false;
  newChecklistName.value = '';
  copyError.value = null;
};

const backbtn = () => {
  router.push("/checklists");
}
</script>

<template>
  <!--<router-link to="/checklists" class="main-button">Back</router-link>-->
  <div class="buttons" >
  <button @click="backbtn" class="main-button">Zurück zu den Checklisten</button>
    <button
      v-if="!isOwner"
      class="main-button"
      @click="copyCheckList"
      :disabled="isCopying"
  >
    {{ isCopying ? 'Copying...' : 'Diese Checkliste Kopieren' }}
  </button>
  <p v-if="copyError" class="error-message">{{ copyError }}</p>
  </div>
  <div id="checklistContainer">
    <div v-for="stepsequence in checklist.stepsequences" :key="stepsequence.id" class="checklists">
      <p>{{ stepsequence.name }}</p>
      <div class="innerChecklistContainer">
        <img class="image-icons" @click="router.push('/danceview/'+stepsequence.id)" :src="playIcon"
             alt="AbspielKnopf: leitet einen zu der Tanzanimation weiter">
        <img class="image-icons" :src="filledFavoriteIcon" alt="Herz Emoji zum Favorisieren">
      </div>
    </div>

  </div>
</template>

<style scoped lang="scss">

.buttons {
  display: flex;
  flex-direction: row;
  justify-content: space-between;
}
.main-button {
  margin-right: 20px;
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

.error-message {
  color: red;
  margin-top: 10px;
}
</style>