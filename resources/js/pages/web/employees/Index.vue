<template>
    <div>
      <Navbar />
      <h1 class="text-xl font-bold">Lista uposlenika</h1>
      <ul>
        <li v-for="(employee, index) in employees" :key="index">
          {{ employee?.first_name ?? "Nema naziva" }} - {{ employee?.email ?? "Nema maila" }}
          <Link :href="`/employees/modify/${employee.id}`" class="text-blue-500 ml-2">Edit</Link>
          <button @click="confirmDelete(employee.id)" class="text-red-500 ml-2">Delete</button>
        </li>
      </ul>
  
      <!-- Modal -->
      <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="p-6 rounded shadow-md text-center">
          <h2 class="text-lg font-bold mb-4">Potvrda brisanja</h2>
          <p>Jeste li sigurni da želite obrisati ovog uposlenika?</p>
          <div class="mt-4 flex justify-center gap-4">
            <button @click="deleteEmployee" class="bg-red-500 text-white px-4 py-2 rounded">Obriši</button>
            <button @click="showModal = false" class="bg-gray-300 px-4 py-2 rounded">Otkaži</button>
          </div>
        </div>
      </div>
  
      <Footer />
    </div>
  </template>
  
  
  <script setup>
  import { Link, router } from '@inertiajs/vue3'
  import Navbar from '@/components/Navbar.vue'
  import Footer from '@/components/Footer.vue'
  import { usePage } from '@inertiajs/vue3'
  import { ref } from 'vue'
  
  const { props } = usePage()
  const employees = ref(props.employees)
  
  const showModal = ref(false)
  const selectedEmployeeId = ref(null)
  
  const confirmDelete = (id) => {
    selectedEmployeeId.value = id
    showModal.value = true
  }
  
  const deleteEmployee = () => {
    router.delete(`/employees/delete/${selectedEmployeeId.value}`, {
      onSuccess: () => {
        employees.value = employees.value.filter(emp => emp.id !== selectedEmployeeId.value)
        showModal.value = false
        selectedEmployeeId.value = null
      },
      onError: (errors) => {
        console.error(errors)
      }
    })
  }
  </script>
  