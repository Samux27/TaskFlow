<template>
  <AuthenticatedLayout>
    <Head title="Mis Empleados" />

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white p-6 rounded-lg shadow border border-gray-200">
          <h2 class="text-xl font-bold text-gray-800 mb-4">Empleados asignados</h2>

          <!-- Tabla -->
          <table ref="tableRef" class="display nowrap w-full border border-gray-300" style="width: 100%">
            <thead>
              <tr class="bg-gray-100 text-gray-700">
                <th>ID</th>
                <th>Nombre</th>
                <th>Correo</th>
                <th>DNI</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="empleado in empleados"
                :key="empleado.id"
                class="cursor-pointer hover:bg-gray-100"
              >
                <td>{{ empleado.id }}</td>
                <td>{{ empleado.name }} {{ empleado.surname }}</td>
                <td>{{ empleado.email }}</td>
                <td>{{ empleado.dni }}</td>
                <td @click.stop>
                  <Link
                    :href="`/mis-empleados/${empleado.id}/tareas`"
                    class="text-indigo-600 hover:underline"
                  >
                    Ver tareas
                  </Link>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { onMounted, ref } from 'vue'
import $ from 'jquery'
import 'datatables.net'
import 'datatables.net-responsive'
import 'datatables.net-dt/css/dataTables.dataTables.min.css'
import 'datatables.net-responsive-dt/css/responsive.dataTables.min.css'

const props = defineProps({
  empleados: Array,
  userRole: String
})

const tableRef = ref(null)

onMounted(() => {
  $(tableRef.value).DataTable({
    responsive: true,
    pageLength: 10,
    lengthChange: false,
    info: false,
    order: [[0, 'desc']],
    paging: true,
        
        
        
    language: {
      search: 'Buscar:',
      zeroRecords: 'No se encontraron empleados',
      paginate: {
        first: 'Primero',
        last: 'Último',
        next: 'Siguiente',
        previous: 'Anterior'
      }
    }
  })
})
</script>
