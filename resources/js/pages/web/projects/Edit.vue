<template>
  <div
    class="min-h-screen flex flex-col bg-gray-50 text-gray-800"
    style="background-image: url('/pozadina.jpg');"
  >
    <Navbar />

    <div class="max-w-xl mx-auto p-8 bg-white shadow rounded-xl border border-purple-200 mt-10">
      <h1 class="text-3xl font-semibold text-purple-700 mb-8">✏️ Uredi Projekat</h1>

      <form @submit.prevent="submit" class="space-y-6">
        <div class="grid grid-cols-1 gap-y-4">
          <div>
            <label for="project_name" class="block text-purple-700 text-sm font-bold mb-2">Naziv projekta:</label>
            <input
              type="text"
              id="project_name"
              v-model="form.project_name"
              class="shadow-sm appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-purple-400"
            />
            <div v-if="errors.project_name" class="text-red-500 text-sm mt-1">
            {{ errors.project_name }}
          </div>
          </div>

          <div>
            <label for="description" class="block text-purple-700 text-sm font-bold mb-2">Opis:</label>
            <textarea
              id="description"
              v-model="form.description"
              rows="4"
              class="shadow-sm appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-purple-400"
            ></textarea>
            <div v-if="errors.description" class="text-red-500 text-sm mt-1">{{ errors.description }}</div>
          </div>

          <div>
            <label for="start_date" class="block text-purple-700 text-sm font-bold mb-2">Datum početka:</label>
            <input
              type="date"
              id="start_date"
              v-model="form.start_date"
              class="shadow-sm appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-purple-400"
            />
            <div v-if="errors.start_date" class="text-red-500 text-sm mt-1">{{ errors.start_date }}</div>
          </div>

          <div>
            <label for="end_date" class="block text-purple-700 text-sm font-bold mb-2">Datum završetka:</label>
            <input
              type="date"
              id="end_date"
              v-model="form.end_date"
              class="shadow-sm appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-purple-400"
            />
            <div v-if="errors.end_date" class="text-red-500 text-sm mt-1">{{ errors.end_date }}</div>
          </div>

          <div>
            <label for="status" class="block text-purple-700 text-sm font-bold mb-2">Status:</label>
            <select
              id="status"
              v-model="form.status"
              class="shadow-sm appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-purple-400"
            >
              <option value="Active">Aktivan</option>
              <option value="Archived">Arhiviran</option>
              <option value="Completed">Završen</option>
            </select>
            <div v-if="errors.status" class="text-red-500 text-sm mt-1">{{ errors.status }}</div>
          </div>

          <div>
            <label for="client_id" class="block text-purple-700 text-sm font-bold mb-2">Klijent:</label>
            <select
              id="client_id"
              v-model="form.client_id"
              class="shadow-sm appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-purple-400"
              style="color: #000;"
            >
              <option value="" disabled>Odaberite klijenta</option>
              <option v-for="client in props.clients" :key="client.id" :value="client.id">{{ client.name }}</option>
            </select>
            <div v-if="errors.client_id" class="text-red-500 text-sm mt-1">{{ errors.client_id }}</div>
          </div>

          <div>
            <label for="team_id" class="block text-purple-700 text-sm font-bold mb-2">Tim:</label>
            <Multiselect
              v-model="form.team_id"            
              :options="props.teams"            
              :multiple="true"                  
              :close-on-select="false"         
              :clear-on-select="false"          
              :preserve-search="true"           
              placeholder="Odaberi timove"      
              label="team_name"                 
              track-by="id"                     
              class="multiselect-custom"        
            />
            <div v-if="errors.team_id" class="text-red-500 text-sm mt-1">{{ errors.team_id }}</div>
          </div>
        </div>

        <div class="flex justify-end gap-4 mt-8">
          <Link :href="route('projects.index')" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-md shadow">
            Odustani
          </Link>
          <button
          type="submit"
          :disabled="loading"
          class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-md shadow"
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
import Multiselect from 'vue-multiselect'
import 'vue-multiselect/dist/vue-multiselect.min.css'

const props = defineProps({ 
  project: Object,
  teams: Array,
  clients: Array, // Dodaj clients prop
  errors: Object,
});

const form = ref({
  project_name: props.project?.project_name || '',
  description: props.project?.description || '',
  start_date: props.project?.start_date || '',
  end_date: props.project?.end_date || '',
  status: props.project?.status || 'Active',
  team_id: props.project?.team.map(t => t.id) || [],
  client_id: props.project?.client_id || '', // <- ispravno
});

const errors = ref({});


const submit = () => {
  // console.log(form.value);
  form.value.team_id = form.value.team_id.map(team => team.id);
  router.put(route('projects.update', { projectId: props.project.id }), form.value, {
    onSuccess: () => {
      router.get(route('projects.index'));
    },
    onError: (err) => {
      console.log(err);
      errors.value = err;
    },
  });
};



</script>