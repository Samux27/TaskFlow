<template>
  <AuthenticatedLayout>
    <Head title="Editar Tarea" />

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white p-6 rounded-lg shadow border border-gray-200">
          <h2 class="text-xl font-bold text-gray-800 mb-4">Editar Tarea</h2>

          <form @submit.prevent="submitForm">
            <!-- Título -->
            <div class="mb-4">
              <label for="title" class="block text-gray-700">Título</label>
              <input
                v-model="form.title"
                id="title"
                type="text"
                autocomplete="off"
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 p-2"
              />
              <small v-if="form.errors.title" class="text-red-500">{{ form.errors.title }}</small>
            </div>

            <!-- Descripción -->
            <div class="mb-4">
              <label for="description" class="block text-gray-700">Descripción</label>
              <textarea
                v-model="form.description"
                id="description"
                rows="4"
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 p-2"
              ></textarea>
              <small v-if="form.errors.description" class="text-red-500">{{ form.errors.description }}</small>
            </div>
            <UserPicker
              v-if="showModal"
              v-model:show="showModal"
               v-model:selected="form.assigned_users"
             :users="employees"
             />
            <!-- Importancia -->
            <div class="mb-4">
              <label for="importancia" class="block text-gray-700">Importancia</label>
              <select
                v-model="form.importancia"
                id="importancia"
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 p-2"
              >
                <option value="baja">Baja</option>
                <option value="media">Media</option>
                <option value="alta">Alta</option>
              </select>
              <small v-if="form.errors.importancia" class="text-red-500">{{ form.errors.importancia }}</small>
            </div>

            <!-- Estado -->
            <div class="mb-4">
              <label for="estado" class="block text-gray-700">Estado</label>
              <select
                v-model="form.estado"
                id="estado"
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 p-2"
              >
                <option value="pendiente">Pendiente</option>
                <option value="en progreso">En progreso</option>
                <option value="bloqueada">Bloqueada</option>
                <option value="finalizada">Finalizada</option>
              </select>
              <small v-if="form.errors.estado" class="text-red-500">{{ form.errors.estado }}</small>
            </div>

            <!-- Fechas -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label for="create_date" class="block text-gray-700">Fecha de creación</label>
                <input
                  v-model="form.create_date"
                  id="create_date"
                  type="date"
                  class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 p-2"
                />
                <small v-if="form.errors.create_date" class="text-red-500">{{ form.errors.create_date }}</small>
              </div>
              <div>
                <label for="dead_line" class="block text-gray-700">Fecha límite</label>
                <input
                  v-model="form.dead_line"
                  id="dead_line"
                  type="date"
                  class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 p-2"
                />
                <small v-if="form.errors.dead_line" class="text-red-500">{{ form.errors.dead_line }}</small>
              </div>
            </div>

            <!-- Asignar Usuarios -->
            <div class="mb-4">
              <label class="block text-gray-700">Asignar a</label>
              <button
                type="button"
                @click="showModal = true"
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 p-2 text-center"
              >
                Seleccionar personas
              </button>
              <small v-if="form.errors.assigned_users" class="text-red-500">{{ form.errors.assigned_users }}</small>
            </div>

            <div class="flex justify-end gap-4 mt-6">
              <button
                type="submit"
                class="bg-indigo-600 text-white px-4 py-2 rounded transition hover:bg-indigo-700 disabled:opacity-50"
                :disabled="form.processing"
              >
                {{ form.processing ? 'Guardando…' : 'Guardar Cambios' }}
              </button>
              <Link href="/task" class="bg-gray-300 text-gray-800 px-4 py-2 rounded transition hover:bg-gray-400">
                Cancelar
              </Link>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Modal -->
    
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, watch } from 'vue'
import { useForm, Head, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import UserPicker from '@/Components/UserPicker.vue'

const props = defineProps({
  employees: { type: Array, default: () => [] },
  task:      { type: Object, required: true },
})

const employees = ref(props.employees ?? [])
watch(() => props.employees, v => (employees.value = v))

// --- Formulario -------------------------------------------------------------
const form = useForm({
  title: '',
  description: '',
  importancia: 'media',
  estado: 'pendiente',
  create_date: '',
  dead_line: '',
  assigned_users: [],
})

const resetForm = () => {
  const t = props.task
  form.title          = t.title ?? ''
  form.description    = t.description ?? ''
  form.importancia    = t.importancia ?? 'media'
  form.estado         = t.estado ?? 'pendiente'
  form.create_date    = t.create_date?.slice(0, 10) ?? ''
  form.dead_line      = t.dead_line?.slice(0, 10) ?? ''
  form.assigned_users = t.assigned_users?.map(u => u.id) ?? []
}

watch(() => props.task, resetForm, { immediate: true })

// --- Envío ------------------------------------------------------------------
const submitForm = () => {
  router.put(`/task/${props.task.id}`, form.data(), {
    preserveScroll: true,
    onStart:   () => (form.processing = true),
    onFinish:  () => (form.processing = false),
  })
}

// --- Modal ------------------------------------------------------------------
const showModal = ref(false)
</script>

<style scoped>
/* Personaliza si es necesario */
</style>
