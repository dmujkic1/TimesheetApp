<template>
  <div
    class="min-h-screen flex flex-col bg-gradient-to-r from-purple-600 to-indigo-800 text-gray-50"
    style="background-image: url('/pozadina.jpg'); background-size: cover; background-position: center;"
  >
    <Navbar />

    <main class="flex-grow px-6 py-12 max-w-2xl mx-auto">
      <h1 class="text-5xl font-extrabold text-center text-purple-400 mb-10">
        ✏️ Uredi podatke uposlenika
      </h1>

      <form @submit.prevent="submit" class="bg-black/60 backdrop-blur-md rounded-3xl border border-purple-500 space-y-6 p-8 shadow-lg">
        <!-- Ime -->
        <div>
          <label class="block text-white text-2xl font-semibold mb-1">Ime:</label>
          <input
            v-model="form.first_name"
            type="text"
            class="w-full bg-gray-700 text-purple-400  border-gray-300 rounded-2xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-purple-500 shadow-md hover:shadow-lg hover:shadow-purple-600/50 transform hover:scale-105 transition-all duration-200"
            placeholder="Unesite ime"
          />
          <span class="text-red-500 text-sm" v-if="form.errors.first_name">{{ form.errors.first_name }}</span>
        </div>

        <!-- Prezime -->
        <div>
          <label class="block text-white text-2xl font-semibold mb-1">Prezime:</label>
          <input
            v-model="form.last_name"
            type="text"
            class="w-full bg-gray-700 text-purple-400 border-gray-300 rounded-2xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-purple-500 shadow-md hover:shadow-lg hover:shadow-purple-600/50 transform hover:scale-105 transition-all duration-200"
            placeholder="Unesite prezime"
          />
          <span class="text-red-500 text-sm" v-if="form.errors.last_name">{{ form.errors.last_name }}</span>
        </div>

        <!-- Email -->
        <div>
          <label class="block text-white text-2xl font-semibold mb-1">Email:</label>
          <input
            v-model="form.email"
            type="email"
            class="w-full bg-gray-700 text-purple-400  border-gray-300 rounded-2xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-purple-500 shadow-md hover:shadow-lg hover:shadow-purple-600/50 transform hover:scale-105 transition-all duration-200"
            placeholder="Unesite email"
          />
          <span class="text-red-500 text-sm" v-if="form.errors.email">{{ form.errors.email }}</span>
        </div>

        <!-- Titula -->
        <div>
          <label class="block text-white text-2xl font-semibold mb-1">Titula:</label>
          <input
            v-model="form.job_title"
            type="text"
            class="w-full bg-gray-700 text-purple-400  border-gray-300 rounded-2xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-purple-500 shadow-md hover:shadow-lg hover:shadow-purple-600/50 transform hover:scale-105 transition-all duration-200"
            placeholder="Unesite titulu"
          />
          <span class="text-red-500 text-sm" v-if="form.errors.job_title">{{ form.errors.job_title }}</span>
        </div>

        <!-- Datum zaposlenja -->
        <div>
          <label class="block text-white text-2xl font-semibold mb-1">Datum zaposlenja:</label>
          <input
            v-model="form.hire_date"
            type="date"
            :max="today"
            class="w-full bg-gray-700 text-purple-400  border-gray-300 rounded-2xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-purple-500 shadow-md hover:shadow-lg hover:shadow-purple-600/50 transform hover:scale-105 transition-all duration-200"
          />
          <span class="text-red-500 text-sm" v-if="form.errors.hire_date">{{ form.errors.hire_date }}</span>
        </div>

        <!-- Status -->
        <div>
          <label class="block text-white text-2xl font-semibold mb-1">Status:</label>
          <select
            v-model="form.status"
            class="w-full bg-gray-700 text-purple-400  border-gray-300 rounded-2xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-purple-500 shadow-md hover:shadow-lg hover:shadow-purple-600/50 transform hover:scale-105 transition-all duration-200"
          >
            <option value="aktivan">Aktivan</option>
            <option value="neaktivan">Neaktivan</option>
          </select>
          <span class="text-red-500 text-sm" v-if="form.errors.status">{{ form.errors.status }}</span>
        </div>

        <!-- Submit Button -->
        <div class="pt-6">
          <button
            type="submit"
            class="bg-purple-600 hover:bg-purple-700 text-white px-8 py-3 rounded-xl shadow-xl hover:shadow-2xl transition-transform transform hover:scale-105 font-semibold"
          >
            Ažuriraj
          </button>
        </div>
      </form>
    </main>

    <!-- Rezerviši visinu footera -->
    <div class="h-24">
      <Footer />
    </div>
  </div>
</template>

<script setup>
import { useForm, usePage } from '@inertiajs/vue3'
import Navbar from '@/components/Navbar.vue'
import Footer from '@/components/Footer.vue'

const { props } = usePage()
const employee = props.employee
const today = new Date().toISOString().split('T')[0]

const formatDate = (dateString) => {
  if (!dateString) return ''
  return dateString.split(' ')[0]
}

const form = useForm({
  first_name: employee.first_name || '',
  last_name: employee.last_name || '',
  email: employee.email || '',
  job_title: employee.job_title || '',
  hire_date: formatDate(employee.hire_date) || '',
  status: employee.status === true ? 'aktivan' : 'neaktivan',
})

const submit = () => {
  form.put(`/employees/update/${employee.id}`)
}
</script>
