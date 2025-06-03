<script setup lang="ts">
import playIcon from '@/assets/icons/playIcon.svg';
import filledFavoriteIcon from '@/assets/icons/filledHearthWhiteIcon.svg';
import {useAuthStore} from "@/stores/auth.ts";
import {type Ref, ref} from "vue";
import router from "@/router";
import Swal from 'sweetalert2';


const props = defineProps({
  id: String //id of the checklist displayed
})
const url = import.meta.env.VITE_ServerIP + `/checklist/get/${props.id}`;
const url2 = import.meta.env.VITE_ServerIP + `/api/check`;
const urlStepsequences = import.meta.env.VITE_ServerIP + `/stepsequence/get` ;
const auth = useAuthStore();
console.log(auth.token)
const checklist = ref({});
const user = ref({});
const isOwner = ref(false);

fetch(url, {

})
    .then(response => response.json())
    .then(response => checklist.value = response).then(() => {
      if(auth.isAuthenticated) {
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
      }else {
        isOwner.value = false;
      }
})

const isCopying = ref(false);
const copyError :  Ref<null, null> = ref(null);
const newChecklistName = ref('');
const showNameDialog = ref(false);
const copyCheckList = async () => {
  const { value: checklistName } = await Swal.fire({
    title: 'Checkliste kopieren',
    input: 'text',
    inputLabel: 'Neuer Name der Checkliste',
    inputPlaceholder: 'Gib einen Namen ein',
    showCancelButton: true,
    confirmButtonText: 'Kopieren',
    cancelButtonText: 'Abbrechen',
    inputValidator: (value) => {
      const trimmed = value.trim();
      if (!trimmed) {
        return 'Bitte gib einen gültigen Namen ein';
      }
      if (!/^[a-zA-Z0-9 ]+$/.test(trimmed)) {
        return 'Nur Buchstaben und Zahlen erlaubt (keine Sonderzeichen)';
      }
    }
  });

  if (!checklistName) {
    return; // Abgebrochen
  }

  isCopying.value = true;

  const urlClone = import.meta.env.VITE_ServerIP + '/checklist/clone';

  try {
    const response = await fetch(urlClone, {
      method: 'POST',
      headers: {
        "Authorization": `Bearer ${auth.token}`,
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        checklist_id: props.id,
        name: checklistName
      })
    });

    if (!response.ok) {
      throw new Error('Fehler beim Kopieren der Checkliste');
    }

    const data = await response.json();
    console.log('Checkliste erfolgreich kopiert:', data);

    await Swal.fire({
      icon: 'success',
      title: 'Erfolg',
      text: 'Die Checkliste wurde erfolgreich kopiert!',
    });

    router.push('/checklists');
  } catch (error: any) {
    console.error(error);
    Swal.fire({
      icon: 'error',
      title: 'Fehler',
      text: error.message || 'Unbekannter Fehler beim Kopieren',
    });
  } finally {
    isCopying.value = false;
  }
};



const cancelCopy = () => {
  showNameDialog.value = false;
  newChecklistName.value = '';
  copyError.value = null;
};

const backbtn = () => {
  router.push("/checklists");
}

const availableSequences = ref([]);

const addStepsToChecklist = async () => {
  try {
    const response = await fetch(urlStepsequences);
    if (!response.ok) throw new Error("Fehler beim Laden der Figuren");
    const allSequences = await response.json();

    const existingIds = new Set((checklist.value.stepsequences || []).map((s: any) => s.id));

     availableSequences.value = allSequences.filter((seq: any) => !existingIds.has(seq.id));

    console.log(existingIds);
    console.log(availableSequences.value);

  }
  catch (error) {
    console.error(error);
  }
}


async function removeStepsequence(stepsequence_id: number) {
  const result = await Swal.fire({
    title: 'Bist du sicher?',
    text: 'Möchtest du diese Figur wirklich aus der Checkliste entfernen?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Ja, entfernen',
    cancelButtonText: 'Abbrechen',
  });

  if (result.isConfirmed) {
    try {
      const response = await fetch(import.meta.env.VITE_ServerIP + '/checklist/remove/stepsequence', {
        method: 'POST',
        headers: {
          "Authorization": `Bearer ${auth.token}`,
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          checklist_id: props.id,
          stepsequence_id: stepsequence_id
        })
      });

      if (!response.ok) {
        throw new Error('Fehler beim Entfernen der Figur');
      }

      // Checkliste aktualisieren
      const updated = await fetch(url);
      checklist.value = await updated.json();

      await Swal.fire('Entfernt', 'Die Figur wurde entfernt.', 'success');
    } catch (error) {
      console.error(error);
      await Swal.fire('Fehler', 'Beim Entfernen der Figur ist ein Fehler aufgetreten.', 'error');
    }
  }
}

const showAddSequencesModal = ref(false);
const selectedSequences = ref<number[]>([]);
const isSubmitting = ref(false);

const openAddSequencesModal = async () => {
  await addStepsToChecklist(); // Lädt verfügbare Sequenzen
  showAddSequencesModal.value = true;
};

const closeAddSequencesModal = () => {
  showAddSequencesModal.value = false;
  selectedSequences.value = [];
};

