<template>
  <div class="min-h-screen flex flex-col bg-gray-50 text-gray-800"
  style="background-image: url('/pozadina.jpg');">
    <Navbar />

    <main class="flex-grow px-4 py-8 max-w-4xl mx-auto">
      <h1 class="text-3xl font-bold text-white mb-6">👤 Podaci uposlenika</h1>

      <div class="bg-white shadow-md rounded-lg p-6 border border-purple-200 space-y-4">
        <p><span class="font-semibold text-purple-700">Ime:</span> {{ employee?.first_name ?? "Nema naziva" }}</p>
        <p><span class="font-semibold text-purple-700">Prezime:</span> {{ employee?.last_name ?? "Nema prezimena" }}</p>
        <p><span class="font-semibold text-purple-700">Email:</span> {{ employee?.email ?? "Nema maila" }}</p>
        <p><span class="font-semibold text-purple-700">Titula:</span> {{ employee?.job_title ?? "Nema titule" }}</p>
        <p><span class="font-semibold text-purple-700">Datum zaposlenja:</span> {{ employee?.hire_date ?? "Nema datuma zaposlenja" }}</p>
        <p><span class="font-semibold text-purple-700">Aktivan:</span> {{ employee?.status ?? "Nema radnog statusa" }}</p>
      </div>
    </main>
  </div>
  <!-- Rezerviši visinu footera -->
  <div class="h-24"><Footer /></div>
</template>

<script setup>
import Navbar from '@/components/Navbar.vue';
import Footer from '@/components/Footer.vue';
import { usePage } from '@inertiajs/vue3'

const { props } = usePage()
const employee = props.employee
</script>
