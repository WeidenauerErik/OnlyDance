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

// Foot element refs
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

// Responsive variables
const isMobile = ref(false);
const footSizeMultiplier = ref(1);
const footSpacing = ref(0);

const updateFeet = (step: FootStep) => {
  howQuick.value = step.howquick;

  const sizeMult = footSizeMultiplier.value;
  const spacing = footSpacing.value;

  const calculatePosition = (
      pos: number,
      isX: boolean,
      isLeftFoot: boolean
  ) => {
    const screenSize = isX ? screenWidth : screenHeight;
    const differenz = isX ? footWidthDifferenz : footHeightDifferenz;
    if (!isX) return screenSize * pos - differenz * sizeMult;

    const spacingOffset = isLeftFoot ? -spacing : spacing;
    return screenSize * pos - differenz * sizeMult + spacingOffset  ;
  };


  if (manLeftFoot.value && manLeftFootHeel.value && manLeftFootToes.value) {
    manLeftFoot.value.style.transition = `top ${step.howquick}s ease, left ${step.howquick}s ease, transform ${step.howquick}s ease`;
    manLeftFoot.value.style.top = `${calculatePosition(step.m1_y, false,true)}px`;
    manLeftFoot.value.style.left = `${calculatePosition(step.m1_x, true,true)}px`;
    manLeftFoot.value.style.transform = `rotate(${step.m1_rotate}deg) scale(${sizeMult})`;
    manLeftFootToes.value.style.backgroundColor = step.m1_toe ? '#73168C' : '#875595';
    manLeftFootHeel.value.style.backgroundColor = step.m1_heel ? '#73168C' : '#875595';
  }

  if (manRightFoot.value && manRightFootHeel.value && manRightFootToes.value) {
    manRightFoot.value.style.transition = `top ${step.howquick}s ease, left ${step.howquick}s ease, transform ${step.howquick}s ease`;
    manRightFoot.value.style.top = `${calculatePosition(step.m2_y, false,false)}px`;
    manRightFoot.value.style.left = `${calculatePosition(step.m2_x, true,false) - spacing}px`;
    manRightFoot.value.style.transform = `rotate(${step.m2_rotate}deg) scale(${sizeMult})`;
    manRightFootToes.value.style.backgroundColor = step.m2_toe ? '#73168C' : '#875595';
    manRightFootHeel.value.style.backgroundColor = step.m2_heel ? '#73168C' : '#875595';
  }

  if (womanLeftFoot.value && womanLeftFootHeel.value && womanLeftFootToes.value) {
    womanLeftFoot.value.style.transition = `top ${step.howquick}s ease, left ${step.howquick}s ease, transform ${step.howquick}s ease`;
    womanLeftFoot.value.style.top = `${calculatePosition(step.w1_y, false,true)}px`;
    womanLeftFoot.value.style.left = `${calculatePosition(step.w1_x, true,true)}px`;
    womanLeftFoot.value.style.transform = `rotate(${step.w1_rotate}deg) scale(${sizeMult})`;
    womanLeftFootToes.value.style.backgroundColor = step.w1_toe ? '#73168C' : '#875595';
    womanLeftFootHeel.value.style.backgroundColor = step.w1_heel ? '#73168C' : '#875595';
  }

  if (womanRightFoot.value && womanRightFootHeel.value && womanRightFootToes.value) {
    womanRightFoot.value.style.transition = `top ${step.howquick}s ease, left ${step.howquick}s ease, transform ${step.howquick}s ease`;
    womanRightFoot.value.style.top = `${calculatePosition(step.w2_y, false,false)}px`;
    womanRightFoot.value.style.left = `${calculatePosition(step.w2_x, true,false) - spacing}px`;
    womanRightFoot.value.style.transform = `rotate(${step.w2_rotate}deg) scale(${sizeMult})`;
    womanRightFootToes.value.style.backgroundColor = step.w2_toe ? '#73168C' : '#875595';
    womanRightFootHeel.value.style.backgroundColor = step.w2_heel ? '#73168C' : '#875595';
  }
};

const checkMobile = () => {
  isMobile.value = window.innerWidth <= 768;
  footSizeMultiplier.value = isMobile.value ? 0.7 : 1;
  footSpacing.value = isMobile.value ? 30 : 0;
};

