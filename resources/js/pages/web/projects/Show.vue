<template>
  <div
    class="min-h-screen flex flex-col bg-gradient-to-r from-purple-600 to-indigo-800 text-gray-50"
    style="background-image: url('/pozadina.jpg'); background-size: cover; background-position: center;"
  >
    <Navbar />
    <head>
      <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@200..700&display=swap" rel="stylesheet">
    </head>

    <!-- Success Message -->
    <div v-if="successMessage" class="bg-green-600 text-white text-center py-4 mb-6 rounded-2xl shadow-2xl animate__animated animate__fadeInUp">
      {{ successMessage }}
    </div>

    <!-- Project Details -->
    <div class="max-w-7xl mx-auto px-6 py-12 bg-black/60 backdrop-blur-md shadow-xl rounded-3xl border border-purple-500 mt-12 text-white">
  <h1 class="text-5xl font-extrabold text-center text-purple-400 mb-10">
    📋 Detalji Projekta
  </h1>

  <div class="space-y-8">
    <!-- Naziv Projekta -->
    <div class="flex justify-between items-center">
      <label class="text-purple-300 text-2xl font-semibold">Naziv projekta:</label>
      <p class="text-white text-lg font-medium">{{ project.project_name }}</p>
    </div>

    <!-- Opis -->
    <div class="flex justify-between items-center">
      <label class="text-purple-300 text-2xl font-semibold">Opis:</label>
      <p class="text-white text-lg font-medium">{{ project.description }}</p>
    </div>

    <!-- Datum početka -->
    <div class="flex justify-between items-center">
      <label class="text-purple-300 text-2xl font-semibold">Datum početka:</label>
      <p class="text-white text-lg font-medium">{{ project.start_date }}</p>
    </div>

    <!-- Datum završetka -->
    <div class="flex justify-between items-center">
      <label class="text-purple-300 text-2xl font-semibold">Datum završetka:</label>
      <p class="text-white text-lg font-medium">{{ project.end_date }}</p>
    </div>

    <!-- Status -->
    <div class="flex justify-between items-center">
      <label class="text-purple-300 text-2xl font-semibold">Status:</label>
      <p class="text-white text-lg font-medium">{{ project.status }}</p>
    </div>

    <!-- Klijent -->
    <div>
      <label class="text-purple-300 text-2xl font-semibold">Klijent:</label>
      <ul class="mt-2 space-y-1 pl-4 list-disc">
        <li v-for="client in clients" :key="client.id" class="text-white text-lg font-medium">
          {{ client.name }}
        </li>
      </ul>
    </div>

    <!-- Tim -->
    <div>
      <label class="text-purple-300 text-2xl font-semibold">Tim:</label>
      <ul class="mt-2 space-y-1 pl-4 list-disc">
        <li v-for="team in teams" :key="team.id" class="text-white text-lg font-medium">
          {{ team.team_name }}
        </li>
      </ul>
    </div>
  </div>

  <!-- Back Button -->
  <div class="flex justify-end mt-12">
    <Link :href="route('projects.index')" 
          class="bg-purple-600 hover:bg-purple-700 text-white px-8 py-3 rounded-xl shadow-xl hover:shadow-2xl transition-transform transform hover:scale-105 font-semibold">
      ← Povratak na listu
    </Link>
  </div>
</div>


    <div class="h-24 relative z-0"><Footer /></div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import Navbar from '@/components/Navbar.vue';
import Footer from '@/components/Footer.vue';

const props = defineProps({
  project: Object,
  teams: Array,
  clients: Array
});
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

body {
  font-family: 'Inter', sans-serif;
  background-color: #f9fafb;
  min-height: 100vh;
  margin: 0;
}

/* Animacije pri ulasku */
.animate__fadeInUp {
  animation: fadeInUp 0.7s ease-in-out forwards;
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Stil za dugme */
a {
  transition: all 0.3s ease;
}

/* Hover efekti za dugme */
a:hover {
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
  transform: scale(1.05);
}

/* Pozadinske boje */
.bg-green-600 {
  background-color: #48bb78;
}

.bg-purple-600 {
  background-color: #7c3aed;
}

.bg-purple-500 {
  background-color: #9f7aea;
}

/* Stil za karticu */
.max-w-4xl {
  max-width: 60rem;
}

.border-gradient-to-r {
  border-image: linear-gradient(90deg, #7c3aed, #4c51bf) 1;
}

.shadow-xl {
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
}

.rounded-3xl {
  border-radius: 1.5rem;
}

.rounded-xl {
  border-radius: 1.25rem;
}

/* Boje fontova */
.text-purple-800 {
  color: #6b46c1;
}

.text-purple-700 {
  color: #6b46c1;
}

.text-gray-800 {
  color: #2d3748;
}

.text-gray-600 {
  color: #4a5568;
}

/* Efekat pri hover na dugmetu */
a {
  background-color: #7c3aed;
  color: white;
  padding: 14px 28px;
  border-radius: 1.25rem;
  text-transform: uppercase;
  letter-spacing: 1px;
  font-weight: bold;
  transition: all 0.3s ease;
}

/* Aktivni status */
a:hover {
  background-color: #6b46c1;
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
  transform: scale(1.05);
}
</style>
