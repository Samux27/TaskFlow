<template>
    <AuthenticatedLayout>
      <Head title="Crear Tarea" />
  
      <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="bg-white p-6 rounded-lg shadow border border-gray-200">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Crear Nueva Tarea</h2>
  
            <!-- Formulario -->
            <form @submit.prevent="submitForm">
              <div class="mb-4">
                <label for="title" class="block text-gray-700">Título</label>
                <input 
                  v-model="form.title" 
                  type="text" 
                  id="title" 
                  class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 p-2" 
                  required 
                />
              </div>
  
              <div class="mb-4">
                <label for="description" class="block text-gray-700">Descripción</label>
                <textarea
                  v-model="form.description"
                  id="description"
                  rows="4"
                  class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 p-2"
                  required
                ></textarea>
              </div>
  
              <div class="mb-4">
                <label for="importancia" class="block text-gray-700">Importancia</label>
                <select
                  v-model="form.importancia"
                  id="importancia"
                  class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 p-2"
                  required
                >
                  <option value="baja">Baja</option>
                  <option value="media">Media</option>
                  <option value="alta">Alta</option>
                </select>
              </div>
  
              <div class="mb-4">
                <label for="estado" class="block text-gray-700">Estado</label>
                <select
                  v-model="form.estado"
                  id="estado"
                  class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 p-2"
                  required
                >
                  <option value="pendiente">Pendiente</option>
                  <option value="en progreso">En progreso</option>
                  <option value="bloqueada">Bloqueada</option>
                  <option value="finalizada">Finalizada</option>
                </select>
              </div>
  
              <div class="mb-4">
                <label for="deadLine" class="block text-gray-700">Fecha límite</label>
                <input 
                  v-model="form.deadLine" 
                  type="date" 
                  id="deadLine"
                  class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 p-2"
                />
              </div>
  
              <div class="flex justify-end gap-4">
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded transition hover:bg-indigo-700">
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
    </AuthenticatedLayout>
  </template>
  
  <script setup>
  import { ref } from 'vue'
  import { useForm } from '@inertiajs/vue3'
  import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
  import { Head, Link } from '@inertiajs/vue3'
  
  // Formulario reactivo
  const form = useForm({
    title: '',
    description: '',
    importancia: 'media',
    estado: 'pendiente',
    deadLine: '',
  })
  
  // Función para manejar el envío del formulario
  const submitForm = () => {
    form.post('/task', {
      onSuccess: () => {
        // Redirigir al listado de tareas o mostrar un mensaje de éxito
        console.log('Tarea creada con éxito')
      },
      onError: () => {
        // Manejo de errores si algo falla en el backend
        console.error('Hubo un error al crear la tarea')
      },
    })
  }
  </script>
  
  <style scoped>
  /* Puedes personalizar los estilos aquí si es necesario */
  </style>
  