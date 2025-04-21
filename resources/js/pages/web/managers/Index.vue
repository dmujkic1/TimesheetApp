<template>
  <div
    class="bg-cover bg-center min-h-screen"
    style="background-image: url('/pozadina.jpg');"
  >
    <Navbar />

    <h1 class="text-3xl font-extrabold text-white text-center pt-10 drop-shadow-lg">
      Lista menadžera
    </h1>

    <ul class="space-y-6 px-10 pt-10 max-w-3xl mx-auto">
      <li
        v-for="(manager, index) in managers"
        :key="index"
        class="bg-gradient-to-br from-purple-900 via-purple-900 to-violet-800 text-white px-6 py-4 rounded-2xl shadow-[0_4px_20px_rgba(0,0,0,0.8)] transition hover:scale-105 duration-300"
      >
        <div class="text-xl font-semibold">
          {{ manager?.user?.name ?? 'Nema naziva' }}
        </div>
        <button
          @click="openModal(manager.user)"
          class="mt-4 bg-gradient-to-r from-violet-500 via-purple-500 to-fuchsia-600 text-white px-5 py-2 rounded-lg shadow-[0_8px_30px_rgba(139,92,246,0.7)] hover:shadow-[0_12px_40px_rgba(168,85,247,0.9)] hover:from-violet-600 hover:via-purple-600 hover:to-fuchsia-700 transition duration-300"
        >
          Prikaži zaposlenike
        </button>
      </li>
    </ul>

    <!-- Modal -->
    <div
      v-if="showModal"
      class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50"
    >
      <div
        class="bg-gradient-to-br from-white via-purple-50 to-fuchsia-100 p-8 rounded-2xl shadow-2xl text-center w-[90%] max-w-lg"
      >
        <h2 class="text-xl font-bold mb-4 text-purple-900">
          Uposlenici - {{ selectedmanager }}
        </h2>

        <!-- Lista zaposlenika -->
        <ul class="space-y-3 mt-4 text-left max-h-60 overflow-y-auto pr-1">
          <li
            v-for="(employee, index) in modalEmployees"
            :key="index"
            class="flex items-center justify-between bg-gradient-to-r from-purple-200 via-purple-100 to-fuchsia-100 text-purple-900 px-4 py-2 rounded-xl shadow-sm hover:scale-[1.01] transition duration-200"
          >
            <div class="flex items-center gap-3">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M5.121 17.804A9.004 9.004 0 0012 21a9.004 9.004 0 006.879-3.196M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              <span class="font-medium">{{ employee.first_name }} {{ employee.last_name }}</span>
            </div>
            <span class="text-sm text-purple-700 italic">#{{ employee.id }}</span>
          </li>
          <li
            v-if="modalEmployees.length === 0"
            class="text-center text-gray-500 italic"
          >
            Nema zaposlenika u ovom timu.
          </li>
        </ul>

        <div class="mt-6 flex justify-center gap-4">
          <button
            @click="showModal = false"
            class="bg-gradient-to-r from-rose-500 to-pink-700 text-white px-5 py-2 rounded-lg shadow hover:from-rose-600 hover:to-pink-800 transition"
          >
            Otkaži
          </button>
        </div>
      </div>
    </div>
  </div>
  <!-- Rezerviši visinu footera -->
  <div class="h-24"><Footer /></div>
</template>

<script setup>
import Navbar from '@/components/Navbar.vue'
import Footer from '@/components/Footer.vue'
import { ref } from 'vue'

const props = defineProps({
  managers: {
    type: Array,
    required: true
  },
  employees: {
    type: Array,
    required: true
  },
  teams: {
    type: Array,
    required: true
  }
})

const showModal = ref(false)
const selectedmanager = ref(null)
const modalEmployees = ref([])

const openModal = (manager) => {
  selectedmanager.value = manager.name
  showModal.value = true

  const managerTeam = props.teams.find(team => team.manager_id === manager.id)

  if (managerTeam) {
    modalEmployees.value = props.employees.filter(emp => emp.team_id === managerTeam.id)
  } else {
    modalEmployees.value = []
  }

  console.log('Otvoren modal za:', modalEmployees.value)
}
</script>

<style lang="scss" scoped>
/* Ako želiš custom scrollbar za modal listu */
ul::-webkit-scrollbar {
  width: 6px;
}
ul::-webkit-scrollbar-thumb {
  background: rgba(168, 85, 247, 0.4);
  border-radius: 4px;
}
</style>
