<template>
    <AuthenticatedLayout>
      <Head title="Crear Usuario" />
  
      <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="bg-white p-6 rounded-lg shadow border border-gray-200">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Crear Usuario</h2>
  
            <form @submit.prevent="submitForm">
              <!-- Nombre -->
              <div class="mb-4">
                <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre</label>
                <input
                  type="text"
                  id="nombre"
                  v-model="form.nombre"
                  required
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                />
              </div>
  
              <!-- CIF -->
              <div class="mb-4">
                <label for="cif" class="block text-sm font-medium text-gray-700">CIF</label>
                <input
                  type="text"
                  id="cif"
                  v-model="form.cif"
                  required
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                />
              </div>
  
              <!-- Email -->
              <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input
                  type="email"
                  id="email"
                  v-model="form.email"
                  required
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                />
              </div>
  
              <!-- Avatar -->
              <div class="mb-4">
                <label for="avatar" class="block text-sm font-medium text-gray-700">Avatar</label>
                <input
                  type="file"
                  id="avatar"
                  @change="handleFileUpload"
                  class="mt-1 block w-full text-sm text-gray-700 border border-gray-300 rounded-md"
                />
                <p v-if="form.avatar" class="mt-2 text-sm text-gray-600">Archivo seleccionado: {{ form.avatar.name }}</p>
              </div>
  
              <!-- Contraseña -->
              <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700">Contraseña (opcional)</label>
                <input
                  type="password"
                  id="password"
                  v-model="form.password"
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                />
              </div>
  
              <!-- Botón de Enviar -->
              <div class="flex justify-end">
                <button
                  type="submit"
                  class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md transition"
                >
                  Crear Usuario
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </AuthenticatedLayout>
  </template>
  
  <script>
  export default {
    data() {
      return {
        form: {
          nombre: '',
          cif: '',
          email: '',
          avatar: null,
          password: ''
        }
      };
    },
    methods: {
      // Función para manejar la carga de la foto
      handleFileUpload(event) {
        this.form.avatar = event.target.files[0];
      },
  
      // Función para enviar el formulario
      async submitForm() {
        const formData = new FormData();
        formData.append('nombre', this.form.nombre);
        formData.append('cif', this.form.cif);
        formData.append('email', this.form.email);
  
        if (this.form.avatar) {
          formData.append('avatar', this.form.avatar);
        }
  
        if (this.form.password) {
          formData.append('password', this.form.password);
        }
  
        try {
          // Realizar una petición POST al backend para crear el usuario
          await this.$inertia.post('/user', formData);  
          this.$inertia.visit('/user'); // Redirigir a la lista de usuarios
        } catch (error) {
          console.error('Error al crear el usuario:', error);
        }
      }
    }
  };
  </script>
  