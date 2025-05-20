<template>
  <div class="bg-cover bg-center min-h-screen" style="background-image: url('/pozadina.jpg');">
    <Navbar />
    <div class="flex flex-col items-center justify-start min-h-[calc(100vh - 6rem)] py-10 px-4">

      <!-- Flash poruke -->
      <div v-if="props?.flash?.error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4">
        {{ props.flash.error }}
      </div>
      <div v-if="props?.flash?.success" class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-4">
        {{ props.flash.success }}
      </div>

      <!-- Lista Projekata Header -->
      <div class="w-full max-w-8xl mx-auto px-4 mt-10">
        <div class="flex items-center justify-between bg-black/60 backdrop-blur-md rounded-xl shadow-lg px-6 py-5">
          <h1 class="text-3xl font-bold text-white">📂 Dodjela zaposlenika timu</h1>
        </div>
      </div>

      <form @submit.prevent="submit" class="w-full max-w-8xl mx-auto px-4 mt-10 bg-black/60 backdrop-blur-md rounded-xl shadow-lg py-5">
        
        <!-- Odabir tima -->
        <div class="mb-6">
          <label class="block text-purple-400 font-semibold mb-2">Odaberi tim</label>
          <select 
            v-model="form.team_id"
            class="w-full border  border-purple-300 rounded px-4 py-2 text-purple-500 focus:ring-2 focus:ring-purple-500 focus:outline-none"
          >
            <option disabled value="">-- Izaberi tim --</option>
            <option v-for="team in props.teams" :key="team.id" :value="team.id">
              {{ team.team_name }}
            </option>
          </select>
        </div>

        <div class="mb-6" v-if="form.team_id">
          <label class="block text-white font-semibold mb-2">Zaposlenici</label>
          <div class="grid grid-cols-2 gap-6">
            
            <!--Dodijeljeni-->
            <div class="text-white border border-purple-300 rounded p-3">
              <h2 class="text-green-400 font-semibold mb-2">U timu</h2>
              <ul>
                <li
                  v-for="employee in assignedEmployees"
                  :key="employee.id"
                  class="flex justify-between items-center mb-1 hover:bg-purple-300 hover:text-gray-900  cursor-pointer w-full bg-gray-700 text-purple-400  border-gray-300 rounded-2xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-purple-500 shadow-md"
                  @click="removeEmployee(employee)"
                >
                  {{ employee.first_name }} {{ employee.last_name }}
                  <span class="text-red-600">❌</span>
                </li>
              </ul>
            </div>

            <!-- Slobodni -->
            <div class="border border-purple-300 rounded p-3">
              <h2 class="text-yellow-300 font-semibold mb-2">Dostupni</h2>
              <ul>
                <li
                  v-for="employee in availableEmployees"
                  :key="employee.id"
                  class="flex justify-between items-center mb-1 hover:bg-purple-300 hover:text-gray-900  cursor-pointer w-full bg-gray-700 text-purple-400  border-gray-300 rounded-2xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-purple-500 shadow-md"
                  @click="assignEmployee(employee)"
                >
                  {{ employee.first_name }} {{ employee.last_name }}
                  <span class="text-green-600">✅</span>
                </li>
              </ul>
            </div>
          </div>
        </div>

        <!-- Dugme -->
        <div class="text-right mt-6">
          <button
            type="submit"
            class="bg-purple-600 hover:bg-purple-700 text-white font-semibold px-6 py-2 rounded transition duration-200"
          >
            Ažuriraj tim
          </button>
        </div>
      </form>
    </div>
  </div>

  <div class="h-24"><Footer /></div>
</template>

<script setup>
import Footer from '@/components/Footer.vue'
import Navbar from '@/components/Navbar.vue'
import { useForm } from '@inertiajs/vue3'
import { ref, computed, watch } from 'vue'

const props = defineProps({
  teams: Array,
  employees: Array,
  flash: Object
})

const form = useForm({
  team_id: '',
  employee_ids: []
})

const assignedEmployees = ref([])

const availableEmployees = computed(() => {
  return props.employees.filter(
    emp => !assignedEmployees.value.some(e => e.id === emp.id)
  )
})

// Kada se izabere tim odma mi označi članove
watch(() => form.team_id, (newTeamId) => {
  const selectedTeam = props.teams.find(team => team.id === newTeamId)
  if (selectedTeam && selectedTeam.employee) {
    assignedEmployees.value = [...selectedTeam.employee]
    form.employee_ids = selectedTeam.employee.map(e => e.id)
  } else {
    assignedEmployees.value = []
    form.employee_ids = []
  }
})

// Dodavanje/uklanjanje zaposlenika
const assignEmployee = (employee) => {
  if (!assignedEmployees.value.some(e => e.id === employee.id)) {
    assignedEmployees.value.push(employee)
  }

  if (!form.employee_ids.includes(employee.id)) {
    form.employee_ids.push(employee.id)
  }
}

const removeEmployee = (employee) => {
  assignedEmployees.value = assignedEmployees.value.filter(e => e.id !== employee.id)
  form.employee_ids = form.employee_ids.filter(id => id !== employee.id)
}

// Submit
const submit = () => {
  form.post(route('teams.assign'), {
    onSuccess: () => {
      window.location.reload()
    }
  })
}
</script>