const handleSubmit = async () => {
  if(selectedSequences.value.length === 0) {
    await Swal.fire('Keine Figur ausgewählt', 'Bitte mindestens eine Figur auswählen', 'warning');
    return;
  }
  isSubmitting.value = true;
  try {
    console.log(selectedSequences.value);
    const response = await fetch(import.meta.env.VITE_ServerIP + '/checklist/add/stepsequences', {
      method: 'POST',
      headers: {
        "Authorization": `Bearer ${auth.token}`,
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        checklist_id: props.id,
        stepsequences: selectedSequences.value,
      })
    });

    if (!response.ok) {
      throw new Error("Fehler beim Hinzufügen der Figuren");
    }

    checklist.value = await (await fetch(url)).json();
    closeAddSequencesModal();
    await Swal.fire('Erfolg', 'Figuren wurden hinzugefügt!', 'success');
  } catch (error: any) {
    console.error(error);
    await Swal.fire('Fehler', error.message || 'Fehler beim Hinzufügen', 'error');
  } finally {
    isSubmitting.value = false;
  }
};


</script>

<template>
  <!--<router-link to="/checklists" class="main-button">Back</router-link>-->
  <div class="buttons" >
  <button @click="backbtn" class="main-button">Zurück zu den Checklisten</button>
    <button
      v-if="!isOwner&&auth.isAuthenticated"
      class="main-button"
      @click="copyCheckList"
      :disabled="isCopying"
  >
      Diese Checkliste Kopieren

  </button>
  <p v-if="copyError" class="error-message">{{ copyError }}</p>
  </div>
  <div id="checklistContainer">
    <div v-for="stepsequence in checklist.stepsequences" :key="stepsequence.id" class="checklists">
      <p>{{ stepsequence.name }}</p>
      <div class="innerChecklistContainer">
        <img class="image-icons" @click="router.push('/danceview/'+stepsequence.id)" :src="playIcon"
             alt="AbspielKnopf: leitet einen zu der Tanzanimation weiter">
        <img class="image-icons" v-if="isOwner" :src="filledFavoriteIcon" @click="removeStepsequence(stepsequence.id)" alt="Herz Emoji zum Favorisieren">
      </div>
    </div>
    <button class="main-button" @click="openAddSequencesModal" v-if="isOwner">Figuren Hinzufügen </button>



  </div>

  <div v-if="showAddSequencesModal" class="modal-overlay">
    <div class="modal-content">
      <h1 class="title">Figuren hinzufügen</h1>

      <form @submit.prevent="handleSubmit" class="checklist-form">
        <label for="sequences">Figuren auswählen:</label>
        <div class="sequence-list">
          <div v-for="sequence in availableSequences" :key="sequence.id" class="sequence-item">
            <input type="checkbox" :id="sequence.id" :value="sequence.id" v-model="selectedSequences" />
            <label :for="sequence.id">{{ sequence.name }}</label>
          </div>
        </div>

        <div class="modal-buttons">
          <button type="submit" :disabled="isSubmitting" class="main-button">
            {{ isSubmitting ? 'Wird hinzugefügt...' : 'Hinzufügen' }}
          </button>
          <button type="button" class="cancel-button" @click="closeAddSequencesModal">Abbrechen</button>
        </div>
      </form>
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
.add-checklist-page {
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  background: linear-gradient(to bottom right, #f3e8ff, #fce7f3, #ffffff);
}

.form-section {
  width: 100%;
  max-width: 500px;
  background-color: $colorWhite;
  border-radius: 1.5rem;
  padding: 2rem;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

.title {
  font-size: 2rem;
  color: $colorPurpleLight;
  margin-bottom: 1.5rem;
  text-align: center;
}

.checklist-form {
  display: flex;
  flex-direction: column;
  gap: 1rem;

  input[type="text"] {
    padding: 0.75rem;
    border-radius: 9999px;
    border: 1px solid #d1d5db;
    font-size: 1rem;
    outline: none;

    &:focus {
      border-color: $colorPurpleLight;
    }
  }

  .sequence-list {
    display: flex;
    flex-direction: column;
    max-height: 200px;
    overflow-y: auto;
    padding: 0.5rem;
    border: 1px solid #ccc;
    border-radius: 0.75rem;
    background-color: #fafafa;
  }

  .sequence-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
  }

  .submit-button {
    background-color: $colorVioletLight;
    color: white;
    font-weight: bold;
    padding: 0.75rem;
    border-radius: 9999px;
    border: none;
    cursor: pointer;

    &:hover {
      background-color: $colorPurpleLight;
    }

    &:disabled {
      background-color: grey;
      cursor: not-allowed;
    }
  }
  .error-text{
    color: $colorVioletLight;
    font-size: 0.75rem;
  }
}
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-content {
  background-color: white;
  padding: 2rem;
  border-radius: 1rem;
  max-width: 500px;
  width: 90%;
  box-shadow: 0 5px 15px rgba(0,0,0,0.3);
}

.modal-buttons {
  display: flex;
  justify-content: space-between;
  margin-top: 1rem;
}

.cancel-button {
  background-color: #ddd;
  color: #333;
  margin-right: 20px;
  margin-top: 20px;
  margin-left: 20px;
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 0.5rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;

  &:hover {
    background-color: #bbb;
    transform: translateY(-1px);
  }
}

</style>