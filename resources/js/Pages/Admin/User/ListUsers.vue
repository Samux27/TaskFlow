<template>
  <AuthenticatedLayout>
    <Head title="Usuarios" />

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white p-6 rounded-lg shadow border border-gray-200">
          <h2 class="text-xl font-bold text-gray-800 mb-4">Usuarios Registrados</h2>

          <!-- Botón -->
          <div class="mb-4 flex justify-between items-center">
            <Link href="/users/create" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded transition">
              Nuevo Usuario
            </Link>
          </div>

          <!-- Tabla con DataTable -->
          <table ref="userTable" class="display nowrap w-full border border-gray-300" style="width:100%">
            <thead>
              <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Estado</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="user in users" :key="user.id" @click="openUserModal(user)" class="cursor-pointer hover:bg-gray-100">
                <td>{{ user.id }}</td>
                <td class="text-indigo-700 text-black">{{ user.name }}</td>
                <td>{{ user.email }}</td>
                <td>
                  <span :class="user.is_active ? 'bg-emerald-500' : 'bg-red-500'" class="px-3 py-1 text-sm rounded text-white">
                    {{ user.is_active ? 'Activo' : 'Inactivo' }}
                  </span>
                </td>
                <td>
                  <Link :href="`/users/${user.id}/edit`" @click.stop class="text-indigo-600 hover:underline">Editar</Link> |
                  <button @click.stop="confirmDelete(user.id)" class="text-rose-600 hover:underline ml-2">Eliminar</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Modal Confirmar Eliminación -->
      <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded shadow-lg border border-gray-300 w-full max-w-md">
          <h3 class="text-lg font-semibold text-gray-800">¿Eliminar usuario?</h3>
          <p class="text-gray-600 mt-2">Esta acción eliminará todas las tareas relacionadas y no se puede deshacer.</p>

          <div class="mt-4 flex gap-4 justify-center">
            <button @click="deleteUser" class="bg-rose-600 hover:bg-rose-700 text-white px-4 py-2 rounded">Eliminar</button>
            <button @click="showModal = false" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded">Cancelar</button>
          </div>
        </div>
      </div>

      <!-- Modal Detalles de Usuario -->
      <div v-if="showUserModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded shadow-lg border border-gray-300 w-full max-w-3xl flex gap-6 relative">
          <!-- Avatar -->
          <div class="w-1/3 flex items-center justify-center">
            <img
              v-if="selectedUser.avatar"
              :src="`/storage/avatars/${$page.props.auth.user.avatar}`" 
              alt="Avatar"
              class="w-32 h-32 rounded-full border object-cover"
            />
            <div v-else class="w-32 h-32 rounded-full border bg-gray-100 flex items-center justify-center text-4xl text-gray-500">
              ❓
            </div>
          </div>

          <!-- Detalles -->
          <div class="w-2/3 space-y-2 text-sm text-gray-800">
            <p><strong>ID:</strong> {{ selectedUser.id }}</p>
            <p><strong>Nombre:</strong> {{ selectedUser.name }}</p>
            <p><strong>Apellidos:</strong> {{ selectedUser.surname }}</p>
            <p><strong>DNI:</strong> {{ selectedUser.dni }}</p>
            <p><strong>Email:</strong> {{ selectedUser.email }}</p>
            <p><strong>Rol:</strong> {{ selectedUser.role }}</p>
            <p><strong>Estado:</strong> {{ selectedUser.is_active ? 'Activo' : 'Inactivo' }}</p>
          </div>

          <!-- Cerrar -->
          <button @click="showUserModal = false" class="absolute top-4 right-4 text-gray-600 hover:text-black text-xl">✖</button>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { onMounted, ref } from 'vue'
import { useToast } from 'vue-toastification'

import 'datatables.net-dt/css/dataTables.dataTables.min.css'
import 'datatables.net-responsive-dt/css/responsive.dataTables.min.css'
import $ from 'jquery'
import 'datatables.net'
import 'datatables.net-responsive'

const { users } = defineProps({ users: Array })
const toast = useToast()
const flash = usePage().props.flash

if (flash?.success) {
  toast.success(flash.success)
}

// refs
const showModal = ref(false)
const showUserModal = ref(false)
const userIdToDelete = ref(null)
const userTable = ref(null)
const selectedUser = ref({})

// abrir modal de confirmación
const confirmDelete = (id) => {
  userIdToDelete.value = id
  showModal.value = true
}

// eliminar usuario
const deleteUser = () => {
  router.delete(`/users/${userIdToDelete.value}`, {
    onSuccess: () => {
      showModal.value = false
      toast.success(flash.success)
      flash.success = null
    }
  })
}

// abrir modal de usuario
const openUserModal = (user) => {
  selectedUser.value = user
  showUserModal.value = true
}

onMounted(() => {
  $(userTable.value).DataTable({
    responsive: true,
    info: false,
    lengthChange: false,
    pageLength: 10,
    language: {
      search: "Buscar:",
      zeroRecords: "No se encontraron usuarios",
      paginate: {
        previous: "Anterior",
        next: "Siguiente"
      }
    }
  })
})
</script>
