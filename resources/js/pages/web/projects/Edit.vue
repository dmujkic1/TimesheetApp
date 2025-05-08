<template>
  <div
    class="min-h-screen flex flex-col bg-gray-50 text-gray-800"
    style="background-image: url('/pozadina.jpg');"
  >
    <Navbar />

    <!-- Success Message -->
    <div v-if="successMessage" class="bg-green-500 text-white text-center py-2 mb-6 rounded-md">
      {{ successMessage }}
    </div>

    <div class="max-w-6xl w-full mx-auto p-8 border bg-black/50 backdrop-blur-lg rounded-xl shadow-lg overflow-hidden">
      <h1 class="text-3xl font-semibold text-white mb-8">✏️ Uredi Projekat</h1>

          <form @submit.prevent="submit" class="space-y-6 max-w-7xl mx-auto p-6">
            <div class="grid grid-cols-2 gap-8">
            <!-- Naziv Projekta -->
            <div>
              <label for="project_name" class="block text-white text-m font-bold mb-2">Naziv projekta:</label>
              <input
                type="text"
                id="project_name"
                v-model="form.project_name"
                class="w-full bg-gray-700 text-purple-400  border-gray-300 rounded-2xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-purple-500 shadow-md hover:shadow-lg hover:shadow-purple-600/50 transform hover:scale-105 transition-all duration-200"              />
              <div v-if="errors.project_name" class="text-red-500 text-sm mt-1">
                {{ errors.project_name }}
              </div>
            </div>
          
            <!-- Opis -->
            <div>
              <label for="description" class="block text-white text-m font-bold mb-2">Opis:</label>
              <textarea
                id="description"
                v-model="form.description"
               class="w-full bg-gray-700 text-purple-400  border-gray-300 rounded-2xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-purple-500 shadow-md hover:shadow-lg hover:shadow-purple-600/50 transform hover:scale-105 transition-all duration-200"
              ></textarea>
              <div v-if="errors.description" class="text-red-500 text-sm mt-1">{{ errors.description }}</div>
            </div>
          
            <!-- Datum početka -->
            <div>
              <label for="start_date" class="block text-white text-m font-bold mb-2">Datum početka:</label>
              <input
                type="date"
                id="start_date"
                v-model="form.start_date"
                class="w-full bg-gray-700 text-purple-400  border-gray-300 rounded-2xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-purple-500 shadow-md hover:shadow-lg hover:shadow-purple-600/50 transform hover:scale-105 transition-all duration-200"
              />
              <div v-if="errors.start_date" class="text-red-500 text-sm mt-1">{{ errors.start_date }}</div>
            </div>
          
            <!-- Datum završetka -->
            <div>
              <label for="end_date" class="block text-white text-m font-bold mb-2">Datum završetka:</label>
              <input
                type="date"
                id="end_date"
                v-model="form.end_date"
                class="w-full bg-gray-700 text-purple-400  border-gray-300 rounded-2xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-purple-500 shadow-md hover:shadow-lg hover:shadow-purple-600/50 transform hover:scale-105 transition-all duration-200"
              />
              <div v-if="errors.end_date" class="text-red-500 text-sm mt-1">{{ errors.end_date }}</div>
            </div>
          
            <!-- Status -->
            <div>
              <label for="status" class="block text-white text-m font-bold mb-2">Status:</label>
              <select
                id="status"
                v-model="form.status"
                class="w-full bg-gray-700 text-purple-400  border-gray-300 rounded-2xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-purple-500 shadow-md hover:shadow-lg hover:shadow-purple-600/50 transform hover:scale-105 transition-all duration-200"
              >
                <option value="Active">Aktivan</option>
                <option value="Archived">Arhiviran</option>
                <option value="Completed">Završen</option>
              </select>
              <div v-if="errors.status" class="text-red-500 text-sm mt-1">{{ errors.status }}</div>
            </div>
          
            <!-- Klijent -->
            <div>
              <label for="client_id" class="block text-white text-m font-bold mb-2">Klijent:</label>
              <select
                id="client_id"
                v-model="form.client_id"
                class="w-full bg-gray-700 text-purple-400  border-gray-300 rounded-2xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-purple-500 shadow-md hover:shadow-lg hover:shadow-purple-600/50 transform hover:scale-105 transition-all duration-200"
              >
                <option value="" disabled>Odaberite klijenta</option>
                <option v-for="client in props.clients" :key="client.id" :value="client.id">{{ client.name }}</option>
              </select>
              <div v-if="errors.client_id" class="text-red-500 text-sm mt-1">{{ errors.client_id }}</div>
            </div>
          
            <!-- Tim -->
            <div class="col-span-2">
              <label for="team_id" class="block text-white text-m font-bold mb-2">Tim:</label>
              <Multiselect
                v-model="selectedTeams"
                :options="props.teams"
                :multiple="true"
                :close-on-select="false"
                :clear-on-select="false"
                :preserve-search="true"
                placeholder="Odaberi timove"
                label="team_name"
                track-by="id"
                class="multiselect bg-gray-700 border border-gray-600 text-white rounded-xl px-3 py-2"
              >
                <!-- Prikaz odabranih tagova -->
                <template #tag="{ option, remove }">
                  <span class="bg-purple-600 text-white rounded px-3 py-1 mr-2 inline-flex items-center gap-2">
                    {{ option.team_name }}
                    <button
                      @click="remove(option)"
                      class="text-white text-lg font-bold hover:text-gray-300"
                    >
                      &times;
                    </button>
                  </span>
                </template>
              
                <!-- Prikaz opcija u dropdownu -->
                <template #option="{ option, index, isSelected, isHighlighted }">
                  <div
                    class="px-4 py-2 cursor-pointer rounded-2xl"
                    :class="{
                      'bg-purple-600 text-white': isHighlighted,
                      'bg-gray-700 text-white': !isHighlighted,
                    }"
                  >
                    {{ option.team_name }}
                  </div>
                </template>
              
                <!-- Placeholder tekst -->
                <template #placeholder>
                  <span class="text-white">Odaberi timove</span>
                </template>
              </Multiselect>
              <div v-if="errors.team_id" class="text-red-500 text-sm mt-1">{{ errors.team_id }}</div>
            </div>

          
          </div>
        
          <!-- Submit Button -->
          <div class="flex justify-end gap-4 mt-10">
            <Link :href="route('projects.index')" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-3 rounded-xl shadow">
              Odustani
            </Link>
            <button
              type="submit"
              :disabled="loading"
              class="bg-purple-600 hover:bg-purple-700 text-white px-8 py-3 rounded-xl shadow"
            >
              Ažuriraj Projekat
            </button>
          </div>
       </form>

    </div>
  </div>
  <div class="h-24"><Footer /></div>
