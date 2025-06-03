<script setup lang="ts">
import {nextTick, onMounted, ref, watch} from "vue";
import FootAnimationComponent from "@/components/FootAnimationComponent.vue";
import type {FootStep} from "@/tsTypes/interfacesDanceView.ts";

import playIcon from "@/assets/icons/playIcon.svg";
import pauseIcon from "@/assets/icons/pauseIcon.svg";

import Swal from 'sweetalert2';
import axios from "axios";
import {useAuthStore} from "@/stores/auth.ts";
const auth = useAuthStore();
onMounted(async () =>{
  await auth.checkAdmin();
  console.log( auth.isAdmin);
})

const ServerUrl = import.meta.env.VITE_ServerIP;

let steps = ref<FootStep[]>([]);
const autoplayActive = ref<boolean>(false);
const autoplayVariable = ref<string>(playIcon);

const currentStep = ref<FootStep | null>(null);
const loaderIsVisible = ref<boolean>(false);
const danceStepCounter = ref<number>(0);
const danceStepLength = ref<number>(1);
const danceName = ref<string>("Walzer");

const howQuick = ref<number>(1);

const m1_x = ref<number>(0.4);
const m1_y = ref<number>(0.6);
const m1_rotate = ref<number>(0);
const m1_toe = ref<boolean>(true);
const m1_heel = ref<boolean>(true);

const m2_x = ref<number>(0.6);
const m2_y = ref<number>(0.6);
const m2_rotate = ref<number>(0);
const m2_toe = ref<boolean>(true);
const m2_heel = ref<boolean>(true);

const w1_x = ref<number>(0.4);
const w1_y = ref<number>(0.3);
const w1_rotate = ref<number>(180);
const w1_toe = ref<boolean>(true);
const w1_heel = ref<boolean>(true);

const w2_x = ref<number>(0.6);
const w2_y = ref<number>(0.3);
const w2_rotate = ref<number>(180);
const w2_toe = ref<boolean>(true);
const w2_heel = ref<boolean>(true);

const sidebarVisible = ref(false);
const toggleSidebar = () => {
  sidebarVisible.value = !sidebarVisible.value;
};

const makeCurrentStep = (): FootStep => ({
  howquick: howQuick.value,
  m1_x: m1_x.value,
  m1_y: m1_y.value,
  m1_rotate: m1_rotate.value,
  m1_toe: m1_toe.value,
  m1_heel: m1_heel.value,
  m2_x: m2_x.value,
  m2_y: m2_y.value,
  m2_rotate: m2_rotate.value,
  m2_toe: m2_toe.value,
  m2_heel: m2_heel.value,
  w1_x: w1_x.value,
  w1_y: w1_y.value,
  w1_rotate: w1_rotate.value,
  w1_toe: w1_toe.value,
  w1_heel: w1_heel.value,
  w2_x: w2_x.value,
  w2_y: w2_y.value,
  w2_rotate: w2_rotate.value,
  w2_toe: w2_toe.value,
  w2_heel: w2_heel.value,
});

watch([
  howQuick, m1_x, m1_y, m1_rotate, m1_toe, m1_heel,
  m2_x, m2_y, m2_rotate, m2_toe, m2_heel,
  w1_x, w1_y, w1_rotate, w1_toe, w1_heel,
  w2_x, w2_y, w2_rotate, w2_toe, w2_heel
], () => {
  if (!steps.value[danceStepCounter.value]) return;
  currentStep.value = makeCurrentStep();
  steps.value[danceStepCounter.value] = makeCurrentStep();
});

onMounted(async () => {
  await nextTick();
  steps.value.push(makeCurrentStep());
  currentStep.value = makeCurrentStep();
});

const backToBeginBtn = () => {
  autoplayActive.value = false;
  danceStepCounter.value = 0;
  currentStep.value = steps.value[danceStepCounter.value];
  steps.value[danceStepCounter.value] = makeCurrentStep();
  refreshInputFields();
};

const backBtn = () => {
  autoplayActive.value = false;
  steps.value[danceStepCounter.value] = makeCurrentStep();
  if (danceStepCounter.value > 0) {
    danceStepCounter.value--;
    currentStep.value = steps.value[danceStepCounter.value];
  }
  refreshInputFields();
};

