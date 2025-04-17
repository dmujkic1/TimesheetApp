<template>
    <div>
      <Navbar />
      <h1 class="text-xl font-bold">Podaci uposlenika</h1>
      Ime uposlenika: {{ employee?.first_name ?? "Nema naziva" }} <br>
      Prezime uposlenika: {{ employee?.last_name ?? "Nema prezimena" }} <br>
      Email uposlenika: {{ employee?.email ?? "Nema maila" }} <br>
      Titula uposlenika: {{ employee?.job_title ?? "Nema titule" }} <br>
      Datum zaposlenja: {{ employee?.hire_date ?? "Nema datuma zaposlenja" }} <br>
      <Footer />
    </div>
  </template>  <!-- //PITATI U DOKUMENTACIJI JE DRUGACIJE -->
  
  <script setup>
  import Navbar from '@/components/Navbar.vue';
  import Footer from '@/components/Footer.vue';
  import { usePage } from '@inertiajs/vue3'
  
  const { props } = usePage()      // ili je moglo const page=usePage() i onda const props=page.props
  const employee = props.employee //employees iz EmployeesController
  </script>
  