<template>
    <div class="fixed w-96 bg-white shadow-lg right-0 top-0 h-full p-6 overflow-y-auto z-50">
      <button @click="$emit('close')" class="absolute top-6 right-6 text-gray-500 hover:text-red-600 text-3xl leading-none">
        x
      </button>
  
      <h2 class="text-xl font-bold mb-4 text-black">Dodaj radni unos</h2>
  
      <!-- Projekat -->
      <div class="mb-4">
        <label class="block text-gray-700 font-medium mb-1">Projekat</label>
        <select v-model="form.project_id" class="w-full border px-3 py-2 rounded bg-white text-black">
          <option v-for="project in projects" :key="project.id" :value="project.id">
            {{ project.project_name }}
          </option>
        </select>
      </div>
  
      <!-- Početak rada -->
      <div class="mb-4">
        <label class="block text-gray-700 font-medium mb-1">Početak rada</label>
        <input type="time" v-model="form.start_time" class="w-full border px-3 py-2 rounded text-black" />
      </div>
  
      <!-- Kraj rada -->
      <div class="mb-4">
        <label class="block text-gray-700 font-medium mb-1">Kraj rada</label>
        <input type="time" v-model="form.end_time" class="w-full border px-3 py-2 rounded text-black" />
      </div>
  
      <!-- Pauza (od) -->
      <div class="mb-4">
        <label class="block text-gray-700 font-medium mb-1">Pauza (od)</label>
        <input type="time" v-model="form.break_start" class="w-full border px-3 py-2 rounded text-black" />
      </div>
  
      <!-- Pauza (do) -->
      <div class="mb-4">
        <label class="block text-gray-700 font-medium mb-1">Pauza (do)</label>
        <input type="time" v-model="form.break_end" class="w-full border px-3 py-2 rounded text-black" />
      </div>
  
      <!-- Bilješka -->
      <div class="mb-4">
        <label class="block text-gray-700 font-medium mb-1">Bilješka</label>
        <textarea v-model="form.notes" class="w-full border px-3 py-2 rounded text-black" rows="3"></textarea>
      </div>
  
      <!-- Submit dugme -->
      <div class="flex justify-end">
        <button @click="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded">
          Spasi unos
        </button>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue'
  import { router } from '@inertiajs/vue3'
  
  const props = defineProps({
    date: String, // Ovo je kliknuti dan u kalendaru
    projects: Array,
    entries: Array
  })
  
  const emit = defineEmits(['close', 'saved'])
  
  const form = ref({
    project_id: null,
    start_time: '',
    end_time: '',
    break_start: '',
    break_end: '',
    notes: '',
    date: '' // Hidden polje za datum
  })
  
  onMounted(() => {
    form.value.date = props.date
  })
  
  function submit() {
    console.log('Submitting form:', form.value)
  const makeTimestamp = (time) => time ? `${props.date} ${time}:00` : null;

  const payload = { //moram ovo proslijediti u timesheets.store zbog timestamp errora kad proslijedim form.value samo
    ...form.value,
    start_time: makeTimestamp(form.value.start_time),
    end_time: makeTimestamp(form.value.end_time),
    break_start: makeTimestamp(form.value.break_start),
    break_end: makeTimestamp(form.value.break_end),
  }

  router.post(route('timesheets.store'), payload, {
    preserveScroll: true,
    onSuccess: () => {
      emit('saved')
      emit('close')
    },
    onError: (errors) => {
      console.error('Greške pri slanju forme:', errors)
    }
  })
}
  </script>
  