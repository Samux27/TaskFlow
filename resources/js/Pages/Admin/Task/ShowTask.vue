<template>
    <AuthenticatedLayout>
      <!-- Título dinámico -->
      <Head :title="`Tarea #${task.id}`" />
  
      <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <!-- 🗂️ Card principal -->
          <div class="bg-white p-6 rounded-lg shadow border border-gray-200">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Detalles de la tarea</h2>
  
            <!-- Acción rápida -->
            <div class="mb-6 flex justify-between items-center text-sm">
              <div class="space-x-3">
                <Link
                  href="/task"
                  class="text-gray-500 hover:text-indigo-600 transition"
                >← Volver</Link>
              </div>
              <div class="space-x-3">
                <Link
                  :href="`/task/${task.id}/edit`"
                  class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded transition"
                >Editar</Link>
                <button
                  @click="showDeleteModal = true"
                  class="bg-rose-600 hover:bg-rose-700 text-white px-4 py-2 rounded transition"
                >Eliminar</button>
              </div>
            </div>
  
            <!-- ✔︎ Contenido -->
            <div class="grid md:grid-cols-2 gap-6 text-sm text-gray-800">
              <!-- Columna izquierda -->
              <div class="space-y-1">
                <p><strong>ID:</strong> {{ task.id }}</p>
                <p><strong>Título:</strong> {{ task.title }}</p>
                <p><strong>Descripción:</strong> {{ task.description }}</p>
                <p>
                  <strong>Estado:</strong>
                  <span
                    :class="estadoClass(task.estado)"
                    class="px-2 py-0.5 rounded text-white"
                  >
                    {{ capital(task.estado) }}
                  </span>
                </p>
                <p>
                  <strong>Importancia:</strong>
                  <span
                    :class="importanciaClass(task.importancia)"
                    class="px-2 py-0.5 rounded text-white"
                  >
                    {{ capital(task.importancia) }}
                  </span>
                </p>
              </div>
  
              <!-- Columna derecha -->
              <div class="space-y-1">
                <p><strong>Creada por:</strong> {{ task.creator?.name || '—' }}</p>
                <p v-if="task.due_date"><strong>Fecha límite:</strong> {{ format(task.due_date) }}</p>
                <p><strong>Creada:</strong> {{ format(task.created_at) }}</p>
                <p><strong>Actualizada:</strong> {{ format(task.updated_at) }}</p>
                <div>
                  <p class="font-semibold">Asignados:</p>
                  <ul v-if="task.assignees?.length" class="list-disc list-inside ml-4">
                    <li v-for="u in task.assignees" :key="u.id">{{ u.name }}</li>
                  </ul>
                  <p v-else class="italic text-gray-500">Sin asignar</p>
                </div>
              </div>
            </div>
          </div>
        </div>
  
        <!-- 🔴 Modal confirmar eliminación -->
        <div
          v-if="showDeleteModal"
          class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
        >
          <div
            class="bg-white p-6 rounded shadow-lg border border-gray-300 w-full max-w-md"
          >
            <h3 class="text-lg font-semibold text-gray-800">
              ¿Eliminar tarea #{{ task.id }}?
            </h3>
            <p class="text-gray-600 mt-2">Esta acción no se puede deshacer.</p>
  
            <div class="mt-4 flex gap-4 justify-center">
              <button
                @click="deleteTask"
                class="bg-rose-600 hover:bg-rose-700 text-white px-4 py-2 rounded"
              >
                Eliminar
              </button>
              <button
                @click="showDeleteModal = false"
                class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded"
              >
                Cancelar
              </button>
            </div>
          </div>
        </div>
      </div>
    </AuthenticatedLayout>
  </template>
  
  <script setup>
  import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
  import { Head, Link, router } from '@inertiajs/vue3'
  import { ref } from 'vue'
  import { useToast } from 'vue-toastification'
  
  const { task } = defineProps({ task: Object })
  const toast = useToast()
  const showDeleteModal = ref(false)
  
  // Helpers de estilo
  const capital = (s) => s.charAt(0).toUpperCase() + s.slice(1)
  
  const estadoClass = (estado) => {
    const e = normalize(estado)
    return {
      'bg-emerald-500': e === 'finalizada',
      'bg-gray-500': e === 'pendiente',
      'bg-yellow-500': e === 'en progreso',
      'bg-red-500': !['finalizada', 'pendiente'].includes(e),
    }
  }
  
  const importanciaClass = (imp) => {
    return {
      'bg-red-500': imp === 'alta',
      'bg-green-500': imp === 'baja',
      'bg-yellow-500': imp !== 'baja' && imp !== 'alta',
    }
  }
  
  const normalize = (s) =>
    s?.toString().trim().toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '')
  
  const format = (d) =>
    new Date(d).toLocaleString('es-ES', { dateStyle: 'medium', timeStyle: 'short' })
  
  // Eliminar tarea
  const deleteTask = () => {
    router.delete(`/task/${task.id}`, {
      onSuccess: () => {
        toast.success('Tarea eliminada correctamente')
        router.visit('/task')
      },
      onError: () => toast.error('Error al eliminar la tarea'),
    })
  }
  </script>
  