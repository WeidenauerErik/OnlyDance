<script setup lang="ts">
import {ref, watch, type Ref, onMounted, nextTick, onUnmounted, onUpdated} from 'vue';
import {
  type FootStep,
  type Step,
  type FootAnimationProps,
  type FootAnimationEmits
} from "@/tsTypes/interfacesDanceView.ts";
import router from "@/router";

import skipLeftIcon from '@/assets/icons/skipLeftIcon.svg';
import skipRightIcon from '@/assets/icons/skipRightIcon.svg';
import arrowLeftIcon from '@/assets/icons/arrowLeftIcon.svg';
import arrowRightIcon from '@/assets/icons/arrowRightIcon.svg';
import settingsIcon from '@/assets/icons/settingsIcon.svg';
import {useRouter} from "vue-router";

const props = defineProps<FootAnimationProps>();
const emit = defineEmits<FootAnimationEmits>();

const stepSequenceName = ref<string>('');

const footHeightDifferenz = 55;
const footWidthDifferenz = 25;

const morphDivRef = ref<HTMLDivElement | null>(null);
let screenWidth: number;
let screenHeight: number;

const howQuick = ref<number | null>(null);

const manLeftFoot = ref<HTMLElement | null>(null);
const manRightFoot = ref<HTMLElement | null>(null);
const womanLeftFoot = ref<HTMLElement | null>(null);
const womanRightFoot = ref<HTMLElement | null>(null);

const manLeftFootToes = ref<HTMLElement | null>(null);
const manLeftFootHeel = ref<HTMLElement | null>(null);

const manRightFootToes = ref<HTMLElement | null>(null);
const manRightFootHeel = ref<HTMLElement | null>(null);

const womanLeftFootToes = ref<HTMLElement | null>(null);
const womanLeftFootHeel = ref<HTMLElement | null>(null);

const womanRightFootToes = ref<HTMLElement | null>(null);
const womanRightFootHeel = ref<HTMLElement | null>(null);

const updateFeet = (step: FootStep) => {
  howQuick.value = step.howquick;

  if (manLeftFoot.value && manLeftFootHeel.value && manLeftFootToes.value) {
    manLeftFoot.value.style.transition = `top ${step.howquick}s ease, left ${step.howquick}s ease, transform ${step.howquick}s ease`;
    manLeftFoot.value.style.top = `${screenHeight * step.m1_y - footHeightDifferenz}px`;
    manLeftFoot.value.style.left = `${screenWidth * step.m1_x - footWidthDifferenz}px`;
    manLeftFoot.value.style.transform = `rotate(${step.m1_rotate}deg)`;
    manLeftFootToes.value.style.backgroundColor = step.m1_toe ? '#73168C' : '#875595';
    manLeftFootHeel.value.style.backgroundColor = step.m1_heel ? '#73168C' : '#875595';
  }

  if (manRightFoot.value && manRightFootHeel.value && manRightFootToes.value) {
    manRightFoot.value.style.transition = `top ${step.howquick}s ease, left ${step.howquick}s ease, transform ${step.howquick}s ease`;
    manRightFoot.value.style.top = `${screenHeight * step.m2_y - footHeightDifferenz}px`;
    manRightFoot.value.style.left = `${screenWidth * step.m2_x - footWidthDifferenz}px`;
    manRightFoot.value.style.transform = `rotate(${step.m2_rotate}deg)`;
    manRightFootToes.value.style.backgroundColor = step.m2_toe ? '#73168C' : '#875595';
    manRightFootHeel.value.style.backgroundColor = step.m2_heel ? '#73168C' : '#875595';
  }

  if (womanLeftFoot.value && womanLeftFootHeel.value && womanLeftFootToes.value) {
    womanLeftFoot.value.style.transition = `top ${step.howquick}s ease, left ${step.howquick}s ease, transform ${step.howquick}s ease`;
    womanLeftFoot.value.style.top = `${screenHeight * step.w1_y - footHeightDifferenz}px`;
    womanLeftFoot.value.style.left = `${screenWidth * step.w1_x - footWidthDifferenz}px`;
    womanLeftFoot.value.style.transform = `rotate(${step.w1_rotate}deg)`;
    womanLeftFootToes.value.style.backgroundColor = step.w1_toe ? '#73168C' : '#875595';
    womanLeftFootHeel.value.style.backgroundColor = step.w1_heel ? '#73168C' : '#875595';
  }

  if (womanRightFoot.value && womanRightFootHeel.value && womanRightFootToes.value) {
    womanRightFoot.value.style.transition = `top ${step.howquick}s ease, left ${step.howquick}s ease, transform ${step.howquick}s ease`;
    womanRightFoot.value.style.top = `${screenHeight * step.w2_y - footHeightDifferenz}px`;
    womanRightFoot.value.style.left = `${screenWidth * step.w2_x - footWidthDifferenz}px`;
    womanRightFoot.value.style.transform = `rotate(${step.w2_rotate}deg)`;
    womanRightFootToes.value.style.backgroundColor = step.w2_toe ? '#73168C' : '#875595';
    womanRightFootHeel.value.style.backgroundColor = step.w2_heel ? '#73168C' : '#875595';
  }
};

