<template>
  <div class="p-6">
    <!-- Prikazivanje flash poruke -->
    <div v-if="flashMessage" class="bg-green-500 text-white p-4 rounded mb-4">
      {{ flashMessage }}
    </div>

    <h1 class="text-2xl font-bold mb-4">Projekti</h1>

    <div class="mb-4">
      <Link
        :href="route('projects.create')"
        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded"
      >
        + Dodaj projekat
      </Link>
    </div>

    <table class="w-full bg-white shadow rounded overflow-hidden">
      <thead class="bg-gray-100 text-black text-sm text-left uppercase font-semibold">
        <tr>
          <th class="px-4 py-3">Naziv</th>
          <th class="px-4 py-3">Klijent</th>
          <th class="px-4 py-3">Status</th>
          <th class="px-4 py-3">Akcije</th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="project in projects"
          :key="project.id"
          class="border-t hover:bg-gray-50 text-black"
        >
          <td class="px-4 py-2">{{ project.project_name }}</td>
          <td class="px-4 py-2">{{ project.client_name ?? 'N/A' }}</td>
          <td class="px-4 py-2">{{ project.status }}</td>
          <td class="px-4 py-2 space-x-2">
            <Link
              :href="route('projects.edit', project.id)"
              class="text-blue-600 hover:underline"
            >
              Uredi
            </Link>
            <button
              @click="confirmDelete(project.id)"
              class="text-red-600 hover:underline"
            >
              Obriši
            </button>
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Modal za potvrdu brisanja -->
    <div
      v-if="showModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
      <div class="bg-white p-6 rounded-lg w-full max-w-md shadow-lg">
        <h2 class="text-black text-lg font-semibold mb-4">
          Da li ste sigurni da želite obrisati ovaj projekat?
        </h2>
        <div class="flex justify-end gap-3">
          <button
            @click="showModal = false"
            class="px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded"
          >
            Otkaži
          </button>
          <button
            @click="deleteProject"
            class="px-4 py-2 bg-red-600 text-white hover:bg-red-700 rounded"
          >
            Obriši
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { router, Link } from '@inertiajs/vue3'

// Props iz controllera
const props = defineProps({
  projects: Array,
  flash: Object  // Dodajemo 'flash' objekat koji sadrži poruke sa servera
})

// Lokalne reaktivne vrijednosti
const projects = ref(props.projects)
const showModal = ref(false)
const selectedProjectId = ref(null)
const flashMessage = ref(props.flash.success)  // Uzmi flash poruku (ako postoji)

const confirmDelete = (id) => {
  selectedProjectId.value = id
  showModal.value = true
}

// Funkcija za brisanje
const deleteProject = () => {
  router.visit(route('projects.destroy', selectedProjectId.value), {
    method: 'delete',
    onSuccess: () => {
      projects.value = projects.value.filter(p => p.id !== selectedProjectId.value)
      showModal.value = false
      selectedProjectId.value = null
    },
    onError: (errors) => {
      console.error(errors)
      alert(errors.error ?? 'Brisanje nije dozvoljeno.')
      showModal.value = false
      selectedProjectId.value = null
    }
  })
}
</script>