</template>



<script setup>
import { ref, onMounted } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import Navbar from '@/components/Navbar.vue';
import Footer from '@/components/Footer.vue';
import Multiselect from 'vue-multiselect';
import 'vue-multiselect/dist/vue-multiselect.min.css';

const props = defineProps({
  project: Object,
  teams: Array,
  clients: Array,
  errors: Object,
  success: String, 
});

const errors = ref({});
const loading = ref(false);
const successMessage = ref('');

onMounted(() => {
  if (props.flash?.success) {
    successMessage.value = props.flash.success;
  }
});


const selectedTeams = ref(props.project.team || []);
const form = ref({
  project_name: props.project?.project_name || '',
  description: props.project?.description || '',
  start_date: props.project?.start_date || '',
  end_date: props.project?.end_date || '',
  status: props.project?.status || 'Active',
  team_id: [],
  client_id: props.project?.client_id || '',
});

const submit = () => {
  form.value.team_id = selectedTeams.value.map(team => team.id);

  loading.value = true; 

  router.put(route('projects.update', props.project.id), form.value, {
    onSuccess: (response) => {
      successMessage.value = response.flash?.success || 'Projekat je uspešno ažuriran!';
      router.visit(route('projects.index')); 
    },
    onError: (err) => {
      errors.value = err;
    },
    onFinish: () => {
      loading.value = false;
    },
  });
};


</script>
