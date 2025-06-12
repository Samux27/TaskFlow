<template>
  <AuthenticatedLayout>
    <Head :title="`Crear tarea para ${empleado.name}`" />

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white p-6 rounded-lg shadow border border-gray-200">
          <h2 class="text-xl font-bold text-gray-800 mb-4">Crear Nueva Tarea</h2>

          <!-- Formulario -->
          <form @submit.prevent="submitForm" enctype="multipart/form-data">
            <!-- Título -->
            <div class="mb-4">
              <label for="title" class="block text-gray-700">Título</label>
              <input v-model="form.title" type="text" id="title"
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 p-2" />
              <div v-if="form.errors.title" class="text-red-500 text-sm mt-1">{{ form.errors.title }}</div>
            </div>

            <!-- Descripción -->
            <div class="mb-4">
              <label for="description" class="block text-gray-700">Descripción</label>
              <textarea v-model="form.description" id="description" rows="4"
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 p-2"></textarea>
              <div v-if="form.errors.description" class="text-red-500 text-sm mt-1">{{ form.errors.description }}</div>
            </div>

            <!-- Empleados asignados -->
            <div class="mb-4">
              <label class="block text-gray-700">Asignar empleados</label>
              <button type="button" @click="showModal = true"
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 p-2 text-center">
                Seleccionar empleados
              </button>
              <div v-if="form.errors.assigned_users" class="text-red-500 text-sm mt-1">{{ form.errors.assigned_users }}</div>
            </div>

            <!-- Importancia -->
            <div class="mb-4">
              <label for="importancia" class="block text-gray-700">Importancia</label>
              <select v-model="form.importancia" id="importancia"
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 p-2">
                <option value="baja">Baja</option>
                <option value="media">Media</option>
                <option value="alta">Alta</option>
              </select>
              <div v-if="form.errors.importancia" class="text-red-500 text-sm mt-1">{{ form.errors.importancia }}</div>
            </div>

            <!-- Estado -->
            <div class="mb-4">
              <label for="estado" class="block text-gray-700">Estado</label>
              <select v-model="form.estado" id="estado"
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 p-2">
                <option value="pendiente">Pendiente</option>
                <option value="en progreso">En progreso</option>
                <option value="bloqueada">Bloqueada</option>
                <option value="finalizada">Finalizada</option>
              </select>
              <div v-if="form.errors.estado" class="text-red-500 text-sm mt-1">{{ form.errors.estado }}</div>
            </div>

            <!-- Fechas -->
            <div class="mb-4">
              <label for="create_date" class="block text-gray-700">Fecha de creación</label>
              <input v-model="form.create_date" type="date" id="create_date"
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 p-2" />
              <div v-if="form.errors.create_date" class="text-red-500 text-sm mt-1">{{ form.errors.create_date }}</div>
            </div>

            <div class="mb-4">
              <label for="deadLine" class="block text-gray-700">Fecha límite</label>
              <input v-model="form.deadLine" type="date" id="deadLine"
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 p-2" />
              <div v-if="form.errors.deadLine" class="text-red-500 text-sm mt-1">{{ form.errors.deadLine }}</div>
            </div>

            <!-- Botones -->
            <div class="flex justify-end gap-4">
              <button type="submit"
                class="bg-indigo-600 text-white px-4 py-2 rounded transition hover:bg-indigo-700"
                :disabled="form.processing">
                Crear Tarea
              </button>
              <Link :href="`/mis-empleados/${empleado.id}/tareas`"
                class="bg-gray-300 text-gray-800 px-4 py-2 rounded transition hover:bg-gray-400">
                Cancelar
              </Link>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Modal UserPicker -->
    <div v-if="showModal" class="fixed inset-0 bg-gray-500 bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-lg">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Seleccionar empleados</h2>

        <input v-model="searchQuery" type="text"
          class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 p-2 mb-4"
          placeholder="Buscar por nombre o DNI" />

        <RecycleScroller :items="filteredUsers" key-field="id" :item-size="96" class="h-96 overflow-y-auto">
          <template #default="{ item: user }">
            <li class="flex items-center gap-4 p-4 border rounded-lg shadow-sm h-24 bg-blue-50">
              <input type="checkbox" :value="user.id" v-model="form.assigned_users" class="h-4 w-4 shrink-0" />
              <div class="flex flex-col truncate">
                <span class="font-semibold truncate w-52">{{ user.name }} {{ user.surname }}</span>
                <span class="text-xs text-gray-600">ID: {{ user.id }}</span>
                <span class="text-xs text-gray-600">DNI: {{ user.dni }}</span>
              </div>
              <span class="ml-auto shrink-0 text-xs px-2 py-1 rounded-full bg-blue-600 text-white">Empleado</span>
            </li>
          </template>
        </RecycleScroller>

        <div class="flex justify-end gap-4 mt-4">
          <button @click="showModal = false"
            class="bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400">Cerrar</button>
          <button @click="showModal = false"
            class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Guardar</button>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { useForm, Head, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { RecycleScroller } from 'vue-virtual-scroller'
import 'vue-virtual-scroller/dist/vue-virtual-scroller.css'

const props = defineProps({
  employees: Array,
  empleado: Object,
})

const form = useForm({
  title: '',
  description: '',
  employe_id: '',
  boss_id: '',
  create_date: new Date().toISOString().slice(0, 10),
  deadLine: '',
  importancia: 'media',
  estado: 'pendiente',
  archivos: [],
  assigned_users: [],
})

onMounted(() => {
  if (!form.assigned_users.includes(props.empleado.id)) {
    form.assigned_users.push(props.empleado.id)
  }
})

const employees = ref(props.employees || [])
const showModal = ref(false)
const searchQuery = ref('')

const filteredUsers = computed(() => {
  return employees.value.filter(user => {
    const query = searchQuery.value.toLowerCase()
    return user.name.toLowerCase().includes(query) ||
      user.surname.toLowerCase().includes(query) ||
      user.dni.toLowerCase().includes(query)
  })
})

const submitForm = () => {
  const data = new FormData()
  Object.entries(form.data()).forEach(([key, value]) => {
    if (key === 'archivos') {
      value.forEach(file => data.append('archivos[]', file))
    } else {
      data.append(key, value)
    }
  })

  form.post(`/mis-empleados/${props.empleado.id}/tareas`, {
    forceFormData: true,
    data,
    onSuccess: () => console.log('✅ Tarea creada'),
    onError: () => console.error('❌ Error al crear la tarea')
  })
}
</script>

<style scoped></style>
