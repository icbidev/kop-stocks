<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head, usePage, Link, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

const props = defineProps({
  logs: Object,
})
const page = usePage()

const modelType = ref(page.props.filters?.model_type || '')
const eventType = ref(page.props.filters?.event || '')
const searchDate = ref(page.props.filters?.date || '')
const changedBy = ref(page.props.filters?.changedBy || '')



const applyFilters = () => {
router.get(route('admin.audit.logs'), {
  model_type: modelType.value,
  event: eventType.value,
  date: searchDate.value,
  changedBy: changedBy.value, // ✅ match backend key
}, {
  preserveState: true,
  preserveScroll: true,
})

}
</script>



<template>
  <Head title="Audit Logs" />

  <AppLayout>
    <section class="p-6 space-y-6">
      <h1 class="text-2xl font-bold text-gray-800">Audit Logs</h1>

      <div class="overflow-auto border rounded-md">
      <!-- Filters -->
<div class="flex flex-wrap gap-4 mb-4">
<div>
  <label class="block text-sm text-gray-700 mb-1">Changed By</label>
  <input v-model="changedBy" type="text" placeholder="e.g. Jane Doe" class="border px-2 py-1 rounded" />
</div>

  <div>
    <label class="block text-sm text-gray-700 mb-1">Model</label>
    <input v-model="modelType" type="text" placeholder="e.g. Product" class="border px-2 py-1 rounded" />
  </div>
  <div>
    <label class="block text-sm text-gray-700 mb-1">Event</label>
    <select v-model="eventType" class="border px-2 py-1 rounded">
      <option value="">All</option>
      <option value="sync-suppliers">Sync Suppliers</option>
      <option value="create">Created</option>
      <option value="update">Updated</option>
      <option value="delete">Deleted</option>
    </select>
  </div>
  <div>
    <label class="block text-sm text-gray-700 mb-1">Date</label>
    <input v-model="searchDate" type="date" class="border px-2 py-1 rounded" />
  </div>
  <div class="flex items-end">
    <button @click="applyFilters" class="bg-blue-600 text-white px-4 py-1 rounded hover:bg-blue-700">
      Apply Filters
    </button>
  </div>
</div>

        <table class="min-w-full text-sm text-left">
          <thead class="bg-gray-100 text-gray-700">
            <tr>
              <th class="px-4 py-2">Model</th>
              <th class="px-4 py-2">ID</th>
              <th class="px-4 py-2">Event</th>
              <th class="px-4 py-2">Old Values</th>
              <th class="px-4 py-2">New Values</th>
              <th class="px-4 py-2">Timestamp</th>
              <th class="px-4 py-2">Changed By</th>

            </tr>
          </thead>
          <tbody>
            <tr
             v-for="log in props.logs.data"
              :key="log.id"
              class="border-t even:bg-gray-50"
            >
              <td class="px-4 py-2">{{ log.model_type.split('\\').pop() }}</td>
              <td class="px-4 py-2">{{ log.model_id }}</td>
              <td class="px-4 py-2 capitalize">{{ log.event }}</td>
              <td class="px-4 py-2 whitespace-pre-wrap text-gray-700">
                <pre>{{ JSON.stringify(log.old_values, null, 2) }}</pre>
              </td>
              <td class="px-4 py-2 whitespace-pre-wrap text-gray-700">
                <pre>{{ JSON.stringify(log.new_values, null, 2) }}</pre>
              </td>
              <td class="px-4 py-2 text-gray-500">
                {{ new Date(log.created_at).toLocaleString() }}
              </td>
              <td class="px-4 py-2">
  {{ log.user?.name || 'System' }}
</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="flex justify-between items-center text-sm">
        <div>
          Showing {{ props.logs.from }}–{{ props.logs.to }} of {{ props.logs.total }} logs
        </div>
        <div class="flex space-x-2">
          <Link
            v-for="(link, index) in logs.links"
            :key="index"
            :href="link.url || ''"
            v-html="link.label"
            :class="{
              'px-3 py-1 rounded border': true,
              'bg-blue-500 text-white': link.active,
              'text-gray-700 hover:bg-gray-200': !link.active,
              'pointer-events-none text-gray-400': !link.url,
            }"
          />
        </div>
      </div>
    </section>
  </AppLayout>
</template>
