<template>
  <AuthenticatedLayout>
    <Head :title="`Tareas de ${empleado.name}`" />

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="bg-white p-6 rounded-lg shadow border border-gray-200">

          <!-- Botón Volver -->
          <<!-- Botón Volver -->
<div class="mb-4">
  <Link
    :href="userRole === 'admin' ? '/users' : '/mis-empleados'"
    class="text-gray-500 hover:text-indigo-600 transition"
  >
    ← Volver 
  </Link>
</div>

          <!-- Título + botón crear tarea -->
          <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-gray-800">Tareas asignadas a {{ empleado.name }}</h2>
<Link
v-if="userRole === 'boss' "
  :href="`/mis-empleados/${empleado.id}/tareas/crear`"
  class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700"
>
  + Nueva Tarea
</Link>
          </div>

          <!-- Tabla con DataTable -->
          <table ref="tableRef" class="display nowrap w-full border text-sm" style="width: 100%">
            <thead class="bg-gray-100 text-gray-700">
              <tr>
                <th>Título</th>
                <th>Importancia</th>
                <th>Estado</th>
                <th>Fecha</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="tarea in tareas" :key="tarea.id" class="hover:bg-gray-50">
                <td class="px-4 py-2">{{ tarea.title }}</td>

                <td class="px-4 py-2">
                  <span class="text-white text-sm px-3 py-1 rounded" :class="colorImportancia(tarea.importancia)">
                    {{ tarea.importancia }}
                  </span>
                </td>

                <td class="px-4 py-2">
                  <span class="text-white text-sm px-3 py-1 rounded" :class="colorEstado(tarea.estado)">
                    {{ tarea.estado }}
                  </span>
                </td>

                <td class="px-4 py-2">{{ formatDate(tarea.create_date) }}</td>
                <td class="px-4 py-2">
      <Link :href="`/task/${tarea.id}`" class="text-green-600 hover:underline">Ver</Link>
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
import { Head, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { onMounted, ref } from 'vue'
import $ from 'jquery'
import 'datatables.net'
import 'datatables.net-responsive'
import 'datatables.net-dt/css/dataTables.dataTables.min.css'
import 'datatables.net-responsive-dt/css/responsive.dataTables.min.css'
import { info } from 'autoprefixer'

const props = defineProps({
  empleado: Object,
  tareas: Array,
  userRole: String
})

const tableRef = ref(null)

onMounted(() => {
  $(tableRef.value).DataTable({
    responsive: true,
    pageLength: 10,
    lengthChange: false,
    info    : false,
    order: [[3, 'desc']],
    language: {
      search: 'Buscar:',
      zeroRecords: 'No se encontraron tareas',
      paginate: {
        first: 'Primero',
        last: 'Último',
        next: 'Siguiente',
        previous: 'Anterior',
      }
    }
  })
})

const colorEstado = (val) => {
  const estado = val?.toLowerCase()?.trim()
  return {
    'bg-gray-500': estado === 'pendiente',
    'bg-yellow-500': estado === 'en progreso',
    'bg-emerald-500': estado === 'finalizada',
    'bg-red-500': !['pendiente', 'en progreso', 'finalizada'].includes(estado),
  }
}

const colorImportancia = (val) => {
  const importancia = val?.toLowerCase()?.trim()
  return {
    'bg-green-500': importancia === 'baja',
    'bg-red-500': importancia === 'alta',
    'bg-yellow-500': !['baja', 'alta'].includes(importancia),
  }
}

const formatDate = (fecha) => new Date(fecha).toLocaleString('es-ES')

const abrirCrearTarea = () => {
  // Redirigir a formulario de creación con el empleado preseleccionado si deseas
  // router.visit(`/task/create?empleado_id=${empleado.id}`)
}
</script>
