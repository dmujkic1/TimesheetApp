<template>
  <div class="min-h-screen flex flex-col bg-gray-50 text-gray-800"
  style="background-image: url('/pozadina.jpg');">
    <Navbar />

    <main class="flex-grow px-4 py-8 max-w-2xl mx-auto">
      <h1 class="text-3xl font-bold text-white mb-6">➕ Dodaj novog uposlenika</h1>

      <form @submit.prevent="submit" class="bg-white p-6 rounded-lg shadow-md border border-purple-200 space-y-5">
        <div>
          <label class="block text-purple-700 font-semibold mb-1">Ime:</label>
          <input v-model="form.first_name" type="text" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-400" />
          <span class="text-red-500 text-sm" v-if="form.errors.first_name">{{ form.errors.first_name }}</span>
        </div>

        <div>
          <label class="block text-purple-700 font-semibold mb-1">Prezime:</label>
          <input v-model="form.last_name" type="text" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-400" />
          <span class="text-red-500 text-sm" v-if="form.errors.last_name">{{ form.errors.last_name }}</span>
        </div>

        <div>
          <label class="block text-purple-700 font-semibold mb-1">Email:</label>
          <input v-model="form.email" type="email" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-400" />
          <span class="text-red-500 text-sm" v-if="form.errors.email">{{ form.errors.email }}</span>
        </div>

        <div>
          <label class="block text-purple-700 font-semibold mb-1">Titula:</label>
          <input v-model="form.job_title" type="text" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-400" />
          <span class="text-red-500 text-sm" v-if="form.errors.job_title">{{ form.errors.job_title }}</span>
        </div>

        <div>
          <label class="block text-purple-700 font-semibold mb-1">Datum zaposlenja:</label>
          <input v-model="form.hire_date" type="date" :max="today" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-400" />
          <span class="text-red-500 text-sm" v-if="form.errors.hire_date">{{ form.errors.hire_date }}</span>
        </div>

        <div class="pt-4">
          <button type="submit" class="bg-purple-600 text-white px-6 py-2 rounded hover:bg-purple-700 transition duration-200">
            Spasi
          </button>
        </div>
      </form>
    </main>
  </div>
  <!-- Rezerviši visinu footera -->
  <div class="h-24"><Footer /></div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import Navbar from '@/components/Navbar.vue'
import Footer from '@/components/Footer.vue'
const today = new Date().toISOString().split('T')[0];


const form = useForm({
  first_name: '',
  last_name: '',
  email: '',
  job_title: '',
  hire_date: new Date().toISOString().split('T')[0],
  status: 'aktivan',
})

const submit = () => {
  console.log("Submitting form...");
  form.post('/employees/store', {
    onError: (errors) => {
      console.log(errors)
    }
  })
}
</script>