const nextBtn = () => {
  autoplayActive.value = false;
  steps.value[danceStepCounter.value] = makeCurrentStep();
  if (danceStepCounter.value < steps.value.length - 1) {
    danceStepCounter.value++;
    currentStep.value = steps.value[danceStepCounter.value];
  }
  refreshInputFields();
};

const AutoplayBtn = async () => {
  steps.value[danceStepCounter.value] = makeCurrentStep();
  if (danceStepCounter.value === steps.value.length - 1) {
    danceStepCounter.value = 0;
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
    await new Promise((resolve) => setTimeout(resolve, steps.value[danceStepCounter.value].howquick * 500));
  }

  autoplayVariable.value = playIcon;
  autoplayActive.value = false;
  refreshInputFields();
};

const backToEndBtn = () => {
  autoplayActive.value = false;
  danceStepCounter.value = steps.value.length - 1;
  currentStep.value = steps.value[danceStepCounter.value];
  steps.value[danceStepCounter.value] = makeCurrentStep();
  refreshInputFields();
};

const addStep = () => {
  const newStep = makeCurrentStep();
  steps.value.push(newStep);
  danceStepLength.value = steps.value.length;
  danceStepCounter.value = danceStepLength.value - 1;
  refreshInputFields();
};

const removeStep = async () => {
  if (steps.value.length > 1) {
    steps.value.splice(danceStepCounter.value, 1);
    danceStepLength.value = steps.value.length;
    danceStepCounter.value = Math.max(0, steps.value.length - 2);
    await nextTick();
    currentStep.value = steps.value[danceStepCounter.value];
    refreshInputFields();
  }
};

const refreshInputFields = () => {
  const step = steps.value[danceStepCounter.value];
  howQuick.value = step.howquick;
  m1_x.value = step.m1_x;
  m1_y.value = step.m1_y;
  m1_rotate.value = step.m1_rotate;
  m1_toe.value = step.m1_toe;
  m1_heel.value = step.m1_heel;
  m2_x.value = step.m2_x;
  m2_y.value = step.m2_y;
  m2_rotate.value = step.m2_rotate;
  m2_toe.value = step.m2_toe;
  m2_heel.value = step.m2_heel;
  w1_x.value = step.w1_x;
  w1_y.value = step.w1_y;
  w1_rotate.value = step.w1_rotate;
  w1_toe.value = step.w1_toe;
  w1_heel.value = step.w1_heel;
  w2_x.value = step.w2_x;
  w2_y.value = step.w2_y;
  w2_rotate.value = step.w2_rotate;
  w2_toe.value = step.w2_toe;
  w2_heel.value = step.w2_heel;
};

const loadData = async (url) => {
  const response = await fetch(ServerUrl + url);
  const data = await response.json();
  const dances = data.reduce((acc, item) => {
    acc[item.id] = item.name;
    return acc;
  }, {});
  return dances;
}