const resize = () => {
  if (morphDivRef.value) {
    screenWidth = morphDivRef.value.offsetWidth;
    screenHeight = morphDivRef.value.offsetHeight;
    if (props.currentStep !== null) updateFeet(props.currentStep);
  }
};

onUpdated(async () => {
  await nextTick();
  resize();
  window.addEventListener('resize', resize);
});

onUnmounted(() => {
  window.removeEventListener('resize', resize);
});
</script>

<template>
  {{ stepSequenceName }}
  <span v-if="props.loaderIsVisible">Loading ...</span>

  <div id="morphDiv" ref="morphDivRef" v-if="!props.loaderIsVisible">

    <div id="infoCounterDisplayCounter">
      <div>
        <button class="main-button" @click="router.go(-1)" v-if="!props.isInEditMode">Zurück zum Menü</button>
        <button class="main-button" @click="router.go(-1)" v-else>Zurück zum Menü</button>
      </div>

      <span id="infoCounterDisplay" v-if="!props.isInEditMode">

        {{ props.danceStepCounter === 0
          ? 0
          : ((props.danceStepCounter - 1) % parseInt(props.danceStepLength)) + 1
        }}
        /
        {{ props.danceStepLength }}
      </span>
      <span id="infoCounterDisplay" v-else> {{ props.danceStepCounter + 1 }} / {{ props.danceStepLength }}</span>


      <div>
        <button @click="$emit('saveDance')" class="main-button" v-if="props.isInEditMode" style="margin-right:5px;">Speichern</button>
      </div>
    </div>

    <div id="manLeftFoot" class="foot" ref="manLeftFoot">
      <div id="manLeftFootToes" class="manToe" ref="manLeftFootToes">
        <span id="manLeftFootLetter" class="footLetter">L1</span>
      </div>
      <div class="innerFootSpacer"></div>
      <div id="manLeftFootHeel" class="manHeel" ref="manLeftFootHeel"></div>
    </div>

    <div id="manRightFoot" class="foot" ref="manRightFoot">
      <div id="manRightFootToes" class="manToe" ref="manRightFootToes">
        <span id="manRightFootLetter" class="footLetter">R1</span>
      </div>
      <div class="innerFootSpacer"></div>
      <div id="manRightFootHeel" class="manHeel" ref="manRightFootHeel"></div>
    </div>

    <div id="womanLeftFoot" class="foot" ref="womanLeftFoot">
      <div id="womanLeftFootToes" class="womanToe" ref="womanLeftFootToes">
        <span id="womanLeftFootLetter" class="footLetter">L2</span>
      </div>
      <div class="innerFootSpacer"></div>
      <div class="womanHeelContainer">
        <div id="womanLeftFootHeel" class="womanHeel" ref="womanLeftFootHeel"></div>
      </div>
    </div>

    <div id="womanRightFoot" class="foot" ref="womanRightFoot">
      <div id="womanRightFootToes" class="womanToe" ref="womanRightFootToes">
        <span id="womanRightFootLetter" class="footLetter">R2</span>
      </div>
      <div class="innerFootSpacer"></div>
      <div class="womanHeelContainer">
        <div id="womanRightFootHeel" class="womanHeel" ref="womanRightFootHeel"></div>
      </div>
    </div>
  </div>


  <div id="controlsMainContainer" v-if="!props.loaderIsVisible">

    <div class="controlsContainerElement">

      <h1 id="infoTextDisplay" v-if="!props.isInEditMode"> {{ props.danceName }}</h1>

    </div>

    <div id="controlsContainer" class="controlsContainerElement">

      <button id="nextButton" class="controlsElement" @click="$emit('backToBeginBtn')">

        <img :src="skipLeftIcon" alt="Zurück zum Anfang">

      </button>

      <button id="backButton" class="controlsElement" @click="$emit('backBtn')">

        <img :src="arrowLeftIcon" alt="Einen Schritt nach vorne">

      </button>

      <button id="autoplayButton" class="controlsElement" @click="$emit('autoplayBtn')">

        <img :src="autoplayVariable" alt="Autoplay Funktion" id="autoplayImage">

      </button>

      <button id="nextButton" class="controlsElement" @click="$emit('nextBtn')">

        <img :src="arrowRightIcon" alt="Einen Schritt weiter">

      </button>

      <button id="nextButton" class="controlsElement" @click="$emit('backToEndBtn')">

        <img :src="skipRightIcon" alt="Zum Ende">

      </button>

    </div>

    <div class="controlsContainerElement" id="controlsSettingsContainer">

      <!--<img :src="settingsIcon" alt="Einstellungen" id="settingsIcon">-->
      <div id="stepControlerContainer" v-if="props.isInEditMode">
        <button @click="$emit('addStep')" class="stepControler" title="Füge einen Tanzschritt hinzu">+</button>
        <button @click="$emit('removeStep')" class="stepControler" title="Lösche den jetztigen Tanzschritt">-</button>
      </div>
    </div>
  </div>
