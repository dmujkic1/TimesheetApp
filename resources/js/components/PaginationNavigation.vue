<template>
    <div class="flex justify-center items-center mt-6 mb-20 space-x-4">
      <!-- PRETHODNA -->
      <div class="w-[130px] flex justify-center">
        <button
          v-if="pagination.prev_page_url"
          @click="changePage(pagination.current_page - 1)"
          class="w-full text-center px-3 py-1 bg-purple-200 hover:bg-purple-300 text-purple-800 rounded"
        >
          ◀ Prethodna
        </button>
        <div v-else class="px-3 py-1 invisible">◀ Prethodna</div>
      </div>
  
      <!-- Brojevi stranica -->
      <div class="flex space-x-2 justify-center">
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
      </div>
  
      <!-- SLJEDEĆA -->
      <div class="w-[130px] flex justify-center">
        <button
          v-if="pagination.next_page_url"
          @click="changePage(pagination.current_page + 1)"
          class="w-full text-center px-3 py-1 bg-purple-200 hover:bg-purple-300 text-purple-800 rounded"
        >
          Sljedeća ▶
        </button>
        <div v-else class="px-3 py-1 invisible">Sljedeća ▶</div>
      </div>
    </div>
  </template>
  
  <script setup>
  defineProps({
    pagination: {
      type: Object,
      required: true
    },
    changePage: {
      type: Function,
      required: true
    }
  })
  </script>
  