<template>
    <div class="bg-cover bg-center min-h-screen" style="background-image: url('/pozadina.jpg');">
        <div class="max-w-3xl mx-auto py-10 px-4">
        <h1 class="text-3xl font-bold text-white mb-8">Dodjela zaposlenika timu</h1>
    
        <!-- Flash poruke -->
        <div v-if="flash.success" class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4 border border-green-300">
            {{ flash.success }}
        </div>
        <div v-if="flash.error" class="bg-red-100 text-red-800 px-4 py-2 rounded mb-4 border border-red-300">
            {{ flash.error }}
        </div>
    
        <!-- Forma -->
        <form @submit.prevent="submit" class="bg-white shadow-md rounded px-8 pt-6 pb-8 border border-purple-300">
            
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
            <div class="mb-6">
            <label class="block text-purple-800 font-semibold mb-2">Odaberi zaposlenike</label>
            <div class="grid grid-cols-2 gap-2 max-h-64 overflow-y-auto border border-purple-300 p-3 rounded">
                <label v-for="employee in employees" :key="employee.id" class="flex items-center gap-2 text-gray-800">
                <input type="checkbox" :value="employee.id" v-model="form.employee_ids" class="accent-purple-600" />
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
                Dodijeli
            </button>
            </div>
        </form>
        </div>
    </div>
  </template>
  
  <script setup>
  import { useForm, usePage } from '@inertiajs/vue3'
  
  const props = defineProps({
    teams: Array,
    employees: Array,
  })
  
  const flash = usePage().props.flash
  
  const form = useForm({
    team_id: '',
    employee_ids: []
  })
  
  const submit = () => {
    form.post(route('teams.assign'))
  }
  </script>
  