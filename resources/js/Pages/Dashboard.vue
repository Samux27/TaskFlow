<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
defineProps({ usuarios: Array });

const { roles, permissions } = usePage().props.auth;
</script>

<template>
    <Head title="Dashboard" />
    
    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Dashboard
            </h2>
            
        </template>
        
        

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                >
                    <div class="p-6 text-gray-900">
                        
                        <div v-if="roles.includes('employee')"> Eres un empleado</div>
                        <div v-if="roles.includes('admin')"> Eres admin</div>
                        <div v-if="roles.includes('boss')"> Eres admin</div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
