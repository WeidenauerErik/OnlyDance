<script setup lang="ts">
import {nextTick, onMounted, type Ref, ref, watch} from "vue";
import FootAnimationComponent from "@/components/FootAnimationComponent.vue";
import type {FootStep, } from "@/tsTypes/interfacesDanceView.ts";

import playIcon from "@/assets/icons/playIcon.svg";
import pauseIcon from "@/assets/icons/pauseIcon.svg";

let steps = ref<FootStep[]>([]);
const autoplayActive = ref<boolean>(false);

const autoplayVariable = ref<string>(playIcon);

//for properties
const currentStep = ref<FootStep | null>(null);
const loaderIsVisible = false;
const danceStepCounter = ref<number>(0);
const danceStepLength = ref<number>(1);
const danceName = ref<string>("Walzer");

const howQuick = ref<Number>(1);

const m1_x = ref<number>(0.4);
const m1_y = ref<number>(0.6)
const m1_rotate = ref<number>(0);
const m1_toe = ref<boolean>(true);
const m1_heel = ref<boolean>(true);

const m2_x = ref<number>(0.6);
const m2_y = ref<number>(0.6)
const m2_rotate = ref<number>(0);
const m2_toe = ref<boolean>(true);
const m2_heel = ref<boolean>(true);

const w1_x = ref<number>(0.4);
const w1_y = ref<number>(0.3)
const w1_rotate = ref<number>(180);
const w1_toe = ref<boolean>(true);
const w1_heel = ref<boolean>(true);

const w2_x = ref<number>(0.6);
const w2_y = ref<number>(0.3)
const w2_rotate = ref<number>(180);
const w2_toe = ref<boolean>(true);
const w2_heel = ref<boolean>(true);

const makeCurrentStep = (): FootStep => {
  return {
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
  };
};

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
    if (autoplayActive.value) {
      danceStepCounter.value++;
      currentStep.value = steps.value[danceStepCounter.value];
      await new Promise((resolve) => setTimeout(resolve, steps.value[danceStepCounter.value].howquick * 500));
    } else {
      return;
    }
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
  if (steps.value.length !== 1) {
    steps.value.splice(danceStepCounter.value,1);
    danceStepLength.value = steps.value.length;
    danceStepCounter.value = steps.value.length-2;
    await nextTick();
    currentStep.value = steps.value[danceStepCounter.value];
    refreshInputFields();
  }
}

const refreshInputFields = () => {
  howQuick.value = steps.value[danceStepCounter.value].howquick;
  m1_x.value = steps.value[danceStepCounter.value].m1_x;
  m1_y.value = steps.value[danceStepCounter.value].m1_y;
  m1_rotate.value = steps.value[danceStepCounter.value].m1_rotate;
  m1_toe.value = steps.value[danceStepCounter.value].m1_toe;
  m1_heel.value = steps.value[danceStepCounter.value].m1_heel;
  m2_x.value = steps.value[danceStepCounter.value].m2_x;
  m2_y.value = steps.value[danceStepCounter.value].m2_y;
  m2_rotate.value = steps.value[danceStepCounter.value].m2_rotate;
  m2_toe.value = steps.value[danceStepCounter.value].m2_toe;
  m2_heel.value = steps.value[danceStepCounter.value].m2_heel;
  w1_x.value = steps.value[danceStepCounter.value].w1_x;
  w1_y.value = steps.value[danceStepCounter.value].w1_y;
  w1_rotate.value = steps.value[danceStepCounter.value].w1_rotate;
  w1_toe.value = steps.value[danceStepCounter.value].w1_toe;
  w1_heel.value = steps.value[danceStepCounter.value].w1_heel;
  w2_x.value = steps.value[danceStepCounter.value].w2_x;
  w2_y.value = steps.value[danceStepCounter.value].w2_y;
  w2_rotate.value = steps.value[danceStepCounter.value].w2_rotate;
  w2_toe.value = steps.value[danceStepCounter.value].w2_toe;
  w2_heel.value = steps.value[danceStepCounter.value].w2_heel;
}


const submitJSON = () => {
  console.log(JSON.stringify(steps.value));
}
</script>

