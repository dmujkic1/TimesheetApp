<template>
  <div
    class="bg-cover bg-center min-h-screen"
    style="background-image: url('/pozadina.jpg');"
  >
    <Navbar />

    <h1 class="text-3xl font-extrabold text-white text-center pt-10 drop-shadow-lg">
      Lista menađera
    </h1>

    <ul class="space-y-6 px-10 pt-10 max-w-3xl mx-auto">
      <li
        v-for="(manager, index) in managers"
        :key="index"
        class="bg-gradient-to-br from-purple-900 via-purple-900 to-violet-800 text-white px-6 py-4 rounded-2xl shadow-[0_4px_20px_rgba(0,0,0,0.8)] transition hover:scale-105 duration-300"
      >
        <div class="text-xl font-semibold">{{ manager?.user.name ?? 'Nema naziva' }}</div>
        <button
          @click="openModal(manager.user)"
          class="mt-4 bg-gradient-to-r from-violet-500 via-purple-500 to-fuchsia-600 text-white px-5 py-2 rounded-lg shadow-[0_8px_30px_rgba(139,92,246,0.7)] hover:shadow-[0_12px_40px_rgba(168,85,247,0.9)] hover:from-violet-600 hover:via-purple-600 hover:to-fuchsia-700 transition duration-300"
        >


          Show employees
        </button>
      </li>
    </ul>

    <!-- Modal -->
    <div
      v-if="showModal"
      class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50"
    >
      <div
        class="bg-gradient-to-br from-white via-purple-50 to-fuchsia-100 p-8 rounded-2xl shadow-2xl text-center w-[90%] max-w-lg"
      >
        <h2 class="text-xl font-bold mb-4 text-purple-900">
          Uposlenici - {{ selectedmanager }}
        </h2>

        <!-- Lista zaposlenika (kasnije) -->
        <!--
        <ul>
          <li v-for="(employee, index) in employees" :key="index">
            {{ employee?.first_name ?? 'Nema imena' }} - {{ employee?.email ?? 'Nema maila' }}
          </li>
        </ul>
        -->

        <div class="mt-6 flex justify-center gap-4">
          <button
            @click="showModal = false"
            class="bg-gradient-to-r from-rose-500 to-pink-700 text-white px-5 py-2 rounded-lg shadow hover:from-rose-600 hover:to-pink-800 transition"
          >
            Otkaži
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import Navbar from '@/components/Navbar.vue'
import { ref } from 'vue'

const props = defineProps({
  managers: {
    type: Array,
    required: true
  },
  employees: {
    type: Array,
    required: true
  }
})

const showModal = ref(false)
const selectedmanager = ref(null)

const openModal = (manager) => {
  selectedmanager.value = manager.name
  showModal.value = true
  console.log('Otvoren modal za:', manager)
}
</script>

<style lang="scss" scoped>
</style>
