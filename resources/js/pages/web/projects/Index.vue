<template>
  <div
    class="min-h-screen flex flex-col bg-purple-900 text-gray-200"
    style="background-image: url('/pozadina.jpg'); background-size: cover; background-position: center;"
  >
    <Navbar />

    <!-- Flash Message -->
    <div v-if="flashMessage" class="bg-green-100 border border-green-400 text-green-700 px-6 py-4 rounded-lg shadow-xl mx-auto max-w-6xl backdrop-blur-lg mt-6">
      {{ flashMessage }}
    </div>

    <!-- Header Section -->
    <!-- Lista Projekata Header -->
    <div class="w-full max-w-6xl mx-auto px-4 mt-10">
      <div class="flex items-center justify-between bg-black/60 backdrop-blur-md rounded-xl shadow-lg px-6 py-5">
        <h1 class="text-3xl font-bold text-white">📂 Lista Projekata</h1>
        <Link
          :href="route('projects.create')"
          class="bg-purple-700 hover:bg-purple-900 text-white px-6 py-3 rounded-md shadow-md transition-all duration-200"
        >
          ➕ Dodaj Projekat
        </Link>
      </div>
    </div>

    <!-- Tabela Projekata -->
    <div class="w-full max-w-6xl mx-auto px-4 mt-6">
      <div class="bg-black/50 backdrop-blur-lg rounded-xl shadow-lg overflow-hidden">
        <table class="w-full text-white text-left text-sm">
          <thead class="bg-purple-900/80 text-white uppercase text-xs tracking-wider">
            <tr>
              <th class="px-6 py-4">Naziv</th>
              <th class="px-6 py-4">Klijent</th>
              <th class="px-6 py-4">Status</th>
              <th class="px-6 py-4 text-center">Akcije</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="project in projects"
              :key="project.id"
              class="border-t border-white/10 hover:bg-purple-700/60 transition-all"
            >
              <td class="px-6 py-4">{{ project.project_name }}</td>
              <td class="px-6 py-4">{{ project.client?.name ?? 'N/A' }}</td>
              <td class="px-6 py-4">
                <span
                  :class="[
                    'px-3 py-1 rounded-full text-xs font-semibold',
                    {
                      'bg-green-600': project.status === 'Active',
                      'bg-yellow-500': project.status === 'Archived',
                      'bg-blue-600': project.status === 'Completed',
                    }
                  ]"
                >
                  {{ project.status }}
                </span>
              </td>
              <td class="px-6 py-4 text-center">
                <div class="flex justify-center gap-4">
                  <Link :href="route('projects.edit', project.id)" class="text-white hover:text-purple-300 transition-all">✏️</Link>
                  <Link :href="route('projects.show', { projectId: project.id })" class="text-white hover:text-green-400 transition-all">🔍</Link>
                  <button @click="confirmDelete(project.id)" class="text-white hover:text-red-400 transition-all">🗑️</button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>


    <!-- Delete Confirmation Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-80 flex items-center justify-center z-50 backdrop-blur-lg">
      <div class="bg-purple-800 p-8 rounded-2xl shadow-2xl text-center w-full max-w-md transform transition-all duration-300 hover:scale-105">
        <h2 class="text-xl font-bold text-white mb-4">Potvrda brisanja</h2>
        <p class="text-gray-300 mb-6">Jeste li sigurni da želite obrisati ovaj projekat?</p>
        <div class="flex justify-center gap-6">
          <button @click="deleteProject" class="bg-red-600 text-white px-8 py-3 rounded-lg shadow-lg hover:bg-red-700 hover:scale-105 transition-all">Obriši</button>
          <button @click="showModal = false" class="bg-gray-700 px-8 py-3 rounded-lg shadow-md hover:bg-gray-800 hover:scale-105 transition-all">Otkaži</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <div class="h-24 mt-auto"><Footer /></div>
</template>

<script setup>
import { ref } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import Navbar from '@/components/Navbar.vue';
import Footer from '@/components/Footer.vue';

const props = defineProps({
  projects: Array,
  flash: Object,
});

const projects = ref(props.projects);
const showModal = ref(false);
const selectedProjectId = ref(null);
const flashMessage = ref(props.flash?.success);

const confirmDelete = (id) => {
  selectedProjectId.value = id;
  showModal.value = true;
};

const deleteProject = () => {
  router.visit(route('projects.destroy', selectedProjectId.value), {
    method: 'delete',
    onSuccess: () => {
      projects.value = projects.value.filter(p => p.id !== selectedProjectId.value);
      showModal.value = false;
      selectedProjectId.value = null;
    },
    onError: (errors) => {
      console.error(errors);
      alert(errors.error ?? 'Brisanje nije dozvoljeno.');
      showModal.value = false;
      selectedProjectId.value = null;
    },
  });
};
</script>

<style scoped>
</style>
