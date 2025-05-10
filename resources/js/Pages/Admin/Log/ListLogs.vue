<template>
  <AuthenticatedLayout>
    <Head title="Historial de Logs" />

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white p-6 rounded-lg shadow border border-gray-200">
          <h2 class="text-xl font-bold text-gray-800 mb-4">Historial de Logs</h2>

          <table ref="logTable" class="display nowrap w-full border border-gray-300" style="width:100%">
            <thead>
              <tr>
                <th>ID</th>
                <th>Usuario Id</th>
                <th>Usuario</th>
                <th>Acción</th>
                <th>Fecha</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="log in logs" :key="log.id" @click="mostrarLog(log)" class="cursor-pointer hover:bg-gray-100">
                <td>{{ log.id }}</td>
                <td>{{ log.user_id ?? 'Desconocido' }}</td>
                <td>{{ log.user?.name ?? 'Desconocido' }}</td>
                <td>{{ log.details }}</td>
                <td>{{ formatDate(log.created_at) }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Modal de detalles del log -->
    <div v-if="mostrarModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-lg border border-gray-300">
        <h3 class="text-lg font-semibold text-gray-800 mb-2">Detalles del Log</h3>
        <div class="text-sm text-gray-700 space-y-2">
          
          <p><strong>Usuario ID:</strong> {{ logSeleccionado.user_id ?? 'Desconocido' }}</p>
          <p><strong>Nombre de Usuario:</strong> {{ logSeleccionado.user?.name ?? 'Desconocido' }}</p>
          <p><strong>Acción:</strong> {{ logSeleccionado.action }}</p>
          <p><strong>Detalles:</strong> {{ logSeleccionado.details }}</p>
          <p><strong>IP:</strong> {{ logSeleccionado.ip_address }}</p>
          <p><strong>Fecha:</strong> {{ formatDate(logSeleccionado.created_at) }}</p>
        </div>
        <div class="mt-4 flex justify-end">
          <button @click="cerrarModal" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded">Cerrar</button>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'
import { onMounted, ref } from 'vue'
import 'datatables.net-dt/css/dataTables.dataTables.min.css'
import 'datatables.net-responsive-dt/css/responsive.dataTables.min.css'
import $ from 'jquery'
import 'datatables.net'
import 'datatables.net-responsive'

const { logs } = defineProps({ logs: Array })
const logTable = ref(null)

const mostrarModal = ref(false)
const logSeleccionado = ref({})

const mostrarLog = (log) => {
  logSeleccionado.value = log
  mostrarModal.value = true
}

const cerrarModal = () => {
  mostrarModal.value = false
  logSeleccionado.value = {}
}

const formatDate = (fecha) => {
  return new Date(fecha).toLocaleString('es-ES')
}

onMounted(() => {
  $(logTable.value).DataTable({
    responsive: true,
    info: false,
    lengthChange: false,
    pageLength: 10,
    language: {
      search: "Buscar:",
      zeroRecords: "No se encontraron logs",
      paginate: {
        previous: "Anterior",
        next: "Siguiente"
      }
    }
  })
})
</script>
