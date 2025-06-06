<template>
    <div
      v-if="show"
      class="fixed inset-0 z-50 flex items-center justify-center bg-gray-500 bg-opacity-50"
    >
      <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-lg">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Seleccionar Usuarios</h2>
  
        <input
          v-model="search"
          type="text"
          placeholder="Buscar por nombre, rol o DNI"
          class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 p-2 mb-4"
        />
  
        <RecycleScroller
          :items="filtered"
          key-field="id"
          :item-size="80"
          class="h-96 overflow-y-auto"
        >
          <template #default="{ item: user }">
            <label
              :for="`user-${user.id}`"
              class="flex items-center gap-4 p-4 border rounded-lg shadow-sm h-20 cursor-pointer"
              :class="rowClass(user)"
            >
              <input
                :id="`user-${user.id}`"
                type="checkbox"
                :value="user.id"
                v-model="localSelected"
                class="h-4 w-4 shrink-0"
              />
  
              <div class="flex flex-col truncate">
                <span class="font-semibold truncate w-52">
                  {{ user.name }} {{ user.surname }}
                </span>
                <span class="text-xs text-gray-600">ID: {{ user.id }}</span>
                <span class="text-xs text-gray-600">DNI: {{ user.dni }}</span>
              </div>
  
              <span
                class="ml-auto shrink-0 text-xs px-2 py-1 rounded-full whitespace-nowrap"
                :class="badgeClass(user)"
              >
                {{ user.roles.map(r => r.name).join(', ') }}
              </span>
            </label>
          </template>
        </RecycleScroller>
  
        <div class="flex justify-end gap-4 mt-4">
          <button
            @click="emit('update:show', false)"
            class="bg-gray-300 text-gray-800 px-4 py-2 rounded transition hover:bg-gray-400"
          >
            Cerrar
          </button>
          <button
            @click="handleSave"
            class="bg-indigo-600 text-white px-4 py-2 rounded transition hover:bg-indigo-700"
          >
            Guardar
          </button>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, computed, watch } from 'vue'
  import { RecycleScroller } from 'vue-virtual-scroller'
  import 'vue-virtual-scroller/dist/vue-virtual-scroller.css'
  
  const props = defineProps({
    show: Boolean,
    selected: { type: Array, default: () => [] },
    users: { type: Array, default: () => [] },
  })
  
  const emit = defineEmits(['update:show', 'update:selected'])
  
  const search = ref('')
  const localSelected = ref([...props.selected])
  
  // Sincroniza con v-model:selected
  watch(
    () => props.selected,
    (v) => (localSelected.value = [...v])
  )
  
  watch(localSelected, (v) => emit('update:selected', v))
  
  const filtered = computed(() => {
    const q = search.value.toLowerCase()
    return props.users.filter((u) =>
      [u.name, u.surname, u.dni]
        .map((v) => (v ?? '').toLowerCase())
        .some((txt) => txt.includes(q)) ||
      u.roles.some((r) => (r.name ?? '').toLowerCase().includes(q))
    )
  })
  
  const rowClass = (u) => ({
    'bg-blue-50': u.roles.some((r) => r.name === 'employee'),
    'bg-green-50': u.roles.some((r) => r.name === 'boss'),
    'bg-red-50': u.roles.some((r) => r.name === 'admin'),
  })
  
  const badgeClass = (u) => ({
    'bg-blue-500 text-white': u.roles.some((r) => r.name === 'employee'),
    'bg-green-500 text-white': u.roles.some((r) => r.name === 'boss'),
    'bg-red-500 text-white': u.roles.some((r) => r.name === 'admin'),
  })
  
  const handleSave = () => {
    emit('update:show', false)
  }
  </script>
  
  <style scoped>
  /* Sin estilos adicionales */
  </style>
  