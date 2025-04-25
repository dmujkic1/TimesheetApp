<template>
  <div
    class="min-h-screen flex flex-col bg-gray-50 text-gray-800"
    style="background-image: url('/pozadina.jpg');"
  >
    <Navbar />

    <div v-if="project" class="max-w-3xl mx-auto p-8 bg-white shadow rounded-xl border border-purple-200 mt-10">
      <h1 class="text-3xl font-semibold text-purple-700 mb-8">🔍 Detalji Projekta</h1>

      <div class="mb-6 border-b border-gray-200 pb-4">
        <strong class="text-purple-700 block font-medium mb-1">Naziv:</strong>
        <span class="text-gray-800">{{ project.project_name }}</span>
      </div>

      <div class="mb-6 border-b border-gray-200 pb-4">
        <strong class="text-purple-700 block font-medium mb-1">Opis:</strong>
        <p class="text-gray-800">{{ project.description }}</p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6 border-b border-gray-200 pb-4">
        <div>
          <strong class="text-purple-700 block font-medium mb-1">Datum početka:</strong>
          <span class="text-gray-800">{{ project.start_date }}</span>
        </div>
        <div>
          <strong class="text-purple-700 block font-medium mb-1">Datum završetka:</strong>
          <span class="text-gray-800">{{ project.end_date }}</span>
        </div>
      </div>

      <div class="mb-6 border-b border-gray-200 pb-4">
        <strong class="text-purple-700 block font-medium mb-1">Status:</strong>
        <span
          :class="[
            'inline-block px-3 py-1 rounded-full font-semibold text-sm',
            {
              'bg-green-500 text-white': project.status === 'Active',
              'bg-yellow-500 text-white': project.status === 'Archived',
              'bg-blue-500 text-white': project.status === 'Completed',
            },
          ]"
        >{{ project.status }}</span>
      </div>

      <div class="mb-6 border-b border-gray-200 pb-4" v-if="project.client">
        <strong class="text-purple-700 block font-medium mb-1">Klijent:</strong>
        <span class="text-gray-800">{{project.client?.name }}</span>
      </div>
      <div class="mb-6 border-b border-gray-200 pb-4" v-else>
        <strong class="text-purple-700 block font-medium mb-1">Klijent:</strong>
        <span class="text-gray-800 italic">Nije dodijeljen</span>
      </div>

      <div class="mb-6" v-if="project.team && project.team.length > 0">
        <strong class="text-purple-700 block font-medium mb-2">Tim:</strong>
        <ul class="list-disc pl-5">
          <li v-for="teamMember in project.team" :key="teamMember.id" class="text-gray-800">{{ teamMember.name }}</li>
        </ul>
      </div>
      <div class="mb-6" v-else>
        <strong class="text-purple-700 block font-medium mb-1">Tim:</strong>
        <span class="text-gray-800 italic">Nije dodijeljen</span>
      </div>

      <div class="mt-8 flex justify-end">
        <Link :href="route('projects.index')" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-md shadow">
          Natrag na listu
        </Link>
      </div>
    </div>
    <div v-else class="max-w-3xl mx-auto p-8 mt-10 text-center">
      <p class="text-gray-600 italic">Projekt nije pronađen.</p>
      <Link :href="route('projects.index')" class="text-purple-600 hover:underline mt-2 inline-block">
        Natrag na listu
      </Link>
    </div>
  </div>
  <div class="h-24"><Footer /></div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import Navbar from '@/components/Navbar.vue';
import Footer from '@/components/Footer.vue';

const props = defineProps({
  project: Object,
});
</script>