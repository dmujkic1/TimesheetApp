<template>
  <div class="min-h-screen bg-gray-100" style="background-image: url('/pozadina.jpg');">
    <Navbar />

    <!-- Flash poruke -->
    <div v-if="props?.flash?.error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4 mx-auto max-w-4xl">
      {{ props.flash.error }}
    </div>
    <div v-if="props?.flash?.success" class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-4 mx-auto max-w-4xl">
      {{ props.flash.success }}
    </div>

    <div class="max-w-6xl mx-auto py-10 px-4">
      <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold text-white">Timesheet Management</h1>
        <button
          v-if="canSubmitMonth"
          @click="submitSelectedMonth"
          type="button"
          class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm shadow-md transition duration-150 ease-in-out">
          Predaj {{ dayjs(selectedMonth).format('MMMM') }}
        </button>
        <button
          @click="showModal = true"
          type="button"
          class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded text-sm shadow-md transition duration-150 ease-in-out ml-2">
          Zatraži OOO vrijeme
        </button>
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
            'bg-white': !day.isToday && !isOOODay(day.date),
            'bg-blue-100 text-gray-600': isOOODay(day.date),
            'hover:bg-purple-50': !day.isEmpty && !isOOODay(day.date),
            'opacity-0 pointer-events-none': day.isEmpty,
            'bg-purple-400 border-2': day.isToday
          }">
          <div v-if="!day.isEmpty">
            <!-- Datum -->
            <div class="font-bold text-sm">{{ parseInt(day.date.split('-')[2]) }}</div>

            <!-- Expected: -->
            <div v-if="isOOODay(day.date)" class="text-xs text-red-500">Expected: {{getExpectedTimeForDate(day.date).label }}</div>
            <div v-else-if="isWeekday(day.date)" class="text-xs text-gray-500">Expected: {{getExpectedTimeForDate(day.date).label }}</div>
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
    <SidebarTimesheet
    v-if="showSidebar" 
    :date="selectedDate" 
    :projects="projects" 
    :entries="entriesForDate"
    @close="showSidebar = false" 
    @saved="handleSaved"
    @deleted="handleSaved" />
  </div>
  <Modal v-if="showModal" @close="showModal = false">
  <template #header>
    <h2 class="text-lg font-semibold">Zatraži OOO vrijeme</h2>
  </template>

  <template #body>
    <form @submit.prevent="submitOOORequest" class="space-y-4">
      <div>
        <label class="block text-sm font-medium text-gray-700">Tip vremena</label>
        <select v-model="oooForm.type" class="mt-1 block w-full rounded border-gray-300 text-black">
          <option disabled value="">-- Odaberite --</option>
          <option value="Annual_leave">Godišnji odmor</option>
          <option value="Religious_holiday">Vjerski praznik</option>
          <option value="Sick_leave">Bolovanje</option>
          <option value="Private">Privatni dani</option>
        </select>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">Datum početka</label>
        <input v-model="oooForm.start_date" type="date" class="mt-1 block w-full rounded border-gray-300 text-black" />
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">Datum završetka</label>
        <input v-model="oooForm.end_date" type="date" class="mt-1 block w-full rounded border-gray-300 text-black" />
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">Napomena</label>
        <textarea v-model="oooForm.notes" class="mt-1 block w-full rounded border-black text-black"></textarea>
      </div>
    </form>
  </template>

  <template #footer>
    <button @click="submitOOORequest" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
      Pošalji zahtjev
    </button>
    <button @click="showModal = false" class="ml-2 px-4 py-2 border rounded bg-red-600 hover:bg-red-700">Otkaži</button>
  </template>
</Modal>

  <div class="h-24">
    <Footer />
  </div>
</template>

<script setup>
import Navbar from '@/components/Navbar.vue'
import { router, usePage } from '@inertiajs/vue3'
import Footer from '@/components/Footer.vue'
import Modal from '@/components/Modal.vue'
import SidebarTimesheet from '@/components/SidebarTimesheet.vue'
import { ref, onMounted } from 'vue'
import dayjs from 'dayjs'
import axios from 'axios';
import { computed } from 'vue'

const props = defineProps({
  projects: Array,
  flash: Object,
  oooRequests: Array,
})


const dailyWork = ref({});
const showModal = ref(false);
const page = usePage();
const oooForm = ref({
  type: '',
  start_date: '',
  end_date: '',
  notes: '',
  user_id: page.props.auth.user.id,
});

/* const allSubmitted = computed(() => {
  return Object.values(dailyWork.value).every(entry => entry.status !== 'Draft');
}); */

const oooDays = computed(() => {
  const allDates = [];
  props?.oooRequests.forEach(req => {
    allDates.push(...getDateRange(req.start_date, req.end_date));
  })
  return allDates;
});

function isOOODay(date) {
  return oooDays.value.includes(date);
}

const getExpectedTimeForDate = (dateStr) => {
  if (isOOODay(dateStr)) {
    return {
      label: '0h (OOO)',
      minutes: 0
    }
  }
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

// Pomoćna funkcija koja generiše sve datume između dva datuma (start do end)
function getDateRange(startDate, endDate) {
  const dates = [];
  let current = new Date(startDate);
  current.setDate(current.getDate() + 1);
  const end = new Date(endDate);
  end.setDate(end.getDate() + 1);

  while (current <= end) {
    dates.push(current.toISOString().split('T')[0]); // yyyy-mm-dd
    current.setDate(current.getDate() + 1);
  }
  return dates;
}


const submitOOORequest = () => {
  router.post(route('ooo.store'), oooForm.value, {
    onSuccess: () => {
      showModal.value = false
      oooForm.value = {
        type: '',
        start_date: '',
        end_date: '',
        notes: '',
        user_id: usePage().props.auth.user.id,
      }
    }
  })
}


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

/* const submitDraft = () => {
  router.post(route('timesheets.draft'), { month: selectedMonth.value }, {
    preserveState: true,
    preserveScroll: true,
    onSuccess: (page) => {
      loadTimesheets()
    }
  })
} */

const canSubmitMonth = computed(() => {
  if (!selectedMonth.value) return false;
  const monthToCheck = dayjs(selectedMonth.value).endOf('month');
  const today = dayjs();
  const isMonthFinished = monthToCheck.isBefore(today, 'day');

  return isMonthFinished;
});

const submitSelectedMonth = () => {
  if (!canSubmitMonth.value) {
    alert("Predaja za ovaj mjesec još nije moguća");
    return;
  }
  if (confirm(`Jeste li sigurni da želite predati sve unose za ${dayjs(selectedMonth.value).format('MMMM')} na odobrenje?`)) {
    router.post(route('timesheets.submitMonth'), { month: selectedMonth.value }, {
      preserveScroll: true,
      onSuccess: (page) => {
        loadDailySummary();
      },
      onError: (errors) => {
        console.error("Greška pri predaji mjeseca:", errors);
        alert(`Greška: ${errors.month}`);
      }
    });
  }
};

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