</template>

<style scoped lang="scss">
$color-purple-primary: #8A2BE2;
$color-purple-light: #9D4EDD;
$color-purple-dark: #6A0DAD;
$color-white: #FFFFFF;
$color-gray-light: #F5F5F5;
$color-gray: #E0E0E0;
$color-text-dark: #333333;

#morphDiv {
  width: 100%;
  height: 80vh;
  position: relative;

  #infoCounterDisplayCounter {
    display: flex;
    justify-content: space-between;
    width: 100%;
    margin-top: 1vh;

    .main-button {
      margin-left: 20px;
      margin-bottom: 2rem;
      padding: 0.75rem 1.5rem;
      border: none;
      border-radius: 0.5rem;
      background-color: $colorVioletLight;
      color: $color-white;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.2s ease;

      &:hover {
        background-color: $colorPurpleLight;
        transform: translateY(-1px);
      }
    }
  }

  #manLeftFoot,
  #manRightFoot,
  #womanLeftFoot,
  #womanRightFoot {
    position: absolute;
    z-index: 10;

    .footLetter {
      color: $colorWhite;
      display: flex;
      justify-content: center;
      padding: 10px;
    }
  }

  #manLeftFoot,
  #manRightFoot {
    .innerFootSpacer {
      height: 10px;
    }
  }

  #womanLeftFoot,
  #womanRightFoot {
    .innerFootSpacer {
      height: 30px;
    }
  }

  .manToe,
  .manHeel {
    width: 50px;
  }

  .womanToe {
    width: 30px;
  }

  .womanToe,
  .manToe {
    border-radius: 2rem 2rem 0 0;
  }

  .manToe {
    height: 60px;
  }

  .womanToe {
    height: 40px;
  }

  .manHeel {
    border-radius: 0 0 1rem 1rem;
    height: 40px;
  }

  .womanHeelContainer {
    display: flex;
    justify-content: center;

    .womanHeel {
      height: 25px;
      width: 25px;
      border-radius: 50%;
    }
  }
}

#controlsMainContainer {
  height: 8vh;
  display: flex;
  padding: 10px;
  justify-content: space-between;
  align-items: center;
  margin: 0 2.5rem 0 2.5rem;

  .controlsContainerElement {
    min-width: 30%;
  }

  #controlsSettingsContainer {
    display: flex;
    justify-content: end;
  }

  #controlsContainer {
    background-color: $colorPurpleLight;
    width: 30%;
    height: 7vh;
    display: flex;
    flex-direction: row;
    justify-content: space-around;
    align-items: center;
    border-radius: 20px;

    .controlsElement {
      cursor: pointer;
      border: none;
      border-radius: 50%;
      width: 6vh;
      height: 6vh;
      background-color: $colorPurpleLight;

      display: flex;
      justify-content: center;
      align-items: center;

      &:hover {
        background-color: $colorVioletLight;
      }
    }
  }

  #settingsIcon {
    cursor: pointer;
    width: 30px;
    height: 30px;
  }

  #nextStepsequence {
    text-decoration: none;
  }
}

#stepControlerContainer {
  display: flex;
  flex-direction: column;
  align-items: start;
  justify-content: space-between;

  .stepControler {
    cursor: pointer;
    border: none;
    border-radius: 50%;
    width: 6vh;
    height: 6vh;
    background-color: $colorPurpleLight;

    display: flex;
    justify-content: center;
    align-items: center;

    &:hover {
      background-color: $colorVioletLight;
    }
  }
}
</style>