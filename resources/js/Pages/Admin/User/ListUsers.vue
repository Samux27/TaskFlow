<template>
    <AuthenticatedLayout>
      <Head title="Usuarios" />
  
      <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="bg-white p-6 rounded-lg shadow border border-gray-200">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Usuarios Registrados</h2>
  
            <div v-if="success" class="bg-emerald-100 text-emerald-700 p-3 rounded mb-4 border border-emerald-300">
              {{ success }}
            </div>
  
            <!-- Botón -->
            <div class="mb-4 flex justify-between items-center">
              <Link href="/user/create" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded transition">
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
                <tr v-for="user in users" :key="user.id">
                  <td>{{ user.id }}</td>
                  <td>{{ user.name }}</td>
                  <td>{{ user.email }}</td>
                  <td>
                    <span :class="user.is_active ? 'bg-emerald-500' : 'bg-red-500'" class="px-3 py-1 text-sm rounded text-white">
                      {{ user.is_active ? 'Activo' : 'Inactivo' }}
                    </span>
                  </td>
                  <td>
                    <Link :href="`/user/${user.id}/edit`" class="text-indigo-600 hover:underline">Editar</Link> |
                    <button @click="confirmDelete(user.id)" class="text-rose-600 hover:underline ml-2">Eliminar</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
  
        <!-- Modal -->
        <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
          <div class="bg-white p-6 rounded shadow-lg border border-gray-300 w-full max-w-md">
            <h3 class="text-lg font-semibold text-gray-800">¿Eliminar usuario?</h3>
            <p class="text-gray-600 mt-2">Esta acción no se puede deshacer.</p>
  
            <div class="mt-4 flex gap-4 justify-center">
              <button @click="deleteUser" class="bg-rose-600 hover:bg-rose-700 text-white px-4 py-2 rounded">Eliminar</button>
              <button @click="showModal = false" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded">Cancelar</button>
            </div>
          </div>
        </div>
      </div>
    </AuthenticatedLayout>
  </template>
  
  <script setup>
  import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
  import { Head, Link, router, usePage } from '@inertiajs/vue3'
  import { onMounted, ref } from 'vue'
  
  // Estilos de DataTables
  import 'datatables.net-dt/css/dataTables.dataTables.min.css'
  import 'datatables.net-responsive-dt/css/responsive.dataTables.min.css'
  
  // JS de DataTables
  import $ from 'jquery'
  import 'datatables.net'
  import 'datatables.net-responsive'
  
  const { users } = defineProps({ users: Array })
  
  const showModal = ref(false)
  const userIdToDelete = ref(null)
  const success = usePage().props.flash?.success
  const userTable = ref(null)
  
  const confirmDelete = (id) => {
    userIdToDelete.value = id
    showModal.value = true
  }
  
  const deleteUser = () => {
    router.delete(`/user/${userIdToDelete.value}`, {
      onSuccess: () => {
        showModal.value = false
      }
    })
  }
  
  onMounted(() => {
    $(userTable.value).DataTable({
      responsive: true,
      info: false,
      lengthChange: false,
      pageLength: 10,
      

      responsive: true,
      language: {
        zeroRecords: "No se encontraron usuarios",
       
      }
    })
  })
  </script>
  