<template>
<div class="fixed w-96 bg-purple-900/20 backdrop-blur-md shadow-lg right-0 top-0 h-full p-6 overflow-y-auto z-50 text-black">
    <button @click="$emit('close')" class="absolute top-6 right-6 text-gray-500 hover:text-red-600 text-3xl leading-none">
      x
    </button>

    <h2 class="text-xl font-bold mb-4 text-violet-200">Unosi za {{ props.date }}</h2>

    <!-- Lista postojećih unosa -->
    <div v-if="props.entries.length" class="mb-6 space-y-4">
      <div v-for="entry in props.entries" :key="entry.id" class="p-4 border rounded bg-gray-50 text-sm text-black">
        <p><strong>Projekat:</strong> {{ entry.project }}</p>
        <p><strong>Od:</strong> {{ formatTime(entry.start_time) }} - <strong>Do:</strong> {{ formatTime(entry.end_time) }}</p>
        <p v-if="entry.break_start && entry.break_end"><strong>Pauza:</strong> {{ formatTime(entry.break_start) }} - {{ formatTime(entry.break_end) }}</p>
        <p v-if="entry.notes"><strong>Bilješka:</strong> {{ entry.notes }}</p>
        <button @click="deleteEntry(entry.id)" class="text-red-600 text-sm mt-2 hover:underline">Obriši</button>
        <button @click="editEntry(entry)" class="text-blue-600 text-sm mr-3 mt-2 hover:underline">Uredi</button>
      </div>
    </div>

    <hr class="my-6 border-gray-300" />

    <!-- Dugme za prikaz forme -->
    <div class="mb-4 flex justify-end">
      <button @click="toggleForm" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
        {{ showForm ? 'Sakrij formu za unos' : 'Novi unos' }}
      </button>
    </div>

    <!-- Forma za dodavanje unosa -->
    <div v-if="showForm">
      <h2 class="text-xl font-bold mb-4 text-black">{{ isEditMode ? 'Uredi radni unos' : 'Dodaj radni unos' }}</h2>

      <!-- Projekat -->
      <div class="mb-4">
        <label class="block text-violet-200 font-medium mb-1">Projekat</label>
        <select v-model="form.project_id" class="w-full border px-3 py-2 rounded bg-purple text-black">
          <option v-for="project in projects" :key="project[0].id" :value="project[0].id">
            {{ project[0].project_name }}
          </option>
        </select>
        <p v-if="errors.project_id" class="text-red-600 text-sm mt-1">{{ errors.project_id }}</p>
      </div>

      <!-- Početak rada -->
      <div class="mb-4">
        <label class="block text-violet-200 font-medium mb-1">Početak rada</label>
        <input type="time" v-model="form.start_time" class="w-full border px-3 py-2 rounded text-white" />
        <p v-if="errors.start_time" class="text-red-600 text-sm mt-1">{{ errors.start_time }}</p>
        <p v-if="errors.overlap_error" class="text-red-600 text-sm mb-4">{{ errors.overlap_error }}</p>
      </div>

      <!-- Kraj rada -->
      <div class="mb-4">
        <label class="block text-violet-200 font-medium mb-1">Kraj rada</label>
        <input type="time" v-model="form.end_time" class="w-full border px-3 py-2 rounded text-white" />
        <p v-if="errors.end_time" class="text-red-600 text-sm mt-1">{{ errors.end_time }}</p>
        <p v-if="errors.overlap_error" class="text-red-600 text-sm mb-4">{{ errors.overlap_error }}</p>
      </div>

      <!-- Pauza (od) -->
      <div class="mb-4">
        <label class="block text-violet-200 font-medium mb-1">Pauza (od)</label>
        <input type="time" v-model="form.break_start" class="w-full border px-3 py-2 rounded text-white" />
        <p v-if="errors.break_start" class="text-red-600 text-sm mt-1">{{ errors.break_start }}</p>
        <p v-if="errors.break_error" class="text-red-600 text-sm mb-4">{{ errors.break_error }}</p>
      </div>

      <!-- Pauza (do) -->
      <div class="mb-4">
        <label class="block text-violet-200 font-medium mb-1">Pauza (do)</label>
        <input type="time" v-model="form.break_end" class="w-full border px-3 py-2 rounded text-white" />
        <p v-if="errors.break_end" class="text-red-600 text-sm mt-1">{{ errors.break_end }}</p>
        <p v-if="errors.break_error" class="text-red-600 text-sm mb-4">{{ errors.break_error }}</p>
      </div>

      <!-- Bilješka -->
      <div class="mb-4">
        <label class="block text-violet-200 font-medium mb-1">Bilješka</label>
        <textarea v-model="form.notes" class="w-full border px-3 py-2 rounded text-white" rows="3"></textarea>
        <p v-if="errors.notes" class="text-red-600 text-sm mt-1">{{ errors.notes }}</p>
      </div>

      <!-- Submit dugme -->
      <div class="flex justify-end">
        <button @click="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded">
          Spasi unos
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { router, useForm } from '@inertiajs/vue3'

