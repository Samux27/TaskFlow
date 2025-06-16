<template>
  <AuthenticatedLayout>
    <Head title="Tareas Urgentes" />
    <div class="max-w-7xl mx-auto py-12 px-4 space-y-6">
      <h2 class="text-2xl font-bold text-red-600">Tareas Urgentes</h2>

      <table class="min-w-full bg-white border rounded shadow">
        <thead class="bg-gray-100 text-gray-700">
          <tr>
            <th class="px-4 py-2 text-left">Título</th>
            <th class="px-4 py-2">Importancia</th>
            <th class="px-4 py-2">Estado</th>
            <th class="px-4 py-2">Fecha Límite</th>
            <th class="px-4 py-2">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="tarea in tareas" :key="tarea.id" class="hover:bg-gray-50">
            <td class="px-4 py-2">{{ tarea.title }}</td>
            <td class="px-4 py-2 text-center">
              <span :class="colorImportancia(tarea.importancia)" class="text-white px-2 py-1 rounded text-sm">
                {{ tarea.importancia }}
              </span>
            </td>
            <td class="px-4 py-2 text-center">
              <span :class="colorEstado(tarea.estado)" class="text-white px-2 py-1 rounded text-sm">
                {{ tarea.estado }}
              </span>
            </td>
            <td class="px-4 py-2 text-center">{{ formatDate(tarea.deadLine) }}</td>
            <td class="px-4 py-2 text-center">
              <Link :href="`/task/${tarea.id}`" class="text-indigo-600 hover:underline">Ver</Link>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
  tareas: Array,
  userRole: String
})

const formatDate = (fecha) => new Date(fecha).toLocaleString('es-ES')

const colorEstado = (estado) => {
  estado = estado?.toLowerCase()
  return {
    'bg-gray-500': estado === 'pendiente',
    'bg-yellow-500': estado === 'en progreso',
    'bg-emerald-500': estado === 'finalizada',
    'bg-red-500': !['pendiente', 'en progreso', 'finalizada'].includes(estado),
  }
}

const colorImportancia = (val) => {
  const importancia = val?.toLowerCase()
  return {
    'bg-green-500': importancia === 'baja',
    'bg-red-500': importancia === 'alta',
    'bg-yellow-500': !['baja', 'alta'].includes(importancia),
  }
}
</script>
