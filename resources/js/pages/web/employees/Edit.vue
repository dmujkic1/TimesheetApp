<template>
    <div>
      <Navbar />
      <h1 class="text-xl font-bold mb-4">Uredi podatke uposlenika</h1>
      
      <form @submit.prevent="submit">
        <div class="mb-4">
          <label class="block text-gray-700">Ime:</label>
          <input v-model="form.first_name" type="text" class="w-full border rounded px-3 py-2" />
          <span class="text-red-500 text-sm" v-if="form.errors.first_name">{{ form.errors.first_name }}</span>
        </div>
  
        <div class="mb-4">
          <label class="block text-gray-700">Prezime:</label>
          <input v-model="form.last_name" type="text" class="w-full border rounded px-3 py-2" />
          <span class="text-red-500 text-sm" v-if="form.errors.last_name">{{ form.errors.last_name }}</span>
        </div>
  
        <div class="mb-4">
          <label class="block text-gray-700">Email:</label>
          <input v-model="form.email" type="email" class="w-full border rounded px-3 py-2" />
          <span class="text-red-500 text-sm" v-if="form.errors.email">{{ form.errors.email }}</span>
        </div>
  
        <div class="mb-4">
          <label class="block text-gray-700">Titula:</label>
          <input v-model="form.job_title" type="text" class="w-full border rounded px-3 py-2" />
          <span class="text-red-500 text-sm" v-if="form.errors.job_title">{{ form.errors.job_title }}</span>
        </div>
  
        <div class="mb-4">
          <label class="block text-gray-700">Datum zaposlenja:</label>
          <input v-model="form.hire_date" type="date" class="w-full border rounded px-3 py-2" />
          <span class="text-red-500 text-sm" v-if="form.errors.hire_date">{{ form.errors.hire_date }}</span>
        </div>

        <div class="mb-4">
          <label class="block text-gray-700">Status:</label>
          <select v-model="form.status" class="w-full border rounded px-3 py-2">
            <option value="aktivan">Aktivan</option>
            <option value="neaktivan">Neaktivan</option>
          </select>
          <span class="text-red-500 text-sm" v-if="form.errors.status">{{ form.errors.status }}</span>
        </div>
  
        <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Ažuriraj</button>
      </form>
  
      <Footer />
    </div>
  </template>
  
  <script setup>
  import { useForm, usePage } from '@inertiajs/vue3'
  import Navbar from '@/components/Navbar.vue'
  import Footer from '@/components/Footer.vue'
  
  const { props } = usePage()
  const employee = props.employee
  
  const form = useForm({
    first_name: employee.first_name || '',
    last_name: employee.last_name || '',
    email: employee.email || '',
    job_title: employee.job_title || '',
    hire_date: employee.hire_date || '',
    status: employee.status || '',
  })
  
  const submit = () => {
    form.put(`/employees/update/${employee.id}`)
  }
  </script>
  