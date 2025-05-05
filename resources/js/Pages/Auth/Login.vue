<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="bg-white rounded-xl shadow-lg flex w-full max-w-4xl overflow-hidden">
      <!-- Imagen -->
      <div class="w-1/2 bg-blue-500 flex items-center justify-center p-8">
        <img :src="logoUrl" alt="Logo" />
      </div>

      <!-- Formulario -->
      <div class="w-1/2 p-8">
        <h1 class="text-2xl font-bold text-gray-800 mb-4">Bienvenido de nuevo</h1>
        <p class="text-gray-500 mb-6">Inicia sesión para continuar</p>

        <form @submit.prevent="submit">
          <!-- DNI -->
          <div class="mb-4">
            <label for="dni">DNI</label>
            <input v-model="form.dni" @input="capitalizeDni" type="text" id="dni" autocomplete="username"
            class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400" />
          </div>

          <!-- Contraseña -->
          <div class="mb-4">
            <label for="password" class="block text-gray-700 mb-1">Contraseña</label>
            <input v-model="form.password" type="password" id="password" autocomplete="current-password"
              class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400" />
          </div>

          <!-- Recordarme -->
          <div class="mb-4 flex justify-between items-center">
            <label class="flex items-center text-sm text-gray-600">
              <input type="checkbox" v-model="form.remember" class="mr-2" />
              Recuérdame
            </label>
          </div>

          <!-- Botón -->
          <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600 transition"
            :disabled="form.processing">
            <span v-if="form.processing">Iniciando...</span>
            <span v-else>Iniciar sesión</span>
          </button>

          <!-- Debug temporal -->
          <pre class="text-red-500 text-sm mt-1">{{ form.errors.password }}{{ form.errors.email }}</pre>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';

const logoUrl = '/images/Logo TaskFlow.jpg';

const form = useForm({
  dni: '',
  password: '',
  remember: false,
});

// Método para capitalizar el DNI
const capitalizeDni = () => {
  form.dni = form.dni.toUpperCase();
};

const submit = () => {
  form.post(route('login'), {
    onError: () => {
      console.log(form);
      form.errors.dni = "Correo electrónico o contraseña incorrectos";
      form.errors.password = "La contraseña es Obligatoria";
    },
    onSuccess: () => {
      form.reset('password');
    },
  });
};
</script>