<template>
  <FootAnimationComponent :loaderIsVisible='loaderIsVisible'
                          :danceStepCounter='danceStepCounter'
                          :danceStepLength='danceStepLength'
                          :currentStep='currentStep || null'
                          :autoplayVariable='autoplayVariable'
                          :danceName='danceName'
                          :isInEditMode='true'
                          @backToBeginBtn="backToBeginBtn"
                          @backBtn="backBtn"
                          @AutoplayBtn="AutoplayBtn"
                          @nextBtn="nextBtn"
                          @backToEndBtn="backToEndBtn"
                          @addStep="addStep"
                          @removeStep="removeStep"
  />

  <div class="overlay">
    <label>howQuick: <input v-model="howQuick" type="number" step="0.1"/></label>
    <div id='inputContainer'>
      <div class="inputContainerElements">
        <label class="inputContainerInnerElements">m1_x: <input v-model="m1_x" type="number" step="0.1" min="0"
                                                                max="1"/></label>
        <label class="inputContainerInnerElements">m1_y: <input v-model="m1_y" type="number" step="0.1" min="0"
                                                                max="1"/></label>
        <label class="inputContainerInnerElements">m1_rotate: <input v-model="m1_rotate" type="number" step="1" min="0"
                                                                     max="360"/></label>
        <label class="inputContainerInnerElements">m1_toe: <input v-model="m1_toe" type="checkbox"/></label>
        <label class="inputContainerInnerElements">m1_heel: <input v-model="m1_heel" type="checkbox"/></label>
      </div>
      <div class="inputContainerElements">
        <label class="inputContainerInnerElements">m2_x: <input v-model="m2_x" type="number" step="0.1" min="0"
                                                                max="1"/></label>
        <label class="inputContainerInnerElements">m2_y: <input v-model="m2_y" type="number" step="0.1" min="0"
                                                                max="1"/></label>
        <label class="inputContainerInnerElements">m2_rotate: <input v-model="m2_rotate" type="number" step="1" min="0"
                                                                     max="360"/></label>
        <label class="inputContainerInnerElements">m2_toe: <input v-model="m2_toe" type="checkbox"/></label>
        <label class="inputContainerInnerElements">m2_heel: <input v-model="m2_heel" type="checkbox"/></label>
      </div>
      <div class="inputContainerElements">
        <label class="inputContainerInnerElements">w1_x: <input v-model="w1_x" type="number" step="0.1" min="0"
                                                                max="1"/></label>
        <label class="inputContainerInnerElements">w1_y: <input v-model="w1_y" type="number" step="0.1" min="0"
                                                                max="1"/></label>
        <label class="inputContainerInnerElements">w1_rotate: <input v-model="w1_rotate" type="number" step="1" min="0"
                                                                     max="360"/></label>
        <label class="inputContainerInnerElements">w1_toe: <input v-model="w1_toe" type="checkbox"/></label>
        <label class="inputContainerInnerElements">w1_heel: <input v-model="w1_heel" type="checkbox"/></label>
      </div>
      <div class="inputContainerElements">
        <label class="inputContainerInnerElements">w2_x: <input v-model="w2_x" type="number" step="0.1" min="0"
                                                                max="1"/></label>
        <label class="inputContainerInnerElements">w2_y: <input v-model="w2_y" type="number" step="0.1" min="0"
                                                                max="1"/></label>
        <label class="inputContainerInnerElements">w2_rotate: <input v-model="w2_rotate" type="number" step="1" min="0"
                                                                     max="360"/></label>
        <label class="inputContainerInnerElements">w2_toe: <input v-model="w2_toe" type="checkbox"/></label>
        <label class="inputContainerInnerElements">w2_heel: <input v-model="w2_heel" type="checkbox"/></label>
      </div>
    </div>
  </div>

  <button @click="submitJSON">Submit für Andi</button>
</template>

<style scoped>
#inputContainer {
  display: flex;
  flex-direction: row;
  justify-content: space-around;

  .inputContainerElements {
    display: flex;
    flex-direction: column;
    width: 17%;

    .inputContainerInnerElements {
      display: flex;
      justify-content: space-between;
    }
  }
}
</style>
