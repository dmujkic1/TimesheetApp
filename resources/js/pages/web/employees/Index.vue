<template>
  <div class="min-h-screen flex flex-col bg-gray-50 text-gray-800"
      style="background-image: url('/pozadina.jpg');">
    <Navbar />

    <!-- Flash poruke -->
    <div v-if="props?.flash?.error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4 mx-auto max-w-4xl">
      {{ props.flash.error }}
    </div>
    <div v-if="props?.flash?.success" class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-4 mx-auto max-w-4xl">
      {{ props.flash.success }}
    </div>

    <!-- Naslov i dugme -->
    <div class="max-w-6xl mx-auto px-4 py-6 flex justify-between items-center">
      <h1 class="text-3xl font-bold text-white">📋 Lista uposlenika</h1>
      <Link href="/employees/add" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-md shadow">
        ➕ Dodaj zaposlenika
      </Link>
    </div>

    <!-- Tabela -->
    <div class="overflow-x-auto max-w-6xl mx-auto px-4">
      <table class="w-full bg-white shadow rounded-lg overflow-hidden border border-purple-200">
        <thead class="bg-purple-100 text-purple-900 text-sm font-semibold">
          <tr>
            <th class="px-5 py-3 text-left">#</th>
            <th class="px-5 py-3 text-left">Ime</th>
            <th class="px-5 py-3 text-left">Email</th>
            <th class="px-5 py-3 text-center">Akcije</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="employee in pagination.data" :key="employee.id"
              class="border-t hover:bg-purple-50 transition">
            <td class="px-5 py-3">{{ employee?.id }}</td>
            <td class="px-5 py-3">{{ employee?.first_name ?? "Nepoznato ime" }}</td>
            <td class="px-5 py-3">{{ employee?.email ?? "Nepoznat email" }}</td>
            <td class="px-5 py-3 text-center">
              <div class="flex justify-center gap-3 text-sm">
                <Link :href="`/employees/edit/${employee.id}`" class="text-purple-600 hover:underline">✏️ Uredi</Link>
                <Link :href="`/employees/get/${employee.id}`" class="text-green-600 hover:underline">🔍 Detalji</Link>
                <button @click="confirmDelete(employee.id)" class="text-red-600 hover:underline">🗑️ Obriši</button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
      <!-- Navigacija kroz paginaciju -->
      <div class="flex justify-center items-center mt-6 mb-20 space-x-2">
        <button
          v-if="pagination.prev_page_url"
          @click="changePage(pagination.current_page - 1)"
          class="px-3 py-1 bg-purple-200 hover:bg-purple-300 text-purple-800 rounded"
        >
          ◀ Prethodna
        </button>

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

        <button
          v-if="pagination.next_page_url"
          @click="changePage(pagination.current_page + 1)"
          class="px-3 py-1 bg-purple-200 hover:bg-purple-300 text-purple-800 rounded"
        >
          Sljedeća ▶
        </button>
      </div>

    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white p-6 rounded-xl shadow-xl text-center w-full max-w-sm">
        <h2 class="text-xl font-bold text-purple-700 mb-2">Potvrda brisanja</h2>
        <p class="text-gray-700 mb-4">Jeste li sigurni da želite obrisati ovog uposlenika?</p>
        <div class="flex justify-center gap-4">
          <button @click="deleteEmployee" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Obriši</button>
          <button @click="showModal = false" class="bg-gray-200 px-4 py-2 rounded hover:bg-gray-300">Otkaži</button>
        </div>
      </div>
    </div>
  </div>
  <!-- Rezerviši visinu footera -->
  <div class="h-24"><Footer /></div>
</template>
  
  
  <script setup>
  import { Link, router } from '@inertiajs/vue3'
  import Navbar from '@/components/Navbar.vue'
  import Footer from '@/components/Footer.vue'
  import { usePage } from '@inertiajs/vue3'
  import { computed } from 'vue'
  import { ref } from 'vue'
  
  const changePage = (page) => {
    router.visit(route('employees.index', { page }), {
      preserveState: true,
      preserveScroll: true,
    })
  }

  //const { props } = usePage()
  const props = defineProps({
    pagination: Object,
    flash: Object
  })
  const employees = ref(props.pagination.data)
  
  const showModal = ref(false)
  const selectedEmployeeId = ref(null)
  
  const confirmDelete = (id) => {
    selectedEmployeeId.value = id
    showModal.value = true
  }
  
  const deleteEmployee = () => {
    router.visit(route('employees.destroy', selectedEmployeeId.value), {
      method: 'delete',
      onSuccess: () => {
        //employees.value = employees.value.filter(emp => emp.id !== selectedEmployeeId.value)
        showModal.value = false
        selectedEmployeeId.value = null
      },
      onError: (errors) => {
        console.error(errors)
        alert(errors.error ?? "Brisanje nije dozvoljeno.")
        showModal.value = false
        selectedEmployeeId.value = null
      }
    })
  }
  </script>
  