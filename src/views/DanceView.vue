<script setup lang="ts">
import FootAnimationComponent from "@/components/FootAnimationComponent.vue";
import {nextTick, onMounted, ref} from "vue";
import type {FootStep, Step} from "@/tsTypes/interfacesDanceView.ts";

import playIcon from "@/assets/icons/playIcon.svg";
import pauseIcon from "@/assets/icons/pauseIcon.svg";
import {useAuthStore} from "@/stores/auth.ts";
import Swal from "sweetalert2";

const props = defineProps({
  id: String
});

//url for fetching for stepsequences
const url = import.meta.env.VITE_ServerIP + `/stepsequence/get/${props.id}`

const steps = ref<Step>();
const autoplayActive = ref<boolean>(false);

const autoplayVariable = ref<string>(playIcon);

//for properties
let currentStep = ref<FootStep | null>(null);
let loaderIsVisible = ref<boolean>(true);
const danceStepCounter = ref<number>(0);
const danceStepLength = ref<number>(0);
const danceName = ref<string>("");

onMounted(() => {
  fetch(url)
      .then((res) => res.json())
      .then((data: Step) => {
        steps.value = data.steps;
        danceStepLength.value = data.dance.tact.split("/")[0];
        danceStepCounter.value = 0;
        currentStep.value = data.steps[danceStepCounter.value];
        danceName.value = data.name;
      })
      .then(async () => {
        await nextTick();
        loaderIsVisible.value = false;
        await nextTick();
      });
});

const backToBeginBtn = () => {
  autoplayActive.value = false;
  danceStepCounter.value = 0;
  currentStep.value = steps.value[danceStepCounter.value];
};

const backBtn = () => {
  autoplayActive.value = false;

  if (danceStepCounter.value > 0) {
    danceStepCounter.value--;
    currentStep.value = steps.value[danceStepCounter.value];
  }
};

const nextBtn = () => {
  autoplayActive.value = false;

  if (danceStepCounter.value < steps.value.length - 1) {
    danceStepCounter.value++;
    currentStep.value = steps.value[danceStepCounter.value];
  }
};

const AutoplayBtn = async () => {
  if (danceStepCounter.value === steps.value.length - 1) {
    danceStepCounter.value = 0;
    currentStep.value = steps.value[danceStepCounter.value];
    await nextTick();
  }

  if (autoplayActive.value) {
    autoplayVariable.value = playIcon;
    autoplayActive.value = false;
    return;
  }

  autoplayVariable.value = pauseIcon;
  autoplayActive.value = true;

  while (danceStepCounter.value < steps.value.length - 1 && autoplayActive.value) {
    danceStepCounter.value++;
    currentStep.value = steps.value[danceStepCounter.value];
    await nextTick();
    const delay = steps.value[danceStepCounter.value].howquick * 500;
    await new Promise((resolve) => setTimeout(resolve, delay));
  }

  autoplayVariable.value = playIcon;
  autoplayActive.value = false;
};

const backToEndBtn = () => {
  autoplayActive.value = false;
  danceStepCounter.value = steps.value.length - 1;
  currentStep.value = steps.value[danceStepCounter.value];
};


//Popup Funktionalität

const auth = useAuthStore();

const showAddToChecklistModal = ref(false);

const availableChecklists = ref([]);
const selectedChecklist = ref();
const isSubmitting = ref(false);


const triggerPopUp = async () => {
  availableChecklists.value = [];

  const response = await fetch(import.meta.env.VITE_ServerIP + "/checklist/user/get", {headers: {"Authorization": `Bearer ${auth.token}`}})
  const data = await response.json();

  availableChecklists.value = data.filter(checklist => !checklist.stepsequences.map(stepsequence => stepsequence.id).includes(Number(props.id))
  );
  showAddToChecklistModal.value = true;


}

const handleSubmit = async () => {
  if (!selectedChecklist.value) {
    await Swal.fire("Fehler", "Bitte wähle eine gültige Checkliste aus.", "warning");
    return;
  }

  isSubmitting.value = true;

  try {
    const response = await fetch(import.meta.env.VITE_ServerIP + "/checklist/add/stepsequence", {
      method: "POST",
      headers: {
        "Authorization": `Bearer ${auth.token}`,
        "Content-Type": "application/json"
      },
      body: JSON.stringify({
        checklist_id: selectedChecklist.value,
        stepsequence_id: props.id
      })
    });

    await Swal.fire("Erfolg", "Die Figur wurde erfolgreich zur Checkliste hinzugefügt!", "success");
    showAddToChecklistModal.value = false;

  } catch (error: any) {
    await Swal.fire("Fehler", error.message || "Ein unbekannter Fehler ist aufgetreten.", "error");

  } finally {
    isSubmitting.value = false;
  }

  selectedChecklist.value = undefined;
};


const closeAddSequencesModal = () => {
  showAddToChecklistModal.value = false;
  selectedChecklist.value = undefined;
};

</script>

<template>
  <FootAnimationComponent
      :loaderIsVisible='loaderIsVisible'
      :danceStepCounter='danceStepCounter'
      :danceStepLength='danceStepLength'
      :currentStep='currentStep || null'
      :autoplayVariable='autoplayVariable'
      :danceName='danceName'
      :isInEditMode='false'
      :isLoggedIn='auth.isAuthenticated'
      @backToBeginBtn="backToBeginBtn"
      @backBtn="backBtn"
      @AutoplayBtn="AutoplayBtn"
      @nextBtn="nextBtn"
      @backToEndBtn="backToEndBtn"
      @popUpBtn="triggerPopUp"/>


  <div v-if="showAddToChecklistModal" class="modal-overlay">
    <div class="modal-content">
      <h1 class="title">Figur zu Checkliste hinzufügen</h1>

      <form  @submit.prevent="handleSubmit" class="checklist-form">
        <label for="sequences">Checkliste auswählen:</label>
        <div v-if="availableChecklists.length > 0" class="sequence-list">
          <select v-model="selectedChecklist" class="sequence-item">
            <option v-for="checklist in availableChecklists" :key="checklist.id" :value="checklist.id">
              {{ checklist.name }}
            </option>
          </select>
        </div>
        <div  v-else>
          <h2>Keine verfügbaren Checklisten gefunden</h2>
        </div>



        <div class="modal-buttons">
          <button type="submit" :disabled="isSubmitting || availableChecklists.length < 1 || !selectedChecklist" class="main-button">
            {{ isSubmitting ? 'Wird hinzugefügt...' : 'Hinzufügen' }}
          </button>
          <button type="button" class="cancel-button" @click="closeAddSequencesModal">Abbrechen</button>
        </div>
      </form>

    </div>
  </div>


</template>
<style lang="scss">

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
  &:disabled {
    background-color: grey;
    cursor: not-allowed;
  }
}

.error-message {
  color: red;
  margin-top: 10px;
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
  align-items: center;
  justify-content: center;
  gap: 1rem;

  label {
    font-weight: 500;
    color: $colorPurpleLight;
  }

  select.sequence-item  {
    padding: 0.75rem 1rem;
    border-radius: 0.75rem;
    border: 2px solid $colorPurpleLight;
    min-width: 20vw;
    width: 100%;
    background-color: white;
    font-size: 1rem;
    font-weight: 500;
    color: $colorPurpleLight;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
    background-position: right 1rem center;
    background-size: 1rem;

    &:hover {
      border-color: darken($colorPurpleLight, 10%);

    }

    &:focus {
      border-color: darken($colorPurpleLight, 15%);
      outline: none;
      box-shadow: 0 0 0 3px rgba(149, 76, 233, 0.2);

    }


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

  .error-text {
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
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
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
