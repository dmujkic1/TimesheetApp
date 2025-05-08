<script setup lang="ts">
import Navbar from '@/components/Navbar.vue'; // putanja prema tvojoj navbar komponenti
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItemType } from '@/types';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="relative min-h-screen bg-gray-100"> <!-- Ovaj div služi da se ceo ekran prekriva -->
      <!-- Pozadina koja se prikazuje ispod sadržaja -->
      <div
        class="absolute inset-0 z-0 bg-cover bg-center bg-no-repeat"
        style="background-image: url('/pozadina.jpg');"
      ></div>

      <!-- Navbar bez pozadine -->
      <div class="relative z-10">
        <Navbar />
      </div>

      <!-- Glavni sadržaj -->
      <div class="relative z-10">
        <slot />
      </div>
    </div>
  </AppLayout>
</template>