const saveDance = async () => {
  const difficulty = {
    0: "1",
    1: "2",
    2: "3",
    3: "4",
    4: "5"
  };

  let dances;
  let badges;

  try {
    dances = await loadData("/dance/dances");
    badges = await loadData("/badge/badges");
  } catch (e) {
    Swal.fire('Server wurde nicht gefunden!',
        '',
        'error');
  }

  const htmlForm = `
  <div style="display: flex; flex-direction: column; gap: 10px">
    <label>Zugehöriger Tanz:
      <select id="danceSelect" class="swal2-select" style="width: 70%; border: 1px solid #551167; border-radius: 10px">
        ${Object.entries(dances).map(([id, name]) => `<option value="${id}">${name}</option>`).join('')}
      </select>
    </label>

    <label>Name der Figur:
      <input type="text" id="nameInput" class="swal2-input" placeholder="Figurenname"
        style="width: 70%; border: 1px solid #551167; color: black; border-radius: 10px"/>
    </label>

    <label>Abzeichen:
      <select id="badgeSelect" class="swal2-select" style="width: 70%; border: 1px solid #551167; border-radius: 10px">
        ${Object.entries(badges).map(([id, name]) => `<option value="${id}">${name}</option>`).join('')}
      </select>
    </label>

    <label>Schwierigkeitsgrad:
      <select id="difficultySelect" class="swal2-select" style="width: 70%; border: 1px solid #551167; border-radius: 10px">
        ${Object.entries(difficulty).map(([id, name]) => `<option value="${id}">${name}</option>`).join('')}
      </select>
    </label>
  </div>
`;



  const result = await Swal.fire({
    title: 'Schrittfolge speichern',
    html: htmlForm,
    focusConfirm: false,
    confirmButtonText: 'Speichern',
    confirmButtonColor: '#551167',
    showCancelButton: true,
    preConfirm: () => {
      const danceId = (Swal.getPopup()?.querySelector('#danceSelect') as HTMLSelectElement)?.value;
      const name = (Swal.getPopup()?.querySelector('#nameInput') as HTMLInputElement)?.value.trim();
      const badgeId = (Swal.getPopup()?.querySelector('#badgeSelect') as HTMLSelectElement)?.value;
      const difficultyId = (Swal.getPopup()?.querySelector('#difficultySelect') as HTMLSelectElement)?.value;

      if (!name) {
        Swal.showValidationMessage('Bitte gib einen Namen ein!');
        return;
      }
      const nameRegex = /^[A-Za-zÄÖÜäöüß\s]+$/;
      if (!nameRegex.test(name)) {
        Swal.showValidationMessage('Der Name darf nur Buchstaben und Leerzeichen enthalten!');
        return;
      }


      return {
        danceId,
        name,
        badgeId,
        difficultyId,
      };
    }
  });

  if (!result.isConfirmed || !result.value) return;

  const {danceId, name, badgeId, difficultyId} = result.value;

  const output = {
    name,
    badge_id: badgeId,
    difficulty: difficultyId,
    dance_id: danceId,
    steps: steps.value,
  };

  const confirm = await Swal.fire({
    title: 'Stimmen die Daten überein?',
    html: `Zugehöriger Tanz: ${dances[danceId]}<br>
         Name: ${name}<br>
         Abzeichen: ${badges[badgeId]}<br>
         Schwierigkeit: ${difficulty[difficultyId]}`,
    icon: 'info',
    showCancelButton: true,
    confirmButtonText: 'Ja',
    confirmButtonColor: '#551167',
    cancelButtonText: 'Nein'
  });


  if (!confirm.isConfirmed) {
    await saveDance();
  } else {
    try {
      await axios.post(ServerUrl + '/stepsequence/add', output, {
        headers: {'Content-Type': 'application/json'}
      });
      Swal.fire('Erfolgreich gespeichert!', '', 'success');
    } catch (error) {
      console.error(error);
      Swal.fire('Fehler beim Speichern', error.message || 'Unbekannter Fehler', 'error');
    }
  }
}
</script>

