<template>
  <div
    class="bg-cover bg-center min-h-screen"
    style="background-image: url('/pozadina.jpg');"
  >
    <Navbar />

    <h1 class="text-3xl font-extrabold text-white text-center pt-10 drop-shadow-lg">
      Lista menadžera
    </h1>

    <ul class="grid grid-cols-1 md:grid-cols-2 gap-6 px-10 pt-10 max-w-6xl mx-auto">

      <li
        v-for="(manager, index) in managers"
        :key="index"
        class="bg-gradient-to-br from-purple-900 via-purple-900 to-violet-800 text-white px-6 py-4 rounded-2xl shadow-[0_4px_20px_rgba(0,0,0,0.8)] transition hover:scale-105 duration-300"
      >
        <div class="grid grid-cols-[1fr_auto_1fr] items-center gap-4">
          <div>
            <div class="text-xl font-semibold">
              {{ manager?.user.name ?? 'Nema naziva' }}
            </div>
            <div class="text-sm italic text-purple-300">
              Email: <br>
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
              <li
                v-for="team in getTeamsForManager(manager.id)"
                :key="team.id"
              >
                {{ team.team_name }}
              </li>
            </ul>
          </div>
        </div>

        <div class="mt-4 text-left">
          <button
            @click="openModal(manager)"
            class="bg-gradient-to-r from-violet-500 via-purple-500 to-fuchsia-600 text-white px-5 py-2 rounded-lg shadow-[0_8px_30px_rgba(139,92,246,0.7)] hover:shadow-[0_12px_40px_rgba(168,85,247,0.9)] hover:from-violet-600 hover:via-purple-600 hover:to-fuchsia-700 transition duration-300 inline-block"
          >
            Prikaži timove
          </button>
        </div>

      </li>
    </ul>

    <div
      v-if="showModal"
      class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50"
    >
      <div
        class="bg-gradient-to-br from-white via-purple-50 to-fuchsia-100 p-8 rounded-2xl shadow-2xl text-center w-[90%] max-w-lg"
      >
        <h2 class="text-xl font-bold mb-4 text-purple-900">
          Odaberi tim - {{ selectedmanager?.user.name }}
        </h2>

        <select
          v-model.number="selectedTeamId"
          @change="loadEmployees"
          class="w-full px-4 py-2 rounded-md border text-black border-purple-300 focus:ring-2 focus:ring-purple-500 mb-4"
        >
         <option :value="null" disabled>-- Izaberite tim --</option>
          <option
            v-for="team in selectedManagerTeams"
            :key="team.id"
            :value="team.id"
          >
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
              <span class="font-medium">{{ employee.first_name }} {{ employee.last_name }}</span>
            </div>
            <span class="text-sm text-purple-700 italic">#{{ employee.id }}</span>
          </li>
          <li
             v-if="selectedTeamId && modalEmployees.length === 0" 
            class="text-center text-gray-500 italic"
          >
            Nema zaposlenika u ovom timu.
          </li>
           <li
            v-if="!selectedTeamId" 
            class="text-center text-gray-500 italic"
          >
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
import { ref, computed } from 'vue' // Dodan computed

const props = defineProps({
  managers: Array,
  employees: Array,
  teams: Array,
  employeeTeams: Array
})

const showModal = ref(false)
const selectedmanager = ref(null)
const modalEmployees = ref([])
const selectedTeamId = ref(null) // Početna vrijednost null
const selectedManagerTeams = ref([])

const openModal = (manager) => {
  selectedmanager.value = manager;
  selectedManagerTeams.value = props.teams.filter(t => t.manager_id === manager.id);

  // Resetuj selekciju tima i zaposlenike svaki put kad se otvori modal
  selectedTeamId.value = null;
  modalEmployees.value = [];

  // Ako menadžer ima timove, možemo postaviti prvi kao default, ali bolje je pustiti korisnika da odabere
  // if (selectedManagerTeams.value.length > 0) {
  //   selectedTeamId.value = selectedManagerTeams.value[0].id;
  //   loadEmployees();
  // }

  showModal.value = true; // Pokaži modal na kraju
}


const loadEmployees = () => {
  // Resetuj listu ako tim nije odabran (npr. ako korisnik odabere default "-- Izaberite tim --")
  if (!selectedTeamId.value) {
      modalEmployees.value = [];
      return;
  }

  // Nađi sve employee_id koji pripadaju selected timu iz pivot tabele
  const employeeIds = props.employeeTeams
    .filter(et => et.team_id === Number(selectedTeamId.value))
    .map(et => et.employee_id);

  // Sada filtriraj sve employee iz props koji imaju ID iz te liste
  modalEmployees.value = props.employees.filter(emp =>
    employeeIds.includes(emp.id)
  );
}


const getTeamsForManager = (managerId) => {
  return props.teams.filter(t => t.manager_id === managerId);
}

</script>

<style lang="scss" scoped>
/* Stilovi za skrolbar ostaju isti */
ul::-webkit-scrollbar {
  width: 6px;
}
ul::-webkit-scrollbar-thumb {
  background: rgba(168, 85, 247, 0.4);
  border-radius: 4px;
}
</style>