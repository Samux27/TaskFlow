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

          <form @submit.prevent="createUser" class="space-y-6" enctype="multipart/form-data">
            <!-- Nombre -->
            <div>
              <label for="name" class="block text-sm font-medium text-gray-700">Nombre</label>
              <input v-model="form.name" type="text" id="name" class="mt-1 block w-full border rounded px-3 py-2" />
              <p class="text-sm text-red-500 mt-1">{{ errores.name }}</p>
            </div>

            <!-- Apellidos -->
            <div>
              <label for="surname" class="block text-sm font-medium text-gray-700">Apellidos</label>
              <input v-model="form.surname" type="text" id="surname" class="mt-1 block w-full border rounded px-3 py-2" />
              <p class="text-sm text-red-500 mt-1">{{ errores.surname }}</p>
            </div>

            <!-- DNI -->
            <div>
              <label for="dni" class="block text-sm font-medium text-gray-700">DNI</label>
              <input v-model="form.dni" @input="form.dni = form.dni.toUpperCase()" type="text" id="dni" class="mt-1 block w-full border rounded px-3 py-2" />
              <p class="text-sm text-red-500 mt-1">{{ errores.dni }}</p>
            </div>

            <!-- Email -->
            <div>
              <label for="email" class="block text-sm font-medium text-gray-700">Correo electrónico</label>
              <input v-model="form.email" type="email" id="email" class="mt-1 block w-full border rounded px-3 py-2" />
              <p class="text-sm text-red-500 mt-1">{{ errores.email }}</p>
            </div>

            <!-- Contraseña -->
            <div>
              <label for="password" class="block text-sm font-medium text-gray-700">Contraseña</label>
              <input v-model="form.password" type="password" id="password" class="mt-1 block w-full border rounded px-3 py-2" />
              <p class="text-sm text-red-500 mt-1">{{ errores.password }}</p>
            </div>

            <!-- Confirmación de contraseña -->
            <div>
              <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmar contraseña</label>
              <input v-model="form.password_confirmation" type="password" id="password_confirmation" class="mt-1 block w-full border rounded px-3 py-2" />
              <p class="text-sm text-red-500 mt-1">{{ errores.password_confirmation }}</p>
            </div>

            <!-- Estado -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
              <button type="button" @click="form.is_active = form.is_active === 1 ? 0 : 1"
                :class="form.is_active === 1 ? 'bg-emerald-500 hover:bg-emerald-600' : 'bg-red-500 hover:bg-red-600'"
                class="text-white px-4 py-2 rounded transition">
                {{ form.is_active === 1 ? 'Activo' : 'Inactivo' }}
              </button>
              <p class="text-sm text-red-500 mt-1">{{ errores.is_active }}</p>
            </div>

            <!-- Avatar -->
            <div>
              <label for="avatar" class="block text-sm font-medium text-gray-700">Avatar</label>
              <input type="file" id="avatar" @change="handleAvatar" class="mt-1 block w-full text-sm text-gray-700" />
              <p class="text-sm text-red-500 mt-1">{{ errores.avatar }}</p>
            </div>

            <!-- Rol -->
            <div>
              <label for="role" class="block text-sm font-medium text-gray-700">Rol</label>
              <select v-model="form.role" id="role" class="mt-1 block w-full border rounded px-3 py-2">
                <option value="admin">Admin</option>
                <option value="boss">Jefe</option>
                <option value="employee">Empleado</option>
              </select>
              <p class="text-sm text-red-500 mt-1">{{ errores.role }}</p>
            </div>

            <!-- Botón -->
            <div class="flex justify-end">
              <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 transition">
                Crear Usuario
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { Head, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import { usePage } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const errores = ref({})
const success = usePage().props.flash?.success

const form = ref({
  name: '',
  surname: '',
  email: '',
  dni: '',
  password: '',
  password_confirmation: '',
  is_active: 1,
  role: 'employee',
  avatar: null,
})

const handleAvatar = (e) => {
  form.value.avatar = e.target.files[0]
}

const createUser = () => {
  const data = new FormData()
  for (const [key, value] of Object.entries(form.value)) {
    if (value !== null && value !== '') {
      data.append(key, value)
    }
  }

  router.post('/users', data, {
    forceFormData: true,
    onSuccess: () => {
      form.value = {
        name: '',
        surname: '',
        email: '',
        dni: '',
        password: '',
        password_confirmation: '',
        is_active: 1,
        role: 'employee',
        avatar: null,
      }
      errores.value = {}
    },
    onError: (error) => {
      errores.value = error
    },
  })
}
</script>
