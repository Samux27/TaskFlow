<template>
    <AuthenticatedLayout>

        <Head title="Editar Usuario" />

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white p-6 rounded-lg shadow border border-gray-200">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Editar Usuario</h2>

                    <form @submit.prevent="updateUser" class="space-y-6" enctype="multipart/form-data">
                        <!-- Nombre -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Nombre</label>
                            <input v-model="form.name" type="text" id="name"
                                class="mt-1 block w-full border rounded px-3 py-2" />
                            <p class="text-sm text-red-500 mt-1">{{ form.errors.name }}</p>
                        </div>

                        <!-- Apellidos -->
                        <div>
                            <label for="surname" class="block text-sm font-medium text-gray-700">Apellidos</label>
                            <input v-model="form.surname" type="text" id="surname"
                                class="mt-1 block w-full border rounded px-3 py-2" />
                            <p class="text-sm text-red-500 mt-1">{{ form.errors.surname }}</p>
                        </div>

                        <!-- DNI -->
                        <div>
                            <label for="dni" class="block text-sm font-medium text-gray-700">DNI</label>
                            <input v-model="form.dni" @input="form.dni = form.dni.toUpperCase()" type="text" id="dni"
                                class="mt-1 block w-full border rounded px-3 py-2" />
                            <p class="text-sm text-red-500 mt-1">{{ form.errors.dni }}</p>
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Correo
                                electrónico</label>
                            <input v-model="form.email" type="email" id="email"
                                class="mt-1 block w-full border rounded px-3 py-2" />
                            <p class="text-sm text-red-500 mt-1">{{ form.errors.email }}</p>
                        </div>

                      
                        <div class="relative">
                            <label for="password" class="block text-sm font-medium text-gray-700">
                                Nueva contraseña (opcional)

                                <!-- Tooltip -->
                                <span class="group relative inline-block cursor-help ml-1 text-blue-600">
                                    ❓
                                    <div
                                        class="absolute left-5 top-full mt-1 w-64 hidden group-hover:block bg-gray-800 text-white text-xs rounded px-3 py-2 shadow-lg z-50">
                                        Si dejas este campo en blanco, la contraseña actual no se modificará.
                                    </div>
                                </span>
                            </label>

                            <input v-model="form.password" type="password" id="password"
                                class="mt-1 block w-full border rounded px-3 py-2" />
                            <p class="text-sm text-red-500 mt-1">{{ form.errors.password }}</p>
                        </div>


                        <!-- Estado -->
                        <div>
  <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
  <button
    type="button"
    @click="form.is_active = form.is_active === 1 ? 0 : 1"
    :class="form.is_active === 1 ? 'bg-emerald-500 hover:bg-emerald-600' : 'bg-red-500 hover:bg-red-600'"
    class="text-white px-4 py-2 rounded transition"
  >
    {{ form.is_active === 1 ? 'Activo' : 'Inactivo' }}
  </button>
  <p class="text-sm text-red-500 mt-1">{{ form.errors.is_active }}</p>
</div>


                        <!-- Avatar -->
                        <div>
                            <label for="avatar" class="block text-sm font-medium text-gray-700">Avatar</label>
                            <input type="file" id="avatar" @change="handleAvatar"
                                class="mt-1 block w-full text-sm text-gray-700" />
                            <p class="text-sm text-red-500 mt-1">{{ form.errors.avatar }}</p>

                            <div v-if="user.avatar" class="mt-2">
                                <img :src="`/storage/avatars/${user.avatar}`" alt="Avatar actual"
                                    class="w-20 h-20 rounded-full border" />
                            </div>
                        </div>

                        <!-- Rol -->
                        <div>
                            <label for="role" class="block text-sm font-medium text-gray-700">Rol</label>
                            <select v-model="form.role" id="role" class="mt-1 block w-full border rounded px-3 py-2">
                                <option value="admin">Admin</option>
                                <option value="boss">Jefe</option>
                                <option value="employee">Empleado</option>
                            </select>
                            <p class="text-sm text-red-500 mt-1">{{ form.errors.role }}</p>
                        </div>

                        <!-- Botón -->
                        <div class="flex justify-end">
                            <button type="submit"
                                class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 transition">
                                Guardar cambios
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
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const { user } = defineProps({ user: Object })

const form = ref({
    name: user.name || '',
    surname: user.surname || '',
    dni: user.dni || '',
    email: user.email || '',
    password: '',
    is_active: user.is_active,
    role: user.role ,
    avatar: null,
    errors: {}
})

const handleAvatar = (e) => {
    form.value.avatar = e.target.files[0]
}

const updateUser = () => {
    const data = new FormData()
    for (const [key, value] of Object.entries(form.value)) {
        if (value !== null && value !== '') {
            data.append(key, value)
        }
    }

    router.post(`/users/${user.id}`, data, {
        method: 'post',
        forceFormData: true,
        headers: {
            'X-HTTP-Method-Override': 'PUT'
        },
        onError: (errors) => {
            form.value.errors = errors
        },
        onSuccess: () => {
            form.value.errors = {}
            form.value.password = ''
        }
    })
}


</script>