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
      if (!value.trim()) {
        return 'Bitte gib einen gültigen Namen ein';
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
const addStepsToChecklist = async () => {
  try {
    // Alle Stepsequences vom Server holen
    const response = await fetch(import.meta.env.VITE_ServerIP + '/stepsequence/get', {
      headers: {
        "Authorization": `Bearer ${auth.token}`,
      }
    });
    if (!response.ok) throw new Error("Fehler beim Laden der Figuren");
    const allSequences = await response.json();

    // IDs der bereits in der Checkliste enthaltenen Stepsequences
    const existingIds = new Set((checklist.value.stepsequences || []).map((s: any) => s.id));

    // Herausfiltern der neuen, noch nicht verwendeten Stepsequences
    const availableSequences = allSequences.filter((seq: any) => !existingIds.has(seq.id));

    if (availableSequences.length === 0) {
      await Swal.fire("Hinweis", "Alle verfügbaren Figuren sind bereits in dieser Checkliste enthalten.", "info");
      return;
    }

    // Custom Scrollbare HTML Liste
    const formHtml = `
      <div style="max-height:300px; overflow-y:auto; text-align:left;">
        ${availableSequences.map(seq => `
          <div style="margin-bottom:8px;">
            <input type="checkbox" id="seq-${seq.id}" value="${seq.id}" />
            <label for="seq-${seq.id}" style="margin-left: 6px;">${seq.name}</label>
          </div>
        `).join('')}
      </div>
    `;

    const { isConfirmed } = await Swal.fire({
      title: 'Figuren hinzufügen',
      html: `
        <form id="addStepsForm">${formHtml}</form>
      `,
      width: '40em',
      showCancelButton: true,
      confirmButtonText: 'Hinzufügen',
      cancelButtonText: 'Abbrechen',
      focusConfirm: false,
      preConfirm: () => {
        const checkboxes = document.querySelectorAll('#addStepsForm input[type="checkbox"]:checked');
        const selectedIds = Array.from(checkboxes).map(cb => Number((cb as HTMLInputElement).value));
        if (selectedIds.length === 0) {
          Swal.showValidationMessage('Bitte wähle mindestens eine Figur aus');
        }
        return selectedIds;
      }
    });

    if (isConfirmed) {
      const selectedCheckboxes = document.querySelectorAll('#addStepsForm input[type="checkbox"]:checked');
      const stepsequence_ids = Array.from(selectedCheckboxes).map(cb => Number((cb as HTMLInputElement).value));

      const res = await fetch(import.meta.env.VITE_ServerIP + '/checklist/add-steps', {
        method: 'POST',
        headers: {
          "Authorization": `Bearer ${auth.token}`,
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          checklist_id: props.id,
          stepsequence_ids
        })
      });

      if (!res.ok) throw new Error('Fehler beim Hinzufügen');

      await Swal.fire('Erfolg', 'Die Figuren wurden hinzugefügt.', 'success');

      // Reload checklist
      const updated = await fetch(url);
      checklist.value = await updated.json();
    }

  } catch (error) {
    console.error(error);
    Swal.fire("Fehler", "Es ist ein Fehler aufgetreten.", "error");
  }
};

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
    <button class="main-button" v-if="isOwner">Figuren Hinzufügen </button>



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