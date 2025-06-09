<template>
  <main class="add-checklist-page">
    <section class="form-section">
      <h1 class="title">Neue Checkliste erstellen</h1>

      <form @submit.prevent="handleSubmit" class="checklist-form">
        <input v-model="state.name" type="text" @blur="v$.name.$touch" placeholder="Name" required />
        <p v-if="v$.name.$error" class="error-text">Name darf nur aus Buchstaben bestehen</p>



        <label class="sequenceLabel" for="sequences">Stepsequences auswählen:</label>
        <div >

          <div>
          <label for="badges">Nach Abzeichen Filtern:</label>
          <div >
            <select v-model="selectedBadge" class="sequence-item">
              <option v-for="badge in badges" :key="badge.id" :value="badge.id">
                {{ badge.name }}
              </option>
            </select>
          </div>
            <span @click="selectedBadge = 0">Filter Zurücksetzen</span>
          </div>

          <div class="sequence-list">
          <div v-for="sequence in stepsequences" :key="sequence.id" class="sequence-item">
            <input type="checkbox" :id="sequence.id" :value="sequence.id" v-model="selectedSequences" />
            <label :for="sequence.id">{{ sequence.name }}</label>
          </div>
          </div>
        </div>

        <button :disabled="isSubmitting||v$.$invalid"  class="submit-button">
          {{ isSubmitting ? 'Wird erstellt...' : 'Checkliste erstellen' }}
        </button>
      </form>
    </section>
  </main>
</template>

<script setup lang="ts">
import {ref, onMounted, reactive, computed, watch} from "vue";
import Swal from "sweetalert2";
import { useAuthStore } from "@/stores/auth";
import router from "@/router";
import {alpha, alphaNum, maxLength, required} from "@vuelidate/validators";
import {useVuelidate} from "@vuelidate/core";


const state = reactive({
  name: '',
})

const rules = {
  name: {required,alphaNum,maxLength: maxLength(20)},
}

const v$ = useVuelidate(rules, state);

const selectedBadge = ref(0);
const badges = ref([]);
const stepsequences = ref([]);
const selectedSequences = ref<number[]>([]);
const isSubmitting = ref(false);

const auth = useAuthStore();
watch(selectedBadge ,async () => {
  if(selectedBadge.value === 0){
    try {
      const response = await fetch(import.meta.env.VITE_ServerIP + '/stepsequence/get');
      stepsequences.value = await response.json();
    } catch (error) {
      Swal.fire("Fehler", "Stepsequences konnten nicht geladen werden.", "error");
    }
  }else {
    try {
      const response = await fetch(import.meta.env.VITE_ServerIP + `/stepsequence/badge/${selectedBadge.value}`);
      stepsequences.value = await response.json();
    } catch (error) {
      Swal.fire("Fehler", "Stepsequences konnten nicht geladen werden.", "error");
    }
  }

});


// Stepsequences laden
onMounted(async () => {
  try {
    const response = await fetch(import.meta.env.VITE_ServerIP + '/stepsequence/get', {
      headers: {
        "Authorization": `Bearer ${auth.token}`,
      }
    });
    stepsequences.value = await response.json();
  } catch (error) {
    Swal.fire("Fehler", "Stepsequences konnten nicht geladen werden.", "error");
  }
  try {
    const response = await fetch(import.meta.env.VITE_ServerIP + '/badge/badges');
    badges.value = await response.json();
  } catch (error) {
    Swal.fire("Fehler", "Abzeichen konnten nicht geladen werden.", "error");
  }
});

const handleSubmit = async () => {
  v$.value.$validate();
  if (v$.value.$error) {
    return;
  }else {


    if (!state.name.trim() || selectedSequences.value.length === 0) {

        Swal.fire("Ungültig", "Name und mindestens ein Stepsequence müssen angegeben werden.", "warning");
      return;
    }

    isSubmitting.value = true;

    try {
      const response = await fetch(import.meta.env.VITE_ServerIP + '/checklist/new', {
        method: 'POST',
        headers: {
          "Authorization": `Bearer ${auth.token}`,
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          name: state.name.trim(),
          stepsequences: selectedSequences.value
        })
      });

      if (!response.ok) {
        const errorData = await response.json();
        throw new Error(errorData.error || 'Fehler beim Erstellen der Checkliste');
      }

      await Swal.fire("Erfolg", "Die Checkliste wurde erfolgreich erstellt!", "success");
      router.push('/checklists');
    } catch (error: any) {
      Swal.fire("Fehler", error.message || "Unbekannter Fehler", "error");
    } finally {
      isSubmitting.value = false;
    }
  }
};
</script>

<style scoped lang="scss">
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
   .sequenceLabel{
     font-size: 1.5rem;
     color: $colorVioletDark;
     margin-bottom: 0.5rem;
     text-align: center;
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
    min-width: 300px;


  }
  span {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    margin-bottom: 2rem;
    margin-top: 0;
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

  .sequence-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
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
  .error-text{
    color: $colorVioletLight;
    font-size: 0.75rem;
  }
}

</style>
