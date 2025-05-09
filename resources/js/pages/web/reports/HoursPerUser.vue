<template>
    <div class="bg-cover bg-center min-h-screen" style="background-image: url('/pozadina.jpg');">
        <Navbar />
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Izvještaj: Ukupno Odobrenih Sati po Korisniku
            </h2>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Filteri -->
                <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-4 mb-6 flex flex-col sm:flex-row items-center space-y-3 sm:space-y-0 sm:space-x-4">
                    <!-- Filter za Mjesec -->
                    <div>
                        <label for="month_filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Mjesec:</label>
                        <input type="month" id="month_filter" v-model="filterForm.month" @change="applyFilters"
                               class="mt-1 block w-full sm:w-auto rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 text-sm" />
                    </div>

                    <!-- Filter za Projekat -->
                    <div>
                        <label for="project_filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Projekti:</label>
                        <select id="project_filter" v-model="filterForm.project_id" @change="applyFilters"
                                class="mt-1 block w-full sm:w-auto rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 text-sm">
                            <option :value="null">Svi projekti</option>
                            <option v-for="project in projects" :key="project.id" :value="project.id">
                                {{ project.project_name }}
                            </option>
                        </select>
                    </div>
                     <!-- Gumb za Reset Filter -->
                     <div class="pt-5">
                         <button @click="resetFilters" class="px-4 py-2 text-sm border border-gray-300 rounded-md hover:bg-gray-100 dark:border-gray-600 dark:hover:bg-gray-700 dark:text-gray-300">
                             Resetuj Filtere
                         </button>
                     </div>

                </div>

                <!-- Tablica s rezultatima -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <div v-if="reportData && reportData.length > 0" class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Korisnik</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ filterForm.project_id ? 'Projekat' : 'Projekti' }}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Ukupno Sati</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    <tr v-for="item in reportData" :key="item.user_id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ item.user_name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ filterForm.project_id ? getProjectNameById(filterForm.project_id) : item.user_project }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ item.total_hours_formatted }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                         <div v-else>
                            <p class="text-center text-gray-500 dark:text-gray-400 py-8">Nema podataka za prikaz za odabrane filtere.</p>
                        </div>
                    </div>
                </div>

                 <!-- Dugme za Export (dodat ćemo kasnije) -->
                 <!-- <div class="mt-6 text-right">
                     <button @click="exportReport" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Export u CSV</button>
                 </div> -->
            </div>
        </div>
    </div>
        <div class="h-24"><Footer /></div>
</template>

<script setup>
import { ref, reactive, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import Navbar from '@/components/Navbar.vue';
import Footer from '@/components/Footer.vue';
import dayjs from 'dayjs';
import pickBy from 'lodash/pickBy'; //uklanjanje null filtera
import throttle from 'lodash/throttle'; //prečesti zahtjeva

const props = defineProps({
    reportData: Array,
    projects: Array,
    filters: Object,
});

const filterForm = reactive({
    month: props.filters.month,
    project_id: props.filters.project_id,
});

const applyFilters = throttle(() => {
    router.get(route('reporting.hoursPerUser'), pickBy(filterForm), {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
}, 50);

const resetFilters = () => {
    filterForm.month = dayjs().subtract(1, 'month').format('YYYY-MM');
    filterForm.project_id = null;
    applyFilters();
};

const getProjectNameById = (id) => {
    const project = props.projects.find(p => p.id === id);
    return project ? project.project_name : 'Nepoznat projekat';
};

</script>