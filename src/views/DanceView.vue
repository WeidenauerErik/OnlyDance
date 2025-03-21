<script setup lang="ts">
import {ref, computed, onMounted, nextTick} from 'vue';
import LoaderComponent from "@/components/LoaderComponent.vue";
import {type FootStep, type Step} from "@/tsTypes/interfacesDanceView.ts";

const stepCounter = ref<number>(0);
const danceStepCounter = ref<number>(1);
const danceStepLength = ref<number>(0);
const danceName = ref<string>()

const footHeightDifferenz = 55;
const footWidthDifferenz = 25;

let screenWidth: number;
let screenHeight: number;

let breakpoint: boolean;

const howQuick = ref<number>(2);
const steps = ref<Step[]>([]);

const updateFeet = (step: Step) => {
  howQuick.value = step.howQuick;

  const updateFoot = (footId: string, footData: FootStep, toeId: string, heelId: string, letterId: string, activeColor: string, inactiveColor: string) => {
    const foot = document.getElementById(footId) as HTMLElement;
    const toes = document.getElementById(toeId) as HTMLElement;
    const heel = document.getElementById(heelId) as HTMLElement;
    const letter = document.getElementById(letterId) as HTMLElement;

    if (foot && toes && heel && letter) {
      foot.style.top = `${screenHeight * footData.height - footHeightDifferenz}px`;
      foot.style.left = `${screenWidth * footData.width - footWidthDifferenz}px`;
      foot.style.transform = `rotate(${footData.rotate}deg)`;
      toes.style.backgroundColor = footData.footToesActive ? activeColor : inactiveColor;
      heel.style.backgroundColor = footData.footHeelActive ? activeColor : inactiveColor;
      letter.style.color = footData.footToesActive ? 'white' : 'black';
    }
  };

  updateFoot('manLeftFoot', step.man.leftFoot, 'manLeftFootToes', 'manLeftFootHeel', 'manLeftFootLetter', 'blue', 'lightblue');
  updateFoot('manRightFoot', step.man.rightFoot, 'manRightFootToes', 'manRightFootHeel', 'manRightFootLetter', 'blue', 'lightblue');
  updateFoot('womanLeftFoot', step.woman.leftFoot, 'womanLeftFootToes', 'womanLeftFootHeel', 'womanLeftFootLetter', 'red', '#FFCCCB');
  updateFoot('womanRightFoot', step.woman.rightFoot, 'womanRightFootToes', 'womanRightFootHeel', 'womanRightFootLetter', 'red', '#FFCCCB');
};

const resize = () => {
  const morphDiv = document.getElementById('morphDiv');
  if (morphDiv) {
    screenWidth = morphDiv.offsetWidth;
    screenHeight = morphDiv.offsetHeight;
    updateFeet(steps.value[stepCounter.value]);
  }
};

onMounted(() => {
  fetch('https://onlydance.onrender.com/')
      .then((res) => res.json())
      .then((data: Step[]) => {
        steps.value = data;
        danceStepLength.value = data.length;
        danceName.value = 'Walzer';
        stepCounter.value = 0;
        window.addEventListener('resize', resize);
      })
      .then(async () => {
        await nextTick();
        const morphDiv = document.getElementById('morphDiv') as HTMLElement;
        const controls = document.getElementById('controls') as HTMLElement;
        const loader = document.getElementById('loader') as HTMLElement;

        if (morphDiv && controls && loader) {
          morphDiv.style.display = 'flex';
          controls.style.display = 'flex';
          loader.style.display = "none";
        }

        resize();
        morphDiv.scrollIntoView({ behavior: "smooth", block: "start" });
      });
});

const BackBtn = () => {
  if (stepCounter.value > 0) {
    breakpoint = true;
    stepCounter.value--;
    danceStepCounter.value--;
    updateFeet(steps.value[stepCounter.value]);
  }
};

const NextBtn = () => {
  if (stepCounter.value < steps.value.length - 1) {
    breakpoint = true;
    stepCounter.value++;
    danceStepCounter.value++;
    updateFeet(steps.value[stepCounter.value]);
  }
};

const BackToBeginBtn = () => {
  breakpoint = true;
  stepCounter.value = 0;
  danceStepCounter.value = 1;
  updateFeet(steps.value[stepCounter.value]);
};

const AutoplayBtn = async () => {
  breakpoint = false;
  while (stepCounter.value < steps.value.length - 1) {
    if (breakpoint) {
      breakpoint = false;
      break;
    }
    stepCounter.value++;
    danceStepCounter.value++;
    updateFeet(steps.value[stepCounter.value]);
    await new Promise((resolve) => setTimeout(resolve, steps.value[stepCounter.value].howQuick * 500));
  }
};

