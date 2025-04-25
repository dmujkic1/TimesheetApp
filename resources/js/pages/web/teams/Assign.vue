<template>
    <div class="bg-cover bg-center min-h-screen" style="background-image: url('/pozadina.jpg');">
      <Navbar />
      <div class="flex flex-col items-center justify-start min-h-[calc(100vh - 6rem)] py-10 px-4">
        <h1 class="text-3xl font-bold text-white mb-8">👨‍💻 Dodjela zaposlenika timu</h1>

        <!-- Flash poruke -->
        <div v-if="props?.flash?.error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4">
          {{ props.flash.error }}
        </div>
        <div v-if="props?.flash?.success" class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-4">
          {{ props.flash.success }}
        </div>
  
         <!-- Forma -->
        <form @submit.prevent="submit" class="max-w-4xl w-full bg-white shadow-md rounded px-8 pt-6 pb-8 border border-purple-300">
  
        <!-- Odabir tima -->
          <div class="mb-6">
            <label class="block text-purple-800 font-semibold mb-2">Odaberi tim</label>
            <select
              v-model="form.team_id"
              class="w-full border border-purple-300 rounded px-4 py-2 text-black focus:ring-2 focus:ring-purple-500 focus:outline-none"
            >
              <option disabled value="">-- Izaberi tim --</option>
              <option v-for="team in teams" :key="team.id" :value="team.id">
                {{ team.team_name }}
              </option>
            </select>
          </div>

          <!-- Odabir zaposlenika -->
          <div class="mb-6" v-if="form.team_id">
            <label class="block text-purple-800 font-semibold mb-2">Zaposlenici</label>
            <div class="grid grid-cols-3 gap-2 max-h-64 overflow-y-auto border border-purple-300 p-3 rounded">
              <label
                v-for="employee in employees"
                :key="employee.id"
                class="flex items-center gap-2 text-gray-800"
              >
                <input
                  type="checkbox"
                  :value="employee.id"
                  v-model="form.employee_ids"
                  class="accent-purple-600"
                />
                {{ employee.first_name }} {{ employee.last_name }}
              </label>
            </div>
          </div>
  
           <!-- Dugme -->
          <div class="text-right">
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
  
    <!-- Rezerviši visinu za footer -->
    <div class="h-24"><Footer /></div>
  </template>
  
  <script setup>
  import Footer from '@/components/Footer.vue'
  import Navbar from '@/components/Navbar.vue'
  import { useForm, usePage } from '@inertiajs/vue3'
  import { watch } from 'vue'
  
  const props = defineProps({
    teams: Array,
    employees: Array,
    flash: Object
  })
  
  //const flash = usePage().props.flash
  
  const form = useForm({
    team_id: '',
    employee_ids: []
  })
  
  // Kada se izabere tim, automatski označi njegove članove
  watch(() => form.team_id, (newTeamId) => {
    const selectedTeam = props.teams.find(team => team.id === newTeamId)
    if (selectedTeam && selectedTeam.employee) {
      form.employee_ids = selectedTeam.employee.map(e => e.id)
    } else {
      form.employee_ids = []
    }
  })
  
  const submit = () => {
    form.post(route('teams.assign'), {
      onSuccess: () => {
        window.location.reload()
      }
    })
  }
  </script>