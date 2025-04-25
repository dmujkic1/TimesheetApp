<template>
  <div
    class="min-h-screen flex flex-col bg-gray-50 text-gray-800"
    style="background-image: url('/pozadina.jpg');"
  >
    <Navbar />

    <div v-if="flashMessage" class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-4 mx-auto max-w-6xl">
      {{ flashMessage }}
    </div>

    <div class="max-w-6xl mx-auto px-6 py-6 flex justify-between items-center">
      <h1 class="text-3xl font-bold text-white">📂 Lista Projekata</h1>
      <Link
        :href="route('projects.create')"
        class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-md shadow"
      >
        ➕ Dodaj Projekat
      </Link>
    </div>

    <div class="overflow-x-auto max-w-6xl mx-auto px-6 pb-8">
      <table class="w-full bg-white shadow rounded-lg overflow-hidden border border-purple-200">
        <thead class="bg-purple-100 text-purple-900 text-sm font-semibold">
          <tr>
            <th class="px-5 py-3 text-left">Naziv</th>
            <th class="px-5 py-3 text-left">Klijent</th>
            <th class="px-5 py-3 text-left">Status</th>
            <th class="px-5 py-3 text-center">Akcije</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="project in projects"
            :key="project.id"
            class="border-t hover:bg-purple-50 transition"
          >
            <td class="px-5 py-3">{{ project.project_name }}</td>
            <td class="px-5 py-3">{{ project.client?.name ?? 'N/A' }}</td>
            <td class="px-5 py-3">
              <span
                :class="[
                  'inline-block px-2 py-1 rounded-full font-semibold text-sm',
                  {
                    'bg-green-500 text-white': project.status === 'Active',
                    'bg-yellow-500 text-white': project.status === 'Archived',
                    'bg-blue-500 text-white': project.status === 'Completed',
                  },
                ]"
              >{{ project.status }}</span>
            </td>
            <td class="px-5 py-3 text-center">
              <div class="flex justify-center gap-3 text-sm">
                <Link :href="route('projects.edit', project.id)" class="text-purple-600 hover:underline">✏️ Uredi</Link>
                <button @click="confirmDelete(project.id)" class="text-red-600 hover:underline">🗑️ Obriši</button>
                <Link :href="route('projects.show', { projectId: project.id })" class="text-green-600 hover:underline">🔍 Detalji</Link>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white p-6 rounded-xl shadow-xl text-center w-full max-w-sm">
        <h2 class="text-xl font-bold text-purple-700 mb-4">Potvrda brisanja</h2>
        <p class="text-gray-700 mb-4">Jeste li sigurni da želite obrisati ovaj projekat?</p>
        <div class="flex justify-center gap-4">
          <button @click="deleteProject" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Obriši</button>
          <button @click="showModal = false" class="bg-gray-200 px-4 py-2 rounded hover:bg-gray-300">Otkaži</button>
        </div>
      </div>
    </div>
  </div>
  <div class="h-24"><Footer /></div>
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