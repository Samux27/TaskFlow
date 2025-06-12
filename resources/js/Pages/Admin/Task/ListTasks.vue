<template>
    <AuthenticatedLayout>

        <Head title="Listado de Tareas" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white p-6 rounded-lg shadow border border-gray-200">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Tareas Registradas</h2>

                    <!-- Botón Nueva Tarea -->
                    <div class="mb-4 flex justify-between items-center">
                        <Link :href="route('task.create')"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded transition">
                        Nueva Tarea
                        </Link>
                    </div>

                    <!-- Tabla -->
                    <table ref="tableRef" class="display nowrap w-full border border-gray-300" style="width: 100%">
                        <thead>
                            <tr class="bg-gray-100 text-gray-700">
                                <th>ID</th>
                                <th>Título</th>
                                <th>Descripción</th>
                                <th>Estado</th>
                                <th>Importancia</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="task in tasks" :key="task.id" @click="openTaskModal(task)"
                                class="cursor-pointer hover:bg-gray-100">
                                <td>{{ task.id }}</td>
                                <td>{{ task.title }}</td>
                                <td>{{ task.description }}</td>
                                <td>
                                    <span class="px-3 py-1 text-sm rounded text-white" :class="{
                                        'bg-emerald-500': estadoNormalizado(task.estado) === 'finalizada',
                                        'bg-gray-500': estadoNormalizado(task.estado) === 'pendiente',
                                        'bg-yellow-500': estadoNormalizado(task.estado) === 'en progreso',
                                        'bg-red-500': !['finalizada', 'pendiente'].includes(
                                            estadoNormalizado(task.estado)
                                        ),
                                    }">
                                        {{
                                            task.estado.charAt(0).toUpperCase() + task.estado.slice(1)
                                        }}
                                    </span>
                                </td>
                                <td>
                                    <span class="px-3 py-1 text-sm rounded text-white" :class="{
                                        'bg-red-500': task.importancia === 'alta',
                                        'bg-green-500': task.importancia === 'baja',
                                        'bg-yellow-500':
                                            task.importancia !== 'baja' && task.importancia !== 'alta',
                                    }">
                                        {{
                                            task.importancia.charAt(0).toUpperCase() +
                                            task.importancia.slice(1)
                                        }}
                                    </span>
                                </td>
                                <td @click.stop>
                                    <a href="#" @click.prevent="router.visit(`/task/${task.id}`)"
                                        class="text-green-600 hover:underline">Ver</a>

                                    
                                    <span v-if="userRole === 'admin' ">
                                        |
                                    <Link :href="`/task/${task.id}/edit`" class="text-indigo-600 hover:underline"
                                        @click.stop>Editar</Link>
                                    |
                                    <button @click.stop="confirmDelete(task.id)"
                                        class="text-rose-600 hover:underline ml-2">
                                        Eliminar
                                    </button>
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Modal Confirmar Eliminación -->
            <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white p-6 rounded shadow-lg border border-gray-300 w-full max-w-md">
                    <h3 class="text-lg font-semibold text-gray-800">¿Eliminar tarea?</h3>
                    <p class="text-gray-600 mt-2">Esta acción no se puede deshacer.</p>

                    <div class="mt-4 flex gap-4 justify-center">
                        <button @click="deleteTask" class="bg-rose-600 hover:bg-rose-700 text-white px-4 py-2 rounded">
                            Eliminar
                        </button>
                        <button @click="showModal = false"
                            class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>

            <button @click="exportTasks" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
  📥 Exportar a Excel
</button>


        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { ref, onMounted, watch } from 'vue'
import $ from 'jquery'
import 'datatables.net'
import 'datatables.net-responsive'
import 'datatables.net-dt/css/dataTables.dataTables.min.css'
import 'datatables.net-responsive-dt/css/responsive.dataTables.min.css'
import { useToast } from 'vue-toastification'

const { tasks } = defineProps({ 
    tasks: Array, 
    userRole: String,
})
const exportTasks = () => {
  window.open('/export-tasks', '_blank');
}
// Flash & toast
const flash = usePage().props.flash
const toast = useToast()
if (flash?.success) {
    toast.success(flash.success)
}

// --- Tabla & búsqueda
const tableRef = ref(null)
const searchTerm = ref('')

// --- Eliminación de tarea
const showModal = ref(false)
const taskIdToDelete = ref(null)

const confirmDelete = (id) => {
    taskIdToDelete.value = id
    showModal.value = true
}

const deleteTask = () => {
    router.delete(`/task/${taskIdToDelete.value}`, {
        onSuccess: () => {
            showModal.value = false
            toast.success('Tarea eliminada correctamente')
        },
        onError: () => {
            toast.error('Error al eliminar la tarea')
        },
    })
}

// --- Modal de detalles
const showTaskModal = ref(false)
const selectedTask = ref({})

const openTaskModal = (task) => {
    selectedTask.value = task
    showTaskModal.value = true
}

// --- Helpers
const estadoNormalizado = (estado) => {
    return estado?.toString().trim().toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '')
}

const formatDate = (date) => {
    return new Date(date).toLocaleString('es-ES', {
        dateStyle: 'medium',
        timeStyle: 'short',
    })
}

// --- DataTable init
onMounted(() => {
    const table = $(tableRef.value).DataTable({
        order: [[0, 'desc']],
        paging: true,
        lengthChange: false,
        info: false,
        responsive: true,
        language: {
            search: 'Buscar:',
            zeroRecords: 'No se encontraron tareas',
            paginate: {
                first: 'Primero',
                last: 'Último',
                next: 'Siguiente',
                previous: 'Anterior',
            },
        },
    })

    watch(searchTerm, (newValue) => {
        table.search(newValue).draw()
    })
})
</script>