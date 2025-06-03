<template>
  <main class="add-checklist-page">
    <section class="form-section">
      <h1 class="title">Neue Checkliste erstellen</h1>

      <form @submit.prevent="handleSubmit" class="checklist-form">
        <input v-model="state.name" type="text" @blur="v$.name.$touch" placeholder="Name" required />
        <p v-if="v$.name.$error" class="error-text">Name darf nur aus Buchstaben bestehen</p>


        <label for="sequences">Stepsequences auswählen:</label>
        <div class="sequence-list">
          <div v-for="sequence in stepsequences" :key="sequence.id" class="sequence-item">
            <input type="checkbox" :id="sequence.id" :value="sequence.id" v-model="selectedSequences" />
            <label :for="sequence.id">{{ sequence.name }}</label>
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
import {ref, onMounted, reactive, computed} from "vue";
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


const name = ref('');
const stepsequences = ref([]);
const selectedSequences = ref<number[]>([]);
const isSubmitting = ref(false);

const auth = useAuthStore();

// Stepsequences laden
onMounted(async () => {
  try {
    const response = await fetch(import.meta.env.VITE_ServerIP + '/stepsequence/get', {
      headers: {
        "Authorization": `Bearer ${auth.token}`,
      }
    });
    if (!response.ok) throw new Error("Fehler beim Laden der Stepsequences");
    stepsequences.value = await response.json();
  } catch (error) {
    Swal.fire("Fehler", "Stepsequences konnten nicht geladen werden.", "error");
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

</style>
