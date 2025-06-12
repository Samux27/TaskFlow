<template>
  <AuthenticatedLayout>
    <!-- 🏷️  Meta título -->

    <Head :title="`Tarea #${props.task.id}`" />

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- ╔══════════════════════════════════════════════════╗ -->
        <!--   Card principal                                  -->
        <!-- ╚══════════════════════════════════════════════════╝ -->
        <div class="bg-white p-6 rounded-lg shadow border border-gray-200">
          <h2 class="text-xl font-bold text-gray-800 mb-4">Detalles de la tarea</h2>

          <!-- 🔗 Navegación rápida -->
          <div class="mb-6 flex justify-between items-center text-sm">
            <Link :href="props.userRole === 'admin' ? '/task' : '/mis-tareas'"
              class="text-gray-500 hover:text-indigo-600 transition">
            ← Volver
            </Link>
            <div class="space-x-3" v-if="userRole !== 'employee'">
              <Link :href="`/task/${props.task.id}/edit`"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded transition">Editar</Link>
              <button @click="showDeleteModal = true"
                class="bg-rose-600 hover:bg-rose-700 text-white px-4 py-2 rounded transition">Eliminar</button>
            </div>
            <div v-else>
              <button @click="openStatusModal"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded transition">
                Cambiar el estado de la Tarea
              </button>
            </div>
          </div>

          <!-- 📝 Datos -->
          <div class="grid md:grid-cols-3 gap-6 text-sm text-gray-800">
            <!-- ─── Columna 1 ─── -->
            <div class="space-y-1 break-words">
              <p><strong>ID:</strong> {{ props.task.id }}</p>
              <p><strong>Título:</strong> {{ props.task.title }}</p>
              <p><strong>Descripción:</strong> {{ props.task.description }}</p>
            </div>

            <!-- ─── Columna 2 ─── -->
            <div class="space-y-1">
              <p>
                <strong>Estado:</strong>
                <span :class="badgeClass(props.task.estado, 'estado')" class="px-2 py-0.5 rounded text-white">
                  {{ capital(props.task.estado) }}
                </span>
              </p>
              <p>
                <strong>Importancia:</strong>
                <span :class="badgeClass(props.task.importancia, 'importancia')" class="px-2 py-0.5 rounded text-white">
                  {{ capital(props.task.importancia) }}
                </span>
              </p>
              <p><strong>Fecha creación :</strong> {{ format(props.task.create_date) }}</p>
              <p v-if="props.task.deadLine"><strong>Fecha límite:</strong> {{ format(props.task.deadLine) }}</p>
              <p v-if="props.task.complete_at"><strong>Completada el:</strong> {{ format(props.task.complete_at) }}</p>
            </div>

            <!-- ─── Columna 3 ─── -->
            <div class="space-y-1">
              <p><strong>Jefe asignador:</strong> {{ props.task.boss?.name + " (" + props.task.boss?.id + ")" || '—' }}
              </p>
              
              <div>
                <p class="font-semibold">Personas relacionadas: <span
                    class="ml-2 cursor-pointer text-blue-500 font-bold"
                    title="El numero antes del nombre de la persona es su ID">?</span></p>
                <ul v-if="relatedPeople.length" class="list-disc list-inside ml-4">
                  <li v-for="u in relatedPeople" :key="u.id"><i>{{ "" + u.id }}</i> {{ " " + u.name }}</li>
                </ul>
                <p v-else class="italic text-gray-500">Sin asignar</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- 💣 Modal eliminar -->
      <div v-if="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded shadow-lg border border-gray-300 w-full max-w-md">
          <h3 class="text-lg font-semibold text-gray-800">¿Eliminar tarea #{{ props.task.id }}?</h3>
          <p class="text-gray-600 mt-2">Esta acción no se puede deshacer.</p>
          <div class="mt-4 flex gap-4 justify-center">
            <button @click="deleteTask" class="bg-rose-600 hover:bg-rose-700 text-white px-4 py-2 rounded">
              Eliminar
            </button>
            <button @click="showDeleteModal = false"
              class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded">
              Cancelar
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- CHAT -->
    <div class="mt-8 bg-white rounded shadow p-4 border border-gray-100">
      <h3 class="text-lg font-bold mb-2">Chat de la tarea <span class="ml-2 cursor-pointer text-blue-500 font-bold"
          title="Se debe reinciar la pagina para actualizar la conversacion.">?</span></h3>
      <div class="max-h-72 overflow-y-auto border rounded mb-4 p-2 bg-gray-50" v-if="props.task.comments.length">
        <div v-for="comment in props.task.comments" :key="comment.id" class="mb-2">
          <div class="text-sm text-gray-600">
            <b>{{ comment.user?.name }}</b> <span class="text-xs">{{ formatDate(comment.created_at) }}</span>
          </div>
          <div class="ml-2 p-2 bg-white rounded shadow text-gray-800">{{ comment.content }}</div>
        </div>
      </div>
      <div v-else class="text-gray-500 mb-4">No hay mensajes todavía.</div>
      <form @submit.prevent="sendComment" class="flex gap-2">
        <input v-model="newComment" type="text" placeholder="Escribe un mensaje..."
          class="flex-1 border rounded px-3 py-2" maxlength="2000" required />
        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
          Enviar
        </button>
      </form>
    </div>

    <!-- Mini Modal de cambio de estado -->
    <div v-if="showStatusModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-40 z-50">
      <div class="bg-white p-6 rounded-lg shadow-xl border border-gray-200 w-full max-w-sm relative">
        <button @click="showStatusModal = false" class="absolute top-2 right-2 text-gray-400 hover:text-rose-600"
          aria-label="Cerrar">&times;</button>
        <h3 class="text-lg font-bold mb-4">Cambiar estado de la tarea</h3>
        <form @submit.prevent="updateTaskStatus">
          <label for="newStatus" class="block text-gray-700 mb-2">Nuevo estado</label>
          <select v-model="selectedStatus" id="newStatus" class="w-full border-gray-300 rounded p-2 mb-4">
            <option value="pendiente">Pendiente</option>
            <option value="en progreso">En progreso</option>
            <option value="finalizada">Finalizada</option>
          </select>
          <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded transition">
            Guardar
          </button>
        </form>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import { useToast } from 'vue-toastification'
