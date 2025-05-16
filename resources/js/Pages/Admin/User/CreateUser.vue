<template>
  <AuthenticatedLayout>
    <Head title="Crear Usuario" />

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white p-6 rounded-lg shadow border border-gray-200">
          <h2 class="text-xl font-bold text-gray-800 mb-4">Crear Usuario</h2>

          <div v-if="success" class="bg-emerald-100 text-emerald-700 p-3 rounded mb-4 border border-emerald-300">
            {{ success }}
          </div>

          <form @submit.prevent="createUser" class="space-y-6">
            <!-- Nombre -->
            <div>
              <label for="name" class="block text-sm font-medium text-gray-700">Nombre</label>
              <input
                type="text"
                id="name"
                v-model="form.name"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              />
              <span v-if="errores.name" class="text-sm text-red-500">{{ errores.name }}</span>
            </div>

            <!-- Apellido -->
            <div>
              <label for="surname" class="block text-sm font-medium text-gray-700">Apellido</label>
              <input
                type="text"
                id="surname"
                v-model="form.surname"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              />
              <span v-if="errores.surname" class="text-sm text-red-500">{{ errores.surname }}</span>
            </div>

            <!-- Correo Electrónico -->
            <div>
              <label for="email" class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
              <input
                type="email"
                id="email"
                v-model="form.email"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              />
              <span v-if="errores.email" class="text-sm text-red-500">{{ errores.email }}</span>
            </div>

            <!-- DNI -->
            <div>
              <label for="dni" class="block text-sm font-medium text-gray-700">DNI</label>
              <input
                type="text"
                id="dni"
                v-model="form.dni"
                @input="form.dni = $event.target.value.toUpperCase()"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              />
              <span v-if="errores.dni" class="text-sm text-red-500">{{ errores.dni }}</span>
            </div>

            <!-- Contraseña -->
            <div>
              <label for="password" class="block text-sm font-medium text-gray-700">Contraseña</label>
              <input
                type="password"
                id="password"
                v-model="form.password"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              />
              <span v-if="errores.password" class="text-sm text-red-500">{{ errores.password }}</span>
            </div>

            <!-- Confirmar Contraseña -->
            <div>
              <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmar Contraseña</label>
              <input
                type="password"
                id="password_confirmation"
                v-model="form.password_confirmation"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              />
              <span v-if="errores.password_confirmation" class="text-sm text-red-500">{{ errores.password_confirmation }}</span>
            </div>

            <!-- Selección de Rol -->
            <div>
              <label for="role" class="block text-sm font-medium text-gray-700">Rol</label>
              <select
                id="role"
                v-model="form.role"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              >
                <option value="admin">Administrador</option>
                <option value="boss">Jefe</option>
                <option value="employee">Empleado</option>
              </select>
              <span v-if="errores.role" class="text-sm text-red-500">{{ errores.role }}</span>
            </div>

            <!-- Botón -->
            <div class="flex justify-end">
              <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-md transition">Crear Usuario</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import { usePage } from '@inertiajs/vue3'

const errores = ref({}) // NUEVO

const form = ref({
  name: '',
  surname: '',
  email: '',
  dni: '',
  password: '',
  password_confirmation: '',
  is_active: true,
  role: 'employee',
})

const success = usePage().props.flash?.success

const createUser = () => {
  router.post('/users', form.value, {
    onSuccess: () => {
      form.value = {
        name: '',
        surname: '',
        email: '',
        dni: '',
        password: '',
        password_confirmation: '',
        is_active: true,
        role: 'employee',
      }
      errores.value = {} // Limpiar errores si todo va bien
    },
    onError: (error) => {
      errores.value = error
      
    },
  })
}
</script>
