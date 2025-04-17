<template>
  <!-- Dodajemo div s pozadinskom slikom -->
  <div
    class="bg-cover bg-center h-screen"
    style="background-image: url('app/pics/what-the-hex-dark.jpg');" 
  >
    <!-- Tvoj postojeći sadržaj -->
    <Navbar />
    <h1 class="text-xl font-bold text-white-500">Lista managera</h1>  <!-- Koristi Tailwind klasu za boje -->
    <ul class="space-y-4">
      <li v-for="(manager, index) in managers" :key="index" class="bg-gradient-to-r from-blue-900 to-blue-300 text-white px-4 py-2 rounded">
        <div class="text-lg font-semibold text-white-800">{{ manager?.user.name ?? "Nema naziva" }}</div>
        <button @click="openModal(manager.user)"
          class="mt-2 bg-gradient-to-r from-sky-300 to-blue-400 text-white px-4 py-2 rounded shadow hover:from-sky-400 hover:to-blue-500 transition"
        >
          Show employees
        </button>
      </li>
    </ul>

    <!-- Modal za prikaz employees vezanih za odabranog managera -->
    <div>
      <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="p-6 rounded shadow-md text-center">
          <h2 class="text-lg font-bold mb-4">Uposlenici - {{ selectedmanager }}</h2>
          <div class="mt-4 flex justify-center gap-4">
            <button @click="showModal = false" class="bg-red-500 px-4 py-2 rounded">Otkaži</button>
            <!-- <ul>
              <li v-for="(employee, index) in employees.filter()" :key="index">
                {{ employee?.first_name ?? "Nema naziva!" }} - {{ employee?.email ?? "Nema maila" }}
              </li>
            </ul>  MORAS NAPRAVITI PROJECTS, TEAMS TE ONDA RELACIJAMA POVEZATI TO TE NAKON TOGA IZVUCI EMPLOYEES KOJI SU VEZANI ZA TAJ TIM KOJIM UPRAVLJA JEDAN MANAGER-->
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
  import Navbar from '@/components/Navbar.vue'
  import { usePage } from '@inertiajs/vue3'
  import { User } from 'lucide-vue-next'
  import { ref } from 'vue'
  
  const props = defineProps({
    managers: {
      type: Array,
      required: true,
    },
    employees: {
      type: Array,
      required: true,
    },
  })

  const showModal = ref(false)
  const selectedmanager = ref(null)

  const openModal = (manager) => {
    selectedmanager.value = manager.name
    showModal.value = true
    console.log(manager)
  }
</script>

<style lang="scss" scoped>
</style>
