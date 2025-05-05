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

      <!-- Weekday header -->
      <div class="grid grid-cols-7 gap-2 mb-2 text-center font-semibold text-white">
        <div v-for="day in ['Pon', 'Uto', 'Sri', 'Čet', 'Pet', 'Sub', 'Ned']" :key="day">
          {{ day }}
        </div>
      </div>


      <!-- Calendar grid -->
      <div class="grid grid-cols-7 gap-2">
        <div v-for="(day, i) in daysInMonth" :key="i" @click="!day.isEmpty && openSidebar(day.date)"
          class="p-2 h-28 text-black rounded shadow transition cursor-pointer flex flex-col justify-start" :class="{
            'bg-white': !day.isToday,
            'hover:bg-purple-50': !day.isEmpty,
            'opacity-0 pointer-events-none': day.isEmpty,
            'bg-purple-400 border-2': day.isToday
          }">
          <div v-if="!day.isEmpty">
            <!-- Datum -->
            <div class="font-bold text-sm">{{ parseInt(day.date.split('-')[2]) }}</div>

            <!-- Expected: -->
            <div v-if="isWeekday(day.date)" class="text-xs text-gray-500">Expected: {{
              getExpectedTimeForDate(day.date).label }}</div>
            <div v-else class="text-xs text-gray-500">Expected: {{ getExpectedTimeForDate(day.date).label }}</div>

            <!-- Radni sati -->
            <div v-if="dailyWork[day.formatted]" class="text-xs mt-1">
              <span class="font-medium">Rad: {{ dailyWork[day.formatted] }}</span>
              <div class="text-xs font-semibold" :class="{
                'text-orange-500': getWorkedMinutes(dailyWork[day.formatted]) < getExpectedTimeForDate(day.date).minutes,
                'text-green-600': getWorkedMinutes(dailyWork[day.formatted]) >= getExpectedTimeForDate(day.date).minutes
              }">
                {{
                  (() => {
                    const workedMinutes = getWorkedMinutes(dailyWork[day.formatted]);
                    const expectedMinutes = getExpectedTimeForDate(day.date).minutes;
                    const diff = workedMinutes - expectedMinutes;

                    if (diff === 0) return 'Norma zadovoljena!';

                    const absDiff = Math.abs(diff);
                    const hours = Math.floor(absDiff / 60);
                    const minutes = absDiff % 60;

                    let output = diff > 0 ? '+' : '-';
                    if (hours > 0) output += `${hours}h`;    //1h 20min ili samo //20min ukoliko nema sati
                    if (minutes > 0) output += `${hours > 0 ? ' ' : ''}${minutes}min`;

                return `${output} ${diff > 0 ? 'iznad' : 'ispod'} norme`;
                })()
                }}
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>

    <!-- Sidebar -->
    <SidebarTimesheet v-if="showSidebar" :date="selectedDate" :projects="projects" :entries="entriesForDate"
      @close="showSidebar = false" @saved="handleSaved" />
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
import axios from 'axios';

const dailyWork = ref({});

const getExpectedTimeForDate = (dateStr) => {
  //Vikend
  if (dayjs(dateStr).day() === 0 || dayjs(dateStr).day() === 6) {
    return {
      label: '0h',
      minutes: 0
    }
  }
  return {
    label: '8h',
    minutes: 8 * 60
  }
}

function getWorkedMinutes(workedString) {
  const match = workedString.match(/(\d+)h(\d+)min/);
  if (!match) return 0;
  const hours = parseInt(match[1], 10); //10 decimal
  const minutes = parseInt(match[2], 10);
  return hours * 60 + minutes;
}

onMounted(async () => {
  const response = await axios.get('/timesheets/daily-summary');
  dailyWork.value = response.data;
});

const props = defineProps({
  projects: Array,
})

const selectedMonth = ref(dayjs().format('YYYY-MM'))
const daysInMonth = ref([])
const showSidebar = ref(false)
const selectedDate = ref(null)
const entriesForDate = ref([])
const projects = ref(props.projects)

const months = Array.from({ length: 12 }, (_, i) => {
  const date = dayjs().month(i)
  return {
    label: date.format('MMMM'),
    value: date.format('YYYY-MM')
  }
})

const isWeekday = (dateStr) => {
  const day = dayjs(dateStr).day()
  return day >= 1 && day <= 5 // Monday to Friday
}

const loadDays = () => {
  const start = dayjs(selectedMonth.value).startOf('month')
  const end = dayjs(selectedMonth.value).endOf('month')
  const days = []
  const startWeekday = (start.day() + 6) % 7 // Nedjelja = 0, ponedjeljak = 1, ..., subota = 6

  for (let i = 0; i < startWeekday; i++) {
    days.push({ isEmpty: true })
  }

  for (let day = 1; day <= end.date(); day++) {
    const date = dayjs(start).date(day)
    days.push({
      label: date.format('dddd'),
      formatted: date.format('YYYY-MM-DD'),
      date: date.format('YYYY-MM-DD'),
      isEmpty: false,
      isToday: date.isSame(dayjs(), 'day')
    })
  }
  daysInMonth.value = days
}

const loadDailySummary = async () => {
  const response = await axios.get('/timesheets/daily-summary', {
    params: { month: selectedMonth.value },
  });
  dailyWork.value = response.data;
};

const loadTimesheets = async () => {
  await loadDays()
  await loadDailySummary()
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