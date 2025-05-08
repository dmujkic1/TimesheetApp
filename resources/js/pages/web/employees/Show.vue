<template>
  <div
    class="min-h-screen flex flex-col bg-gradient-to-r from-purple-600 to-indigo-800 text-gray-50"
    style="background-image: url('/pozadina.jpg'); background-size: cover; background-position: center;"
  >
    <Navbar />

    <main class="flex-grow px-6 py-12 max-w-4xl mx-auto">
      <h1 class="text-5xl font-extrabold text-center text-purple-400 mb-10">
        👤 Podaci uposlenika
      </h1>

      <div class="bg-black/60 backdrop-blur-md shadow-xl rounded-3xl p-8 border border-purple-500 space-y-6">
        <p><span class="font-semibold text-purple-300 text-2xl">Ime:</span> {{ employee?.first_name ?? "Nema naziva" }}</p>
        <p><span class="font-semibold text-purple-300 text-2xl">Prezime:</span> {{ employee?.last_name ?? "Nema prezimena" }}</p>
        <p><span class="font-semibold text-purple-300 text-2xl">Email:</span> {{ employee?.email ?? "Nema maila" }}</p>
        <p><span class="font-semibold text-purple-300 text-2xl">Titula:</span> {{ employee?.job_title ?? "Nema titule" }}</p>
        <p><span class="font-semibold text-purple-300 text-2xl">Datum zaposlenja:</span> {{ employee?.hire_date ?? "Nema datuma zaposlenja" }}</p>
        <p><span class="font-semibold text-purple-300 text-2xl">Aktivan:</span> {{ employee?.status ?? "Nema radnog statusa" }}</p>
      </div>
    </main>
    
    <!-- Rezerviši visinu footera -->
    <div class="h-24">
      <Footer />
    </div>
  </div>
</template>

<script setup>
import Navbar from '@/components/Navbar.vue';
import Footer from '@/components/Footer.vue';
import { usePage } from '@inertiajs/vue3'

const { props } = usePage()
const employee = props.employee
</script>