<template>
  <button @click="toggleSidebar" class="sidebar-toggle-btn" v-if="!sidebarVisible">
    <span>⚙</span>
  </button>

  <FootAnimationComponent
      :loaderIsVisible="loaderIsVisible"
      :danceStepCounter="danceStepCounter"
      :danceStepLength="danceStepLength"
      :currentStep="currentStep || null"
      :autoplayVariable="autoplayVariable"
      :danceName="danceName"
      :isInEditMode="true"
      @backToBeginBtn="backToBeginBtn"
      @backBtn="backBtn"
      @AutoplayBtn="AutoplayBtn"
      @nextBtn="nextBtn"
      @backToEndBtn="backToEndBtn"
      @addStep="addStep"
      @removeStep="removeStep"
      @saveDance="saveDance"
  />

  <transition name="slide">
    <div v-if="sidebarVisible" class="sidebar">
      <div class="inputContainer">
        <button @click="toggleSidebar" class="main-button" v-if="sidebarVisible">
          Schließen
        </button>
        <label>howQuick: <input v-model="howQuick" type="number" step="0.1"/></label>

        <div class="inputContainerElements">
          <label class="inputContainerInnerElements">m1_x: <input v-model="m1_x" type="number" step="0.1" min="0"
                                                                  max="1"/></label>
          <label class="inputContainerInnerElements">m1_y: <input v-model="m1_y" type="number" step="0.1" min="0"
                                                                  max="1"/></label>
          <label class="inputContainerInnerElements">m1_rotate: <input v-model="m1_rotate" type="number" step="1"
                                                                       min="0" max="360"/></label>
          <label class="inputContainerInnerElements">m1_toe: <input v-model="m1_toe" type="checkbox"/></label>
          <label class="inputContainerInnerElements">m1_heel: <input v-model="m1_heel" type="checkbox"/></label>
        </div>

        <div class="inputContainerElements">
          <label class="inputContainerInnerElements">m2_x: <input v-model="m2_x" type="number" step="0.1" min="0"
                                                                  max="1"/></label>
          <label class="inputContainerInnerElements">m2_y: <input v-model="m2_y" type="number" step="0.1" min="0"
                                                                  max="1"/></label>
          <label class="inputContainerInnerElements">m2_rotate: <input v-model="m2_rotate" type="number" step="1"
                                                                       min="0" max="360"/></label>
          <label class="inputContainerInnerElements">m2_toe: <input v-model="m2_toe" type="checkbox"/></label>
          <label class="inputContainerInnerElements">m2_heel: <input v-model="m2_heel" type="checkbox"/></label>
        </div>

        <div class="inputContainerElements">
          <label class="inputContainerInnerElements">w1_x: <input v-model="w1_x" type="number" step="0.1" min="0"
                                                                  max="1"/></label>
          <label class="inputContainerInnerElements">w1_y: <input v-model="w1_y" type="number" step="0.1" min="0"
                                                                  max="1"/></label>
          <label class="inputContainerInnerElements">w1_rotate: <input v-model="w1_rotate" type="number" step="1"
                                                                       min="0" max="360"/></label>
          <label class="inputContainerInnerElements">w1_toe: <input v-model="w1_toe" type="checkbox"/></label>
          <label class="inputContainerInnerElements">w1_heel: <input v-model="w1_heel" type="checkbox"/></label>
        </div>

        <div class="inputContainerElements">
          <label class="inputContainerInnerElements">w2_x: <input v-model="w2_x" type="number" step="0.1" min="0"
                                                                  max="1"/></label>
          <label class="inputContainerInnerElements">w2_y: <input v-model="w2_y" type="number" step="0.1" min="0"
                                                                  max="1"/></label>
          <label class="inputContainerInnerElements">w2_rotate: <input v-model="w2_rotate" type="number" step="1"
                                                                       min="0" max="360"/></label>
          <label class="inputContainerInnerElements">w2_toe: <input v-model="w2_toe" type="checkbox"/></label>
          <label class="inputContainerInnerElements">w2_heel: <input v-model="w2_heel" type="checkbox"/></label>
        </div>
      </div>
    </div>
  </transition>
</template>

<style scoped lang="scss">
.main-button {
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

.sidebar-toggle-btn {
  position: fixed;
  right: 10px;
  top: 50%;
  transform: translateY(-50%);
  background-color: #551167;
  color: white;
  height: 3rem;
  width: 3rem;
  border: none;
  border-radius: 50%;
  padding: 15px 18px;
  cursor: pointer;
  z-index: 1001;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
  transition: background-color 0.3s ease;
  display: flex;
  justify-content: center;
  align-items: center;
}

.sidebar-toggle-btn:hover {
  background-color: #6f1b8e;
}

.sidebar {
  position: fixed;
  right: 0;
  top: 0;
  height: 100vh;
  width: 380px;
  background: #fdfdfd;
  border-left: 1px solid #551167;
  padding: 1.5rem;
  overflow-y: auto;
  z-index: 1000;
  box-shadow: -4px 0 12px rgba(0, 0, 0, 0.1);
}

.inputContainer {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.inputContainerElements {
  margin-top: 15px;
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.inputContainerInnerElements {
  display: flex;
  justify-content: space-between;
}

input {
  border: 1px solid $colorVioletLight;
  border-radius: 5px;
  padding-left: 10px;
}

.slide-enter-active, .slide-leave-active {
  transition: transform 0.3s ease;
}

.slide-enter-from, .slide-leave-to {
  transform: translateX(100%);
}
</style>