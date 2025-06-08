<template>
  <AuthenticatedLayout>
    <!-- 🏷️  Meta título -->
    <Head :title="`Tarea #${task.id}`" />

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- ╔══════════════════════════════════════════════════╗ -->
        <!--   Card principal                                  -->
        <!-- ╚══════════════════════════════════════════════════╝ -->
        <div class="bg-white p-6 rounded-lg shadow border border-gray-200">
          <h2 class="text-xl font-bold text-gray-800 mb-4">Detalles de la tarea</h2>

          <!-- 🔗 Navegación rápida -->
          <div class="mb-6 flex justify-between items-center text-sm">
            <Link
              href="/task"
              class="text-gray-500 hover:text-indigo-600 transition"
            >← Volver</Link>

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

          <!-- 📝 Datos -->
          <div class="grid md:grid-cols-3 gap-6 text-sm text-gray-800">
            <!-- ─── Columna 1 ─── -->
            <div class="space-y-1 break-words">
              <p><strong>ID:</strong> {{ task.id }}</p>
              <p><strong>Título:</strong> {{ task.title }}</p>
              <p><strong>Descripción:</strong> {{ task.description }}</p>
              
            </div>

            <!-- ─── Columna 2 ─── -->
            <div class="space-y-1">
              <p>
                <strong>Estado:</strong>
                <span :class="badgeClass(task.estado, 'estado')" class="px-2 py-0.5 rounded text-white">
                  {{ capital(task.estado) }}
                </span>
              </p>
              <p>
                <strong>Importancia:</strong>
                <span :class="badgeClass(task.importancia, 'importancia')" class="px-2 py-0.5 rounded text-white">
                  {{ capital(task.importancia) }}
                </span>
              </p>
              <p><strong>Fecha creación :</strong> {{ format(task.create_date) }}</p>
              <p v-if="task.deadLine"><strong>Fecha límite:</strong> {{ format(task.deadLine) }}</p>
              <p v-if="task.complete_at"><strong>Completada el:</strong> {{ format(task.complete_at) }}</p>
            </div>

            <!-- ─── Columna 3 ─── -->
            <div class="space-y-1">
              <p><strong>Jefe asignador:</strong> {{ task.boss?.name+" ("+task.boss?.id+")"  || '—' }}</p>
              
              <p><strong>Actualizada:</strong> {{ format(task.updated_at) }}</p>

              <div>
                <p class="font-semibold">Personas relacionadas:</p>
                <ul v-if="relatedPeople.length" class="list-disc list-inside ml-4">
                  <li v-for="u in relatedPeople" :key="u.id">{{ u.id + " "+ u.name }}</li>
                </ul>
                <p v-else class="italic text-gray-500">Sin asignar</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- 💣 Modal eliminar -->
      <div
        v-if="showDeleteModal"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
      >
        <div class="bg-white p-6 rounded shadow-lg border border-gray-300 w-full max-w-md">
          <h3 class="text-lg font-semibold text-gray-800">¿Eliminar tarea #{{ task.id }}?</h3>
          <p class="text-gray-600 mt-2">Esta acción no se puede deshacer.</p>

          <div class="mt-4 flex gap-4 justify-center">
            <button @click="deleteTask" class="bg-rose-600 hover:bg-rose-700 text-white px-4 py-2 rounded">
              Eliminar
            </button>
            <button @click="showDeleteModal = false" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded">
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
import { ref, computed } from 'vue'
import { useToast } from 'vue-toastification'

/* Props recibidos desde el back-end */
const { task } = defineProps({ task: Object })

/* Toast */
const toast = useToast()

/* Modal */
const showDeleteModal = ref(false)

/* Helpers de formato */
const capital = (s) => s ? s.charAt(0).toUpperCase() + s.slice(1) : ''
const format  = (d) => d ? new Date(d).toLocaleString('es-ES', { dateStyle: 'medium' }) : '—'

/* Badges */
const badgeClass = (value, type) => {
  const val = (value || '').toString().toLowerCase()
  if (type === 'estado') {
    return {
      'bg-gray-500': val === 'pendiente',
      'bg-yellow-500': val === 'en progreso',
      'bg-emerald-500': val === 'finalizada',
      'bg-red-500': !['pendiente', 'en progreso', 'finalizada'].includes(val),
    }
  }
  if (type === 'importancia') {
    return {
      'bg-green-500': val === 'baja',
      'bg-red-500': val === 'alta',
      'bg-yellow-500': !['baja', 'alta'].includes(val),
    }
  }
  return {}
}



/* Personas relacionadas: jefe + asignados */
const relatedPeople = computed(() => {
  const set = new Map()
  ;[...(task.assignees || []), task.boss].forEach((p) => {
    if (p && p.id) set.set(p.id, p)
  })
  return [...set.values()]
})

/* Eliminar tarea */
function deleteTask() {
  router.delete(`/task/${task.id}`, {
    onSuccess: () => {
      toast.success('Tarea eliminada correctamente')
      router.visit('/task')
    },
    onError: () => toast.error('Error al eliminar la tarea'),
  })
}
console.log('Task:', task)
</script>
