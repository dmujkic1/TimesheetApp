<template>
    <div class="min-h-screen bg-gray-100" style="background-image: url('/pozadina.jpg');">
    <Navbar />
        <div class="text-center py-8">
         <h2 class="text-3xl font-bold tracking-tight text-gray-800 dark:text-gray-200 flex items-center justify-center space-x-3 mx-auto max-w-xl">
           <span class="text-green-600 dark:text-green-400 text-4xl">⏰</span>
           <span>Timesheets</span>
         </h2>
         <p class="mt-2 text-gray-600 dark:text-gray-400 text-sm max-w-md mx-auto">
           Pregled radnih sati s mogućnošću unosa i uređivanja.
         </p>
        </div>

  
      <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div v-if="flash?.success" class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ flash.success }}</span>
          </div>
          <div v-if="flash?.error" class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ flash.error }}</span>
          </div>
          <div v-if="flash?.info" class="mb-4 bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ flash.info }}</span>
          </div>
  
          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6">
              <div v-if="pendingEntries.data && pendingEntries.data.length > 0" class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                  <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                      <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Zaposlenik</th>
                      <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Projekat</th>
                      <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Datum</th>
                      <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Sati</th>
                      <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Napomene</th>
                      <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Akcije</th>
                    </tr>
                  </thead>
                  <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <tr v-for="entry in pendingEntries.data" :key="entry.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ entry.user.name }} <span class="text-xs text-gray-500">({{ entry.user.email }})</span></td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ entry.project.project_name }}</td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ formatDate(entry.date) }}</td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ calculateHours(entry.start_time, entry.end_time, entry.break_start, entry.break_end) }}</td>
                      <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300 max-w-xs truncate" :title="entry.notes">{{ entry.notes || '-' }}</td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                        <button @click="approveEntry(entry.id)" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 font-semibold py-1 px-2 rounded hover:bg-indigo-100 dark:hover:bg-gray-700 transition duration-150 ease-in-out">Odobri</button>
                        <button @click="openRejectModal(entry)" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 font-semibold py-1 px-2 rounded hover:bg-red-100 dark:hover:bg-gray-700 transition duration-150 ease-in-out">Odbij</button>
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
              <div v-else>
                <p class="text-gray-500 dark:text-gray-400 text-center py-8">Trenutno nema unosa koji čekaju na odobrenje.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
  
      <div v-if="showRejectModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
          <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
              <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity dark:bg-gray-900 dark:bg-opacity-75" aria-hidden="true"></div>
              <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
              <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                  <div class="p-6">
                      <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100" id="modal-title">
                          Odbijanje Unosa Rada
                      </h2>
                      <div v-if="entryToReject" class="mt-2">
                          <p class="text-sm text-gray-600 dark:text-gray-400">Odbijate unos za: <strong>{{ entryToReject.user.name }}</strong></p>
                          <p class="text-sm text-gray-600 dark:text-gray-400">Projekt: <strong>{{ entryToReject.project.project_name }}</strong></p>
                          <p class="text-sm text-gray-600 dark:text-gray-400">Datum: <strong>{{ formatDate(entryToReject.date) }}</strong></p>
                          <p class="text-sm text-gray-600 dark:text-gray-400">Napomena korisnika: <em class="truncate block">{{ entryToReject.notes || 'Nema napomene' }}</em></p>
                      </div>
                      <div class="mt-4">
                          <label for="rejection_reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Razlog odbijanja (obavezno)</label>
                          <textarea id="rejection_reason" v-model="rejectionForm.reason" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm text-black dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:focus:border-indigo-500 dark:focus:ring-indigo-500" placeholder="Unesite razlog..."></textarea>
                          <p v-if="rejectionForm.errors.rejection_reason" class="mt-2 text-sm text-red-600">{{ rejectionForm.errors.rejection_reason }}</p>
                      </div>
                  </div>
                  <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse space-y-2 sm:space-y-0 sm:space-x-3 sm:space-x-reverse">
                      <button type="button" @click="submitRejection" :disabled="rejectionForm.processing || !rejectionForm.reason"
                              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50">
                          Potvrdi Odbijanje
                      </button>
                      <button type="button" @click="closeRejectModal"
                              class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm dark:bg-gray-600 dark:text-gray-200 dark:border-gray-500 dark:hover:bg-gray-500">
                          Odustani
                      </button>
                  </div>
              </div>
          </div>
      </div>
    </div>
    <div class="h-24">
    <Footer />
  </div>
  </template>
  
  <script setup>
  import PaginationNavigation from '@/components/PaginationNavigation.vue';
  import Navbar from '@/components/Navbar.vue';
  import Footer from '@/components/Footer.vue';
  import { useForm, usePage, router } from '@inertiajs/vue3';
  import { ref, computed } from 'vue';
  import dayjs from 'dayjs';
  
  const props = defineProps({
    pagination: Object,
    pendingEntries: Object,
  });

  const changePage = (page) => {
  router.visit(route('manager.timesheets.pending', {
      page: page,
    }), {
    preserveState: true,
    preserveScroll: true,
  })
}
  
  const page = usePage();
  const flash = computed(() => page.props.flash || {});
  
  const showRejectModal = ref(false);
  const entryToReject = ref(null);
  
  const rejectionForm = useForm({
    reason: '',
  });
  
  const formatDate = (dateString) => dayjs(dateString).format('DD.MM.YYYY');
  
  const calculateHours = (start, end, breakStart, breakEnd) => {
    if (!start || !end) return 'N/A';
    const startTime = dayjs(start);
    const endTime = dayjs(end);
    let diffMinutes = endTime.diff(startTime, 'minutes');
  
    if (breakStart && breakEnd) {
      const breakStartTime = dayjs(breakStart);
      const breakEndTime = dayjs(breakEnd);
      const breakDiff = breakEndTime.diff(breakStartTime, 'minutes');
      if (breakDiff > 0) diffMinutes -= breakDiff;
    }
    if (diffMinutes < 0) diffMinutes = 0;
    const hours = Math.floor(diffMinutes / 60);
    const minutes = diffMinutes % 60;
    return `${hours}h ${minutes}min`;
  };
  
  const approveEntry = (entryId) => {
    if (confirm('Jeste li sigurni da želite odobriti ovaj unos?')) {
      router.patch(route('manager.timesheets.approveEntry', { timesheet: entryId }), {}, {
        preserveScroll: true,
        onSuccess: () => {},
        onError: (errors) => {
          console.error("Greška pri odobravanju:", errors);
          alert('Došlo je do greške prilikom odobravanja unosa.');
        }
      });
    }
  };
  
  const openRejectModal = (entry) => {
    entryToReject.value = entry;
    rejectionForm.reset();
    showRejectModal.value = true;
  };
  
  const closeRejectModal = () => {
    showRejectModal.value = false;
    entryToReject.value = null;
  };
  
  const submitRejection = () => {
    if (!entryToReject.value || !rejectionForm.reason.trim()) {
        alert('Razlog odbijanja je obavezan.');
        return;
    }
    rejectionForm.transform(data => ({
      rejection_reason: data.reason,
    })).patch(route('manager.timesheets.rejectEntry', { timesheet: entryToReject.value.id }), {
      preserveScroll: true,
      onSuccess: () => closeRejectModal(),
      onError: (errors) => {
        // Greške su na rejectionForm.errors
        console.error("Greška pri odbijanju:", errors);
      }
    });
  };
  </script>