const resize = () => {
  if (morphDivRef.value) {
    screenWidth = morphDivRef.value.offsetWidth;
    screenHeight = morphDivRef.value.offsetHeight;
    checkMobile();
    if (props.currentStep !== null) updateFeet(props.currentStep);
  }
};

onMounted(() => {
  window.addEventListener('resize', resize);
  checkMobile();
});

onUpdated(async () => {
  await nextTick();
  resize();
});

onUnmounted(() => {
  window.removeEventListener('resize', resize);
});
</script>

<template>
  <div id="animation-container" :class="{ 'mobile-layout': isMobile }">
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
        <div v-else>
          <button @click="$emit('removeStep')" class="main-button" title="Lösche den jetztigen Tanzschritt">Tanzschritt entfernen</button>
        </div>
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

      <div>
        <button @click="$emit('addStep')" class="main-button" title="Füge einen Tanzschritt hinzu" v-if="props.isInEditMode">Tanzschritt hinzufügen</button>
        <button v-else-if="props.isLoggedIn" @click="$emit('popUpBtn')" class="main-button" title="Füge einen Tanzschritt zu einer Checkliste hinzu" v-if="!props.isInEditMode">Tanzschritt zu Checkliste hinzufügen</button>
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

#animation-container {
  position: relative;
  width: 100%;
  height: 80vh;

  &.mobile-layout {
    height: 60vh;

    #morphDiv {
      height: 100%;

      .foot {
        z-index: 10;

        &#manLeftFoot, &#womanLeftFoot {
          z-index: 11;
        }
      }
    }
  }
}

.main-button {
  margin-left: 20px;
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 0.5rem;
  background-color: $colorVioletLight;
  color: $color-white;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
  margin-bottom: 20px;

  &:hover {
    background-color: $colorPurpleLight;
    transform: translateY(-1px);
  }
}

#morphDiv {
  width: 100%;
  height: 80vh;
  position: relative;

  #infoCounterDisplayCounter {
    display: flex;
    justify-content: space-between;
    width: 100%;
    margin-top: 1vh;
  }

  #manLeftFoot,
  #manRightFoot,
  #womanLeftFoot,
  #womanRightFoot {
    position: absolute;
    z-index: 10;
    transition: all 0.3s ease;

    .footLetter {
      color: $colorWhite;
      display: flex;
      justify-content: center;
      padding: calc(10px * v-bind(footSizeMultiplier));
      font-size: calc(12px * v-bind(footSizeMultiplier));
    }
  }

  #manLeftFoot,
  #manRightFoot {
    .innerFootSpacer {
      height: calc(10px * v-bind(footSizeMultiplier));
    }
  }

  #womanLeftFoot,
  #womanRightFoot {
    .innerFootSpacer {
      height: calc(30px * v-bind(footSizeMultiplier));
    }
  }

  .manToe,
  .manHeel {
    width: calc(50px * v-bind(footSizeMultiplier));
  }

  .womanToe {
    width: calc(30px * v-bind(footSizeMultiplier));
  }

  .womanToe,
  .manToe {
    border-radius: 2rem 2rem 0 0;
  }

  .manToe {
    height: calc(60px * v-bind(footSizeMultiplier));
  }

  .womanToe {
    height: calc(40px * v-bind(footSizeMultiplier));
  }

  .manHeel {
    border-radius: 0 0 1rem 1rem;
    height: calc(40px * v-bind(footSizeMultiplier));
  }

  .womanHeelContainer {
    display: flex;
    justify-content: center;

    .womanHeel {
      height: calc(25px * v-bind(footSizeMultiplier));
      width: calc(25px * v-bind(footSizeMultiplier));
      border-radius: 50%;
    }
  }
}

#controlsMainContainer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  height: auto;
  min-height: 8vh;
  padding: 10px 2.5rem;
  margin: 0;

  .controlsContainerElement {
    display: flex;
    align-items: center;
    min-width: 30%;
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
}

@media (max-width: 768px) {
  #animation-container {
    height: 60vh;
  }

  #controlsMainContainer {
    flex-direction: column;
    gap: 1rem;
    padding: 10px;

    .controlsContainerElement {
      min-width: 100%;
      justify-content: center;
    }

    #controlsContainer {
      width: 100%;
      height: 8vh;

      .controlsElement {
        width: 8vh;
        height: 8vh;

        img {
          width: 60%;
          height: 60%;
        }
      }
    }
  }
}
</style>