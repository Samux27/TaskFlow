<template>
  <AuthenticatedLayout>
    <Head title="Permisos de Jefes" />

    <!-- ░░░ Listado principal ░░░ -->
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white p-6 rounded-lg shadow border border-gray-200">
          <h2 class="text-xl font-bold text-gray-800 mb-4">
            Jefes con permiso para asignar tareas
          </h2>

          <!-- Tabla de jefes -->
          <table
            ref="bossTable"
            class="display nowrap w-full border border-gray-300"
            style="width: 100%"
          >
            <thead>
              <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Asignados</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="boss in bosses"
                :key="boss.id"
                @click="openPicker(boss)"
                class="cursor-pointer hover:bg-gray-100"
              >
                <td>{{ boss.id }}</td>
                <td class="text-indigo-700">{{ boss.name }}</td>
                <td>{{ boss.employee_count }}</td>
                <td @click.stop>
                  <button
                    class="text-indigo-600 hover:underline"
                    @click.stop="openPicker(boss)"
                  >
                    Configurar
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ░░░ Modal de selección de empleados ░░░ -->
    <div
      v-if="showPicker"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
    >
      <div
        class="bg-white p-6 rounded-lg shadow-xl border border-gray-300 w-full max-w-3xl relative"
      >
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
          Empleados que puede gestionar
          <span class="text-indigo-700">{{ currentBoss?.name }}</span>
        </h3>

        <!-- 🔍 Buscador -->
        <input
          v-model="search"
          type="text"
          placeholder="Buscar por Nombre o DNI"
          class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 p-2 mb-4"
        />

        <!-- 📜 Lista de empleados -->
        <div class="h-[28rem] overflow-y-auto space-y-2 pr-1">
          <label
            v-for="emp in filteredEmployees"
            :key="emp.id"
            :for="`emp-${emp.id}`"
            class="flex items-center gap-4 p-4 border rounded-lg shadow-sm cursor-pointer"
            :class="rowClass(emp)"
          >
            <input
              :id="`emp-${emp.id}`"
              type="checkbox"
              :value="emp.id"
              v-model="selectedEmployeeIds"
              class="h-4 w-4 shrink-0"
            />

            <div class="flex flex-col truncate">
              <span class="font-semibold truncate w-52">
                {{ emp.name }} {{ emp.surname }}
              </span>
              <span class="text-xs text-gray-600">ID: {{ emp.id }}</span>
              <span class="text-xs text-gray-600">DNI: {{ emp.dni }}</span>
            </div>

            <span
              class="ml-auto shrink-0 text-xs px-2 py-1 rounded-full whitespace-nowrap"
              :class="badgeClass(emp)"
            >
              {{ emp.role === ROLE_EMP ? 'employee' : emp.role }}
            </span>
          </label>
        </div>

        <!-- 🚦 Acciones -->
        <div class="mt-6 flex gap-4 justify-end">
          <button
            @click="save"
            class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded"
          >
            Guardar
          </button>
          <button
            @click="closePicker"
            class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-5 py-2 rounded"
          >
            Cancelar
          </button>
        </div>

        <button
          @click="closePicker"
          class="absolute top-4 right-4 text-gray-600 hover:text-black text-xl"
        >
          ✖
        </button>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
/* ─────────────────────────────────────────────────────────── */
/*  Importaciones                                             */
/* ─────────────────────────────────────────────────────────── */
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, router }    from '@inertiajs/vue3'
import { ref, onMounted, nextTick, computed, watch } from 'vue'
import $ from 'jquery'
import 'datatables.net'
import 'datatables.net-responsive'
import 'datatables.net-dt/css/dataTables.dataTables.min.css'
import 'datatables.net-responsive-dt/css/responsive.dataTables.min.css'
import { useToast } from 'vue-toastification'

/* ─────────────────────────────────────────────────────────── */
/*  Props desde el back‑end                                   */
/* ─────────────────────────────────────────────────────────── */
const props = defineProps({
  users:   { type: Array, default: () => [] },
  mapping: { type: Array, default: () => [] },
})

/* ─────────────────────────────────────────────────────────── */
/*  Constantes de rol                                         */
/* ─────────────────────────────────────────────────────────── */
const ROLE_BOSS = 'boss'
const ROLE_EMP  = 'employee'

/* ─────────────────────────────────────────────────────────── */
/*  1️⃣  Limpiar y clasificar usuarios                         */
/* ─────────────────────────────────────────────────────────── */
const validUsers = computed(() =>
  props.users.filter(
    (u) => u && !Array.isArray(u) && u.id &&
      (u.role === ROLE_BOSS || u.role === ROLE_EMP),
  ),
)

const bosses = computed(() =>
  validUsers.value
    .filter((u) => u.role === ROLE_BOSS)
    .map((b) => {
      const rec = props.mapping.find((m) => m.boss_id === b.id)
      return {
        ...b,
        employee_count: rec ? rec.employee_ids.length : 0,
      }
    }),
)

const employees = computed(() =>
  validUsers.value.filter((u) => u.role === ROLE_EMP),
)

/* ─────────────────────────────────────────────────────────── */
/*  2️⃣  DataTable                                             */
/* ─────────────────────────────────────────────────────────── */
const bossTable = ref(null)
let dtInstance  = null

function initTable() {
  if (!bossTable.value) return
  if (dtInstance) {
    dtInstance.destroy()
    dtInstance = null
  }
  dtInstance = $(bossTable.value).DataTable({
    responsive:  true,
    lengthChange:false,
    info:        false,
    pageLength:  10,
    language: {
      search: 'Buscar:',
      zeroRecords: 'No se encontraron jefes',
      paginate: { previous: 'Anterior', next: 'Siguiente' },
    },
  })
}

onMounted(async () => {
  await nextTick()
  initTable()
})

watch(bosses, () => nextTick(initTable))

/* ─────────────────────────────────────────────────────────── */
/*  3️⃣  Modal y selección                                     */
/* ─────────────────────────────────────────────────────────── */
const showPicker          = ref(false)
const currentBoss         = ref(null)
const selectedEmployeeIds = ref([])
const toast               = useToast()

/* Buscador dentro del modal */
const search = ref('')
const filteredEmployees = computed(() => {
  const q = search.value.toLowerCase()
  return employees.value.filter((e) =>
    [e.name, e.surname, e.dni]
      .map((v) => (v ?? '').toLowerCase())
      .some((txt) => txt.includes(q)),
  )
})

function rowClass(u) {
  return {
    'bg-blue-50': true,
  }
}

function badgeClass(u) {
  return 'bg-blue-500 text-white'
}

function openPicker(boss) {
  currentBoss.value = boss
  const record = props.mapping.find((m) => m.boss_id === boss.id)
  selectedEmployeeIds.value = record ? [...record.employee_ids] : []
  showPicker.value = true
}

function closePicker() {
  showPicker.value = false
  currentBoss.value = null
  selectedEmployeeIds.value = []
  search.value = ''
}

function save() {
  if (!currentBoss.value) return

  router.post(
    '/boss/permissions',
    {
      boss_id: currentBoss.value.id,
      employee_ids: selectedEmployeeIds.value,
    },
    {
      onSuccess: () => {
        toast.success('Permisos actualizados')
        // Actualizamos contador local
        const row = bosses.value.find((b) => b.id === currentBoss.value.id)
        if (row) row.employee_count = selectedEmployeeIds.value.length
        closePicker()
      },
      onError: () => toast.error('Error al actualizar'),
    },
  )
}
</script>
