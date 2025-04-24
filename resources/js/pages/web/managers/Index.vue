<template>
  <div class="bg-cover bg-center min-h-screen" style="background-image: url('/pozadina.jpg');">
    <Navbar />

    <h1 class="text-3xl font-extrabold text-white text-center pt-10 drop-shadow-lg">
      Lista menadžera
    </h1>

    <ul class="grid grid-cols-1 md:grid-cols-2 gap-6 px-10 pt-10 max-w-6xl mx-auto">
      <li
        v-for="(manager, index) in pagination.data"
        :key="index"
        class="bg-gradient-to-br from-purple-900 via-purple-900 to-violet-800 text-white px-6 py-4 rounded-2xl shadow-[0_4px_20px_rgba(0,0,0,0.8)] transition hover:scale-105 duration-300"
      >
        <div class="grid grid-cols-[1fr_auto_1fr] items-center gap-4">
          <div>
            <div class="text-xl font-semibold">
              {{ manager?.user.name ?? 'Nema naziva' }}
            </div>
            <div class="text-sm italic text-purple-300">
              Email: <br />
              {{ manager.user.email ?? 'Nema emaila' }}
            </div>
          </div>

          <div class="justify-self-center mt-4 mb-2">
            <img
              v-if="manager?.user?.image"
              :src="manager.user.image"
              alt="Manager Image"
              class="w-24 h-24 rounded-full object-cover border-2 border-purple-500"
            />
            <img
              v-else
              src="/manager_profile_picture.png"
              alt="Manager Default Image"
              class="w-24 h-24 rounded-full object-cover border-2 border-purple-500"
            />
          </div>

          <div class="text-sm font-semibold text-purple-300 text-right">
            <span class="block text-white">Timovi:</span>
            <ul class="list-disc list-inside">
              <li v-for="team in getTeamsForManager(manager.id)" :key="team.id">
                {{ team.team_name }}
              </li>
            </ul>
          </div>
        </div>

        <div class="mt-4 text-left">
          <button
            @click="openModal(manager)"
            class="bg-gradient-to-r from-violet-500 via-purple-500 to-fuchsia-600 text-white px-5 py-2 rounded-lg shadow hover:from-violet-600 hover:via-purple-600 hover:to-fuchsia-700 transition duration-300 inline-block"
          >
            Prikaži timove
          </button>
        </div>
      </li>
    </ul>

    <!-- Navigacija kroz paginaciju -->
    <div class="flex justify-center items-center mt-6 mb-20 space-x-4">
      <!-- PRETHODNA -->
      <div class="w-[130px] flex justify-center">
        <button
          v-if="pagination.prev_page_url"
          @click="changePage(pagination.current_page - 1)"
          class="w-full text-center px-3 py-1 bg-purple-200 hover:bg-purple-300 text-purple-800 rounded"
        >
          ◀ Prethodna
        </button>
        <div v-else class="px-3 py-1 invisible">◀ Prethodna</div>
      </div>

      <!-- Brojevi stranica -->
      <div class="flex space-x-2 justify-center">
        <button
          v-for="page in pagination.last_page"
          :key="page"
          @click="changePage(page)"
          :class="[
            'px-3 py-1 rounded',
            page === pagination.current_page
              ? 'bg-purple-600 text-white'
              : 'bg-purple-100 hover:bg-purple-200 text-purple-800'
          ]"
        >
          {{ page }}
        </button>
      </div>

      <!-- SLJEDEĆA -->
      <div class="w-[130px] flex justify-center">
        <button
          v-if="pagination.next_page_url"
          @click="changePage(pagination.current_page + 1)"
          class="w-full text-center px-3 py-1 bg-purple-200 hover:bg-purple-300 text-purple-800 rounded"
        >
          Sljedeća ▶
        </button>
        <div v-else class="px-3 py-1 invisible">Sljedeća ▶</div>
      </div>
    </div>


    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50">
      <div class="bg-gradient-to-br from-white via-purple-50 to-fuchsia-100 p-8 rounded-2xl shadow-2xl text-center w-[90%] max-w-lg">
        <h2 class="text-xl font-bold mb-4 text-purple-900">
          Odaberi tim - {{ selectedmanager?.user.name }}
        </h2>

        <select
          v-model.number="selectedTeamId"
          @change="loadEmployees"
          class="w-full px-4 py-2 rounded-md border text-black border-purple-300 focus:ring-2 focus:ring-purple-500 mb-4"
        >
          <option :value="null" disabled>-- Izaberite tim --</option>
          <option v-for="team in selectedManagerTeams" :key="team.id" :value="team.id">
            {{ team.team_name }}
          </option>
        </select>

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
              <span class="font-medium">{{ employee.first_name }} {{ employee.last_name }} -- {{ employee.job_title }}</span>
            </div>
            <span class="text-sm text-purple-700 italic">#{{ employee.id }}</span>
          </li>
          <li v-if="selectedTeamId && modalEmployees.length === 0" class="text-center text-gray-500 italic">
            Nema zaposlenika u ovom timu.
          </li>
          <li v-if="!selectedTeamId" class="text-center text-gray-500 italic">
            Molimo odaberite tim da vidite zaposlenike.
          </li>
        </ul>

        <div class="mt-6 flex justify-center gap-4">
          <button
            @click="showModal = false"
            class="bg-gradient-to-r from-rose-500 to-pink-700 text-white px-5 py-2 rounded-lg shadow hover:from-rose-600 hover:to-pink-800 transition"
          >
            Zatvori
          </button>
        </div>
      </div>
    </div>
  </div>

  <div class="h-24"><Footer /></div>
</template>

<script setup>
import Navbar from '@/components/Navbar.vue'
import Footer from '@/components/Footer.vue'
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  pagination: Object,
  employees: Array,
  teams: Array,
  employeeTeams: Array
})

const showModal = ref(false)
const selectedmanager = ref(null)
const modalEmployees = ref([])
const selectedTeamId = ref(null)
const selectedManagerTeams = ref([])

const openModal = (manager) => {
  selectedmanager.value = manager
  selectedManagerTeams.value = props.teams.filter(t => t.manager_id === manager.id)
  selectedTeamId.value = null
  modalEmployees.value = []
  showModal.value = true
}

const changePage = (page) => {
  router.visit(route('managers.index', { page }), {
    preserveState: true,
    preserveScroll: true,
  })
}


const loadEmployees = () => {
  if (!selectedTeamId.value) {
    modalEmployees.value = []
    return
  }

  const employeeIds = props.employeeTeams
    .filter(et => et.team_id === Number(selectedTeamId.value))
    .map(et => et.employee_id)

  modalEmployees.value = props.employees.filter(emp =>
    employeeIds.includes(emp.id)
  )
}

const getTeamsForManager = (managerId) => {
  return props.teams.filter(t => t.manager_id === managerId)
}
</script>

<style scoped>
ul::-webkit-scrollbar {
  width: 6px;
}
ul::-webkit-scrollbar-thumb {
  background: rgba(168, 85, 247, 0.4);
  border-radius: 4px;
}
</style>