const NextBtnDisabled = computed(() => stepCounter.value >= steps.value.length - 1);
const AutoplayBtnDisabled = computed(() => stepCounter.value >= steps.value.length - 1);
const BackBtnDisabled = computed(() => stepCounter.value <= 0);
const BackToBeginBtnDisabled = computed(() => stepCounter.value === 0);
</script>

<template>

  <div id="loader">

    <LoaderComponent/>

  </div>

  <div id="morphDiv">

    <div id="infoDisplay">

      <div>

        <h1 id="infoTextDisplay"> {{ danceName }}:</h1>

      </div>

      <div>

        <span id="infoCounterDisplay"> {{ danceStepCounter }} / {{ danceStepLength }}</span>

      </div>

    </div>

    <div id="manLeftFoot" class="foot" :class="{ quickMovement: howQuick === 2, slowMovement: howQuick === 1 }">

      <div id="manLeftFootToes" class="manToe">

        <span id="manLeftFootLetter" class="footLetter">L</span>

      </div>

      <div class="innerFootSpacer"></div>
      <div id="manLeftFootHeel" class="manHeel"></div>

    </div>

    <div id="manRightFoot" class="foot" :class="{ quickMovement: howQuick === 2, slowMovement: howQuick === 1 }">

      <div id="manRightFootToes" class="manToe">

        <span id="manRightFootLetter" class="footLetter">R</span>

      </div>

      <div class="innerFootSpacer"></div>
      <div id="manRightFootHeel" class="manHeel"></div>

    </div>

    <div id="womanLeftFoot" class="foot" :class="{ quickMovement: howQuick === 2, slowMovement: howQuick === 1 }">

      <div id="womanLeftFootToes" class="womanToe">

        <span id="womanLeftFootLetter" class="footLetter">L</span>

      </div>

      <div class="innerFootSpacer"></div>
      <div class="womanHeelContainer">

        <div id="womanLeftFootHeel" class="womanHeel"></div>

      </div>

    </div>

    <div
        id="womanRightFoot"
        class="foot"
        :class="{ quickMovement: howQuick === 2, slowMovement: howQuick === 1 }"
    >

      <div id="womanRightFootToes" class="womanToe">

        <span id="womanRightFootLetter" class="footLetter">R</span>

      </div>

      <div class="innerFootSpacer"></div>
      <div class="womanHeelContainer">

        <div id="womanRightFootHeel" class="womanHeel"></div>

      </div>

    </div>

  </div>

  <div id="controls">

    <div class="controlsSpacer"></div>
    <button id="backButton" class="controlsElement" @click="BackBtn" :disabled="BackBtnDisabled">
      Zurück
    </button>
    <div class="controlsSpacer"></div>
    <button id="nextButton" class="controlsElement" @click="NextBtn" :disabled="NextBtnDisabled">
      Weiter
    </button>
    <div class="controlsSpacer"></div>
    <button id="nextButton" class="controlsElement" @click="BackToBeginBtn" :disabled="BackToBeginBtnDisabled">
      Zurück zum Anfang
    </button>
    <div class="controlsSpacer"></div>
    <button id="autoplayButton" class="controlsElement" @click="AutoplayBtn" :disabled="AutoplayBtnDisabled">Autoplay
    </button>

  </div>

</template>

<style scoped lang="scss">
#morphDiv {
  width: 100%;
  height: 90vh;
  position: relative;
  display: none;

  #infoDisplay {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    width: 100%;
    margin: 0 10px 0 10px;

    #infoCounterDisplay, #infoTextDisplay {
      position: relative;
      top: 1%;
      margin: 0;
      height: 5vh;
      display: flex;
      align-items: center;
    }
  }

  #manLeftFoot,
  #manRightFoot,
  #womanLeftFoot,
  #womanRightFoot {
    position: absolute;

    .footLetter {
      display: flex;
      justify-content: center;
      padding: 10px;
    }
  }


  .quickMovement {
    transition: top 0.5s ease,
    left 0.5s ease,
    transform 0.5s ease;
  }

  .slowMovement {
    transition: top 4s ease,
    left 4s ease,
    transform 4s ease;
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

#controls {
  display: none;
  padding: 10px;
  justify-content: center;

  .controlsSpacer {
    width: 50px;
  }

  .controlsElement {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 10rem;
    height: 2.5rem;
    background-color: $buttonColorPrimary;
    color: $fontColorWhite;
    border: none;
    cursor: pointer;
  }

  .controlsElement:disabled {
    background-color: $buttonColorPrimaryDisabled;
  }
}
</style>