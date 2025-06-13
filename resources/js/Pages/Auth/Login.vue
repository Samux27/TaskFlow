<template>
  <Head title="Login " />
  <div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="bg-white rounded-xl shadow-lg flex w-full max-w-4xl overflow-hidden">
      <!-- Imagen -->
      <div class="w-1/2 bg-blue-500 flex items-center justify-center p-8">
        <img :src="logoUrl" alt="Logo" />
      </div>

      <!-- Formulario -->
      <div class="w-1/2 p-8 relative">
        <h1 class="text-2xl font-bold text-gray-800 mb-4">Bienvenido de nuevo</h1>
        <p class="text-gray-500 mb-6">Inicia sesión para continuar</p>

        <form @submit.prevent="submit">
          <!-- DNI -->
          <div class="mb-4">
            <label for="dni">DNI</label>
            <input
              v-model="form.dni"
              @input="capitalizeDni"
              type="text"
              id="dni"
              autocomplete="username"
              class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400"
            />
          </div>

          <!-- Contraseña -->
          <div class="mb-4">
            <label for="password" class="block text-gray-700 mb-1">Contraseña</label>
            <input
              v-model="form.password"
              type="password"
              id="password"
              autocomplete="current-password"
              class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400"
            />
          </div>

          <!-- Enlace de contraseña olvidada -->
          <div class="mb-4 text-right">
            <button type="button" class="text-sm text-blue-600 hover:underline" @click="showModal = true">
              ¿Has olvidado tu contraseña?
            </button>
          </div>

          <!-- Botón de login -->
          <button
            type="submit"
            class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600 transition"
            :disabled="form.processing"
          >
            <span v-if="form.processing">Iniciando...</span>
            <span v-else>Iniciar sesión</span>
          </button>

          <!-- Debug temporal -->
          <pre class="text-red-500 text-sm mt-1">{{ form.errors.password }}</pre> <br>
          <pre class="text-red-500 text-sm mt-1">{{ form.errors.dni }}</pre>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal -->
  <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md relative">
      <button @click="showModal = false" class="absolute top-2 right-2 text-gray-600 hover:text-gray-800 text-xl">
        &times;
      </button>
      <h2 class="text-lg font-semibold mb-4 text-center text-gray-800">¿Has olvidado tu contraseña?</h2>
      <p class="text-gray-700 text-sm text-center">
        Para recuperar la contraseña ponte en contacto con el administrador del servicio o con el proveedor del mismo.
      </p>
    </div>
  </div>
</template>

<script setup>
import { useForm, Head } from '@inertiajs/vue3';
import { ref } from 'vue';

const logoUrl = '/images/Logo_TaskFlow.jpg';

const form = useForm({
  dni: '',
  password: '',
  remember: false,
});

const showModal = ref(false);

const capitalizeDni = () => {
  form.dni = form.dni.toUpperCase();
};

const submit = () => {
  form.post(route('login'), {
    onError: () => {
      
    },
    onSuccess: () => {
      form.reset('password');
    },
  });
};
</script>
