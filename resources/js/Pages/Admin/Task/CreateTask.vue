<template>
  <AuthenticatedLayout>
    <Head title="Crear Tarea" />

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white p-6 rounded-lg shadow border border-gray-200">
          <h2 class="text-xl font-bold text-gray-800 mb-4">Crear Nueva Tarea</h2>

          <!-- Formulario -->
          <form @submit.prevent="submitForm" enctype="multipart/form-data">
            <!-- Título -->
            <div class="mb-4">
              <label for="title" class="block text-gray-700">Título</label>
              <input
                v-model="form.title"
                type="text"
                id="title"
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 p-2"
              />
              <!-- Mostrar error -->
              <div v-if="form.errors.title" class="text-red-500 text-sm mt-1">{{ form.errors.title }}</div>
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
              <!-- Mostrar error -->
              <div v-if="form.errors.description" class="text-red-500 text-sm mt-1">{{ form.errors.description }}</div>
            </div>

            <!-- Empleado asignado -->
            <div class="mb-4">
              <label for="employe_id" class="block text-gray-700">Asignar a</label>
              <button
                type="button"
                @click="showModal = true"
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 p-2 text-center"
              >
                Seleccionar personas
              </button>
              <!-- Mostrar error -->
              <div v-if="form.errors.assigned_users" class="text-red-500 text-sm mt-1">{{ form.errors.assigned_users }}</div>
            </div>

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
              <!-- Mostrar error -->
              <div v-if="form.errors.importancia" class="text-red-500 text-sm mt-1">{{ form.errors.importancia }}</div>
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
              <!-- Mostrar error -->
              <div v-if="form.errors.estado" class="text-red-500 text-sm mt-1">{{ form.errors.estado }}</div>
            </div>

            <!-- Fecha de creación -->
            <div class="mb-4">
              <label for="create_date" class="block text-gray-700">Fecha de creación</label>
              <input
                v-model="form.create_date"
                type="date"
                id="create_date"
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 p-2"
              />
              <!-- Mostrar error -->
              <div v-if="form.errors.create_date" class="text-red-500 text-sm mt-1">{{ form.errors.create_date }}</div>
            </div>

            <!-- Fecha límite -->
            <div class="mb-4">
              <label for="deadLine" class="block text-gray-700">Fecha límite</label>
              <input
                v-model="form.deadLine"
                type="date"
                id="deadLine"
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 p-2"
              />
              <!-- Mostrar error -->
              <div v-if="form.errors.deadLine" class="text-red-500 text-sm mt-1">{{ form.errors.deadLine }}</div>
            </div>

            <!-- Archivos -->
            <div class="mb-6">
              <label for="archivos" class="block text-gray-700">Adjuntar archivos</label>
              <input
                type="file"
                id="archivos"
                multiple
                @change="handleFiles"
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 p-2"
              />
              <!-- Mostrar error -->
              <div v-if="form.errors.archivos" class="text-red-500 text-sm mt-1">{{ form.errors.archivos }}</div>
            </div>

            <!-- Botones -->
            <div class="flex justify-end gap-4">
              <button
                type="submit"
                class="bg-indigo-600 text-white px-4 py-2 rounded transition hover:bg-indigo-700"
                :disabled="form.processing"
              >
                Crear Tarea
              </button>
              <Link
                href="/task"
                class="bg-gray-300 text-gray-800 px-4 py-2 rounded transition hover:bg-gray-400"
              >
                Cancelar
              </Link>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Modal para Asignar Usuarios -->
    <div v-if="showModal" class="fixed inset-0 bg-gray-500 bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-lg">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Seleccionar Usuarios</h2>

        <!-- Búsqueda -->
        <input
          v-model="searchQuery"
          type="text"
          class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 p-2 mb-4"
          placeholder="Buscar por nombre"
        />

        <!-- Lista de usuarios -->
        <ul>
          <li
            v-for="user in filteredUsers"
            :key="user.id"
            class="flex items-center space-x-4 mb-2 p-4 border rounded-lg shadow-sm"
            :class="{
              'bg-blue-100': user.role === 'employee',
              'bg-green-100': user.role === 'boss',
              'bg-red-100': user.role === 'admin'
            }"
          >
            <!-- Casilla de selección -->
            <input
              type="checkbox"
              :value="user.id"
              v-model="form.assigned_users"
              class="h-4 w-4"
            />

            <!-- Información del usuario -->
            <div class="flex flex-col">
              <span class="font-semibold">{{ user.name }} {{ user.surname }}</span>
              <span class="text-sm text-gray-600">ID: {{ user.id }}</span>
              <span class="text-sm text-gray-600">DNI: {{ user.dni }}</span>
            </div>

            <!-- Role badge -->
            <span
              class="ml-auto text-xs px-2 py-1 rounded-full"
              :class="{
                'bg-blue-500 text-white': user.roles.some(role => role.name === 'employee'),
                'bg-green-500 text-white': user.roles.some(role => role.name === 'boss'),
                'bg-red-500 text-white': user.roles.some(role => role.name === 'admin')
              }"
            >
              {{ user.roles.map(role => role.name).join(', ') }}
            </span>
          </li>
        </ul>

        <!-- Botones del modal -->
        <div class="flex justify-end gap-4 mt-4">
          <button
            @click="showModal = false"
            class="bg-gray-300 text-gray-800 px-4 py-2 rounded transition hover:bg-gray-400"
          >
            Cerrar
          </button>
          <button
            @click="showModal = false"
            class="bg-indigo-600 text-white px-4 py-2 rounded transition hover:bg-indigo-700"
          >
            Guardar
          </button>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useForm, Head } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
  employees: Array,
  bossId: Number
})

const form = useForm({
  title: '',
  description: '',
  employe_id: '',
  boss_id: 1,
  create_date: new Date().toISOString().slice(0, 10),
  deadLine: '',
  importancia: 'media',
  estado: 'pendiente',
  archivos: [],
  assigned_users: [],
})

const showModal = ref(false)
const searchQuery = ref('')
const employees = ref(props.employees || [])

const filteredUsers = computed(() => {
  return employees.value.filter(user =>
    user.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
    user.surname.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
    user.dni.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
    user.roles.some(role => role.name.toLowerCase().includes(searchQuery.value.toLowerCase()))
  )
})

watch(() => props.employees, (newEmployees) => {
  employees.value = newEmployees
}, { immediate: true })

const handleFiles = (e) => {
  form.archivos = Array.from(e.target.files)
}

const submitForm = () => {
  const data = new FormData()
  Object.entries(form.data()).forEach(([key, value]) => {
    if (key === 'archivos') {
      value.forEach(file => data.append('archivos[]', file))
    } else {
      data.append(key, value)
    }
  })

  form.post('/task', {
    forceFormData: true,
    data,
    onSuccess: () => {
      console.log('Tarea creada con éxito')
    },
    onError: () => {
      console.error('Hubo un error al crear la tarea')
    }
  })
}

console.log(props.employees)
</script>

<style scoped>
/* Aquí puedes personalizar el estilo del modal si lo necesitas */
</style>
