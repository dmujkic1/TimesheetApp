<template>
  <div class="min-h-screen bg-gray-100" style="background-image: url('/pozadina.jpg');">
    <Navbar />
    <div class="max-w-6xl mx-auto py-10 px-4">
      <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold text-white">Timesheet Management</h1>
        <select v-model="selectedMonth" @change="loadTimesheets" class="bg-white border rounded px-3 py-1 text-black">
          <option v-for="month in months" :value="month.value" :key="month.value">
            {{ month.label }}
          </option>
        </select>
      </div>

      <!-- Calendar grid -->
      <div class="grid grid-cols-7 gap-2">
        <div v-for="(day, i) in daysInMonth" :key="i" @click="!day.isEmpty && openSidebar(day.date)"
          class="p-3 h-24 bg-white text-black rounded shadow transition cursor-pointer"
          :class="{ 'hover:bg-purple-50': !day.isEmpty, 'opacity-0 pointer-events-none': day.isEmpty }">
          <div v-if="!day.isEmpty">
            <div class="font-semibold">{{ day.label }}</div>
            <div class="text-sm text-gray-500">{{ day.formatted }}</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Sidebar -->
    <SidebarTimesheet v-if="showSidebar"
    :date="selectedDate"
    :projects="projects"
    :entries="entriesForDate"
    @close="showSidebar = false"
    @saved="handleSaved" />
  </div>
  <div class="h-24">
    <Footer />
  </div>
</template>

<script setup>
import Navbar from '@/components/Navbar.vue'
import { router } from '@inertiajs/vue3'
import Footer from '@/components/Footer.vue'
import SidebarTimesheet from '@/components/SidebarTimesheet.vue'
import { ref, onMounted } from 'vue'
import dayjs from 'dayjs'

const props = defineProps({
  projects: Array,
})

const selectedMonth = ref(dayjs().format('YYYY-MM'))
const daysInMonth = ref([])
const showSidebar = ref(false)
const selectedDate = ref(null)
const entriesForDate = ref([])
const projects = ref(props.projects)
console.log(props.projects) //NEKADA UCITA A NEKADA NE UCITA?????
console.log(projects.value)

const months = Array.from({ length: 12 }, (_, i) => {
  const date = dayjs().month(i)
  return {
    label: date.format('MMMM'),
    value: date.format('YYYY-MM')
  }
})

const loadDays = () => {
  const start = dayjs(selectedMonth.value).startOf('month')
  const end = dayjs(selectedMonth.value).endOf('month')
  const days = []
  const startWeekday = (start.day() + 6) % 7 // Nedjelja = 0, ponedjeljak = 1, ..., subota = 6

  for (let i = 0; i < startWeekday; i++) {
    days.push({ isEmpty: true })
  }

  for (let day = 1; day <= end.date(); day++) {
    const date = start.date(day)
    days.push({
      label: date.format('dddd'),
      formatted: date.format('YYYY-MM-DD'),
      date: date.format('YYYY-MM-DD'),
      isEmpty: false
    })
  }
  daysInMonth.value = days
}

const loadTimesheets = async () => {
  await loadDays()
  router.get(route('timesheets.index', { month: selectedMonth.value }), {}, { preserveState: true })
}


const openSidebar = (date) => {
  selectedDate.value = date;
  router.get(route('timesheets.entries'), { date }, {
    preserveState: true,
    preserveScroll: true,
    onSuccess: (page) => {
      entriesForDate.value = page.props.entries;
      showSidebar.value = true;
    }
  });
}

const handleSaved = () => {
  loadTimesheets()
  showSidebar.value = false
}

onMounted(loadTimesheets)
onMounted(() => {
  projects.value = props.projects
  loadDays()
})
</script>