import "vue-toastification/dist/index.css";

const props = defineProps({
  task: Object,
  userRole: String,
})
const task = props.task
const showDeleteModal = ref(false)
const showStatusModal = ref(false)
const selectedStatus = ref(task.estado)
const toast = useToast()

// --- CHAT (opcional: si no usas esta función, ignórala) ---
const newComment = ref('')
function sendComment() {
  if (!newComment.value.trim()) return
  router.post(`/task/${task.id}/comments`, {
    content: newComment.value
  }, {
    onSuccess: () => {
      toast.success('Comentario enviado')
      // Recarga la página (o mejor, recarga sólo los comentarios si tu backend lo soporta)
      router.reload({ only: ['task'] });
      newComment.value = ''
    }
  })
}

// --- CAMBIO DE ESTADO ---
function openStatusModal() {
  selectedStatus.value = props.task.estado
  showStatusModal.value = true
}

function updateTaskStatus() {
  if (!selectedStatus.value) return toast.error('Selecciona un estado')
  router.patch(`/task/${task.id}/status`,
    { estado: selectedStatus.value },
    {
      preserveScroll: true,
      onSuccess: () => {
        toast.success('Estado actualizado correctamente')
        showStatusModal.value = false
        router.reload({ only: ['task'] }) // Refresca sólo la tarea (recomendado si usas Inertia)
        // Si no funciona, usa location.reload() (pero perderás SPA)
      },
      onError: () => {
        toast.error('Error al actualizar el estado')
      }
    }
  )
}

// --- Helpers de formato y badges ---
const capital = (s) => s ? s.charAt(0).toUpperCase() + s.slice(1) : ''
const format = (d) => d ? new Date(d).toLocaleString('es-ES', { dateStyle: 'medium' }) : '—'
function formatDate(date) {
  return new Date(date).toLocaleString('es-ES', {
    dateStyle: 'short',
    timeStyle: 'short',
  })
}
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

// --- Personas relacionadas: jefe + asignados ---

const relatedPeople = computed(() => {
  const set = new Map()
    ;[...(props.task.assigned_users || []), task.boss].forEach((p) => {
      if (p && p.id) set.set(p.id, p)
    })
  return [...set.values()]
})

// --- ELIMINAR TAREA ---
function deleteTask() {
  router.delete(`/task/${props.task.id}`, {
    onSuccess: () => {
      toast.success('Tarea eliminada correctamente')
      router.visit(props.userRole === 'admin' ? '/task' : '/mis-tareas')
    }, // si el rol es diferente a admin, redirige a mis-tareas
    onError: () => toast.error('Error al eliminar la tarea'),
  })
}

</script>