const errors = ref({})
const showForm = ref(false)

const props = defineProps({
  date: String,
  projects: Array,
  entries: Array
})

const emit = defineEmits(['close', 'saved', 'deleted'])

const form = ref({
  project_id: null,
  //project: '',
  start_time: '',
  end_time: '',
  break_start: '',
  break_end: '',
  notes: '',
  date: ''
})

onMounted(() => {
  form.value.date = props.date
})

const isEditMode = ref(false)
const editingId = ref(null)

function toggleForm() {
  // reset prilikom sakrivanja forme
  if (showForm.value) {
    isEditMode.value = false;
    editingId.value = null;
    resetForm();
  }
  showForm.value = !showForm.value; //toggle true-false false-true
}

function editEntry(entry) {
  isEditMode.value = true
  showForm.value = true
  editingId.value = entry.id
  console.log('Editing ID:', editingId.value)
  console.log('Entry:', entry)  // Provjeri cijeli objekat

  form.value = {
    project_id: entry.project_id,
    //project: entry.project,
    start_time: entry.start_time?.substring(11, 16),
    end_time: entry.end_time?.substring(11, 16),
    break_start: entry.break_start?.substring(11, 16) || '',
    break_end: entry.break_end?.substring(11, 16) || '',
    notes: entry.notes || '',
    date: props.date
  }
  console.log('Form project_id:', form.value.project_id)
}

function resetForm() {
  form.value = {
    project_id: null,
    start_time: '',
    end_time: '',
    break_start: '',
    break_end: '',
    notes: '',
    date: props.date
  };
}

function submit() {
  console.log('Editing ID:', editingId.value)
  const makeTimestamp = (time) => time ? `${props.date} ${time}:00` : null;

  const payload = {
    ...form.value,
    start_time: makeTimestamp(form.value.start_time),
    end_time: makeTimestamp(form.value.end_time),
    break_start: makeTimestamp(form.value.break_start),
    break_end: makeTimestamp(form.value.break_end),
  }

  const url = isEditMode.value
    /* ? route('timesheets.update', editingId.value) */
    ? `/timesheets/update/${editingId.value}`
    : route('timesheets.store')

    router.visit(url, {
  method: isEditMode.value ? 'put' : 'post',
  data: payload,
  preserveScroll: true,
  preserveState: true,
  onSuccess: () => {console.log("Unos je uspješno ažuriran");
    emit('saved')
    emit('close')
    showForm.value = false
    isEditMode.value = false
    editingId.value = null
  },
  onError: (err) => {console.log("Unos nije ažuriran");
    errors.value = err
  }
})
}


function deleteEntry(id) {
  if (confirm('Da li ste sigurni da želite obrisati ovaj unos?')) {
    router.delete(route('timesheets.destroy', id), {
      preserveScroll: true,
      onSuccess: () => {
        emit('deleted')
        emit('close')
      },
      onError: (err) => {
        errors.value = err
      }
    })
  }
}


function formatTime(timestamp) {
  if (!timestamp) return ''
  const time = new Date(timestamp)
  return time.toLocaleTimeString('hr-BA', { hour: '2-digit', minute: '2-digit' })
}
</script>
