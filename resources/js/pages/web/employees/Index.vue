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

    <div class="max-w-6xl mx-auto px-4 py-4 flex justify-between items-center">
      <input
        v-model="search"
        @keydown.enter="searchEmployees"
        type="text"
        placeholder="Pretraži po imenu..."
        class="bg-white text-gray-800 border border-gray-300 rounded px-3 py-2 w-64 focus:outline-none focus:ring-2 focus:ring-purple-400"
      />
      <button
        @click="searchEmployees"
        class="ml-2 bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-md shadow"
      >
        Pretraži
      </button>
    </div>


    <!-- Tabela -->
    <div class="overflow-x-auto max-w-6xl mx-auto px-4">
      <table class="w-full bg-white shadow rounded-lg overflow-hidden border border-purple-200">
        <thead class="bg-purple-100 text-purple-900 text-sm font-semibold">
          <tr>
            <th class="px-5 py-3 text-left">#</th>
            <th class="px-5 py-3 text-left">Ime</th>
            <th class="px-5 py-3 text-left">Prezime</th>
            <th class="px-5 py-3 text-left">Email</th>
            <th class="px-5 py-3 text-center">Akcije</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="employee in pagination.data" :key="employee.id"
              class="border-t hover:bg-purple-50 transition">
            <td class="px-5 py-3">{{ employee?.id }}</td>
            <td class="px-5 py-3">{{ employee?.first_name ?? "Nepoznato ime" }}</td>
            <td class="px-5 py-3">{{ employee?.last_name ?? "Nepoznato prezime" }}</td>
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
      <PaginationNavigation
        :pagination="pagination"
        :changePage="changePage"
      />

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
  
  <!-- Rezerviši visinu footera -->
  <div class="h-24"><Footer /></div>
</template>
  
  
  <script setup>
  import PaginationNavigation from '@/components/PaginationNavigation.vue'
  import { Link, router } from '@inertiajs/vue3'
  import Navbar from '@/components/Navbar.vue'
  import Footer from '@/components/Footer.vue'
  import { usePage } from '@inertiajs/vue3'
  import { computed } from 'vue'
  import { ref } from 'vue'


  const props = defineProps({
    pagination: Object,
    flash: Object,
    search: String
  })
  console.log(props);
  console.log(props.pagination);
  const search = ref(props.search ?? '');
  const searchEmployees = () => {
    router.visit(route('employees.index', { search: search.value }), {
      preserveState: true,
      preserveScroll: true
    });
  };
  
  const changePage = (page) => {
  router.visit(route('employees.index', {
      page: page,
      search: props.search // Očuvaj trenutni search query prilikom paginacije
    }), {
    preserveState: true,
    preserveScroll: true,
  })
}
  
  //const employees = ref(props.pagination.data)
  
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
  