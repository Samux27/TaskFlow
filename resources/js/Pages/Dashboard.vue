<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';

const { user, roles } = usePage().props.auth;
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-gray-800 leading-tight">
                Bienvenido al Panel de Control
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 space-y-4">

                        <h3 class="text-2xl font-bold text-indigo-700">
                            ¡Hola, {{ user.name }}!
                        </h3>

                        <p class="text-gray-600">
                            Este es tu espacio de trabajo personalizado. Aquí podrás gestionar tareas,
                            revisar tus asignaciones y mantenerte al día según tu rol dentro de la empresa.
                        </p>

                        <div class="mt-6">
                            <div v-if="roles.includes('admin')" class="bg-indigo-100 p-4 rounded border-l-4 border-indigo-500">
                                <p class="font-medium text-indigo-800">Eres administrador.</p>
                                <p class="text-sm text-gray-700">Tienes acceso completo al sistema, incluyendo usuarios, roles y logs.</p>
                            </div>

                            <div v-if="roles.includes('boss')" class="bg-yellow-100 p-4 rounded border-l-4 border-yellow-500">
                                <p class="font-medium text-yellow-800">Eres jefe de equipo.</p>
                                <p class="text-sm text-gray-700">Puedes crear tareas, asignarlas a tus empleados y supervisar su avance.</p>
                            </div>

                            <div v-if="roles.includes('employee')" class="bg-green-100 p-4 rounded border-l-4 border-green-500">
                                <p class="font-medium text-green-800">Eres empleado.</p>
                                <p class="text-sm text-gray-700">Consulta tus tareas asignadas, actualiza su estado y comunícate con tu jefe.</p>
                            </div>
                        </div>

                        <p class="text-sm text-gray-500 mt-8 italic">
                            Si necesitas ayuda, contacta con el administrador .
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
