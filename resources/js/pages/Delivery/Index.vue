<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head,useForm,router, Link   } from '@inertiajs/vue3';
import { ref,onMounted,computed  } from 'vue';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';
import AddDelivery from '@/pages/Delivery/Create.vue';
import EditDelivery from '@/pages/Delivery/Edit.vue';
import dayjs from 'dayjs'
import relativeTime from 'dayjs/plugin/relativeTime'
dayjs.extend(relativeTime)
import { useToast } from 'vue-toastification'

const toast = useToast()

const currentPage = ref(1)
const perPage = 10

// Computed total pages based on filtered deliveries
const totalPages = computed(() => {
  return Math.ceil(filteredDeliveries.value.length / perPage)
})

// Paginated deliveries to show on the current page
const paginatedDeliveries = computed(() => {
  const start = (currentPage.value - 1) * perPage
  const end = start + perPage
  return filteredDeliveries.value.slice(start, end)
})

const showModal = ref(false)
const showEditModal = ref(false)
const selectedDelivery = ref(null)

function openEditModal(delivery) {
  selectedDelivery.value = delivery
  showEditModal.value = true
}

// If needed, load products via props or API
const products = ref([]) // Fill with your actual product data
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Delivery',
        href: '/delivery',
    },
];

const props = defineProps({
  deliveries: Array
})

function destroy(id) {
  if (confirm("Are you sure you want to delete this delivery?")) {
    router.delete(`/delivery/delete/${id}`)
          toast.error('Delivery Deleted!')
  }
}
const filters = ref({
  received_by: '',
  type: '',
  quantity: '',
  weight_per_item: '',
  notes: '',
});

const filteredDeliveries = computed(() => {
  return props.deliveries.filter((delivery: any) => {
    const matchesProduct =
      !filters.value.product ||
      delivery.product?.name?.toLowerCase().includes(filters.value.product.toLowerCase());

    const matchesReceivedBy =
      !filters.value.received_by ||
      delivery.received_by?.toLowerCase().includes(filters.value.received_by.toLowerCase());

    const matchesType =
      !filters.value.type || delivery.type === filters.value.type;

    const matchesQuantity =
      !filters.value.quantity || delivery.quantity == filters.value.quantity;

    const matchesWeight =
      !filters.value.weight_per_item || delivery.weight_per_item == filters.value.weight_per_item;

    const matchesNotes =
      !filters.value.notes ||
      delivery.notes?.toLowerCase().includes(filters.value.notes.toLowerCase());

    const matchesCreatedAt = (
      (!filters.value.created_at_from || dayjs(delivery.created_at).isAfter(filters.value.created_at_from + 'T00:00:00') || dayjs(delivery.created_at).isSame(filters.value.created_at_from, 'day')) &&
      (!filters.value.created_at_to || dayjs(delivery.created_at).isBefore(filters.value.created_at_to + 'T23:59:59') || dayjs(delivery.created_at).isSame(filters.value.created_at_to, 'day'))
    )

    const matchesUpdatedAt = (
      (!filters.value.updated_at_from || dayjs(delivery.updated_at).isAfter(filters.value.updated_at_from + 'T00:00:00') || dayjs(delivery.updated_at).isSame(filters.value.updated_at_from, 'day')) &&
      (!filters.value.updated_at_to || dayjs(delivery.updated_at).isBefore(filters.value.updated_at_to + 'T23:59:59') || dayjs(delivery.updated_at).isSame(filters.value.updated_at_to, 'day'))
    )

    return (
      matchesProduct &&
      matchesReceivedBy &&
      matchesType &&
      matchesQuantity &&
      matchesWeight &&
      matchesNotes &&
      matchesCreatedAt &&
      matchesUpdatedAt
    );
  });
});

</script>
<template>
    <Head title="Delivery" />

    <AppLayout :breadcrumbs="breadcrumbs">
      <div class="p-6">
            <h1 class="text-2xl font-bold py-4">Deliveries</h1>
        <div class="flex justify-between items-center mb-6">
        

<div class="mb-4 flex flex-wrap gap-4 items-end">
  <div>
    <label class="block text-sm font-medium">Product Name</label>
    <input
      v-model="filters.product"
      type="text"
      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
      placeholder="Enter product name"
    />
  </div>
<!-- Quantity -->
<div>
  <label class="block text-sm font-medium">Quantity</label>
  <input
    v-model="filters.quantity"
    type="number"
    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
    placeholder="Enter quantity"
  />
</div>

<!-- Weight -->
<div>
  <label class="block text-sm font-medium">Weight</label>
  <input
    v-model="filters.weight_per_item"
    type="number"
    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
    placeholder="Enter weight"
  />
</div>

<!-- Notes -->
<div>
  <label class="block text-sm font-medium">Notes</label>
  <input
    v-model="filters.notes"
    type="text"
    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
    placeholder="Enter notes"
  />
</div>
  <div>
    <label class="block text-sm font-medium">Received By</label>
    <input
      v-model="filters.received_by"
      type="text"
      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
      placeholder="Enter receiver name"
    />
  </div>

<!-- Updated At Range -->
<div>
  <label class="block text-sm font-medium">Updated At (From)</label>
  <input
    v-model="filters.updated_at_from"
    type="date"
    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
  />
</div>
<div>
  <label class="block text-sm font-medium">Updated At (To)</label>
  <input
    v-model="filters.updated_at_to"
    type="date"
    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
  />
</div>

  <div>
    <label class="block text-sm font-medium">Type</label>
    <select
      v-model="filters.type"
      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
    >
      <option value="" class="text-black">All</option>
      <option value="in" class="text-black">in</option>
      <option value="out" class="text-black">out</option>
      <option value="end" class="text-black">end</option>
    </select>
  </div>
</div>


  <button
    @click="showModal = true"
    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
  >
    Add Delivery
  </button>

  <AddDelivery   v-if="showModal"
  @close="showModal = false"
  @success="showModal = false"
  :products="products"/>
    </div>
        <div class="overflow-x-auto bg-white shadow rounded-lg">
<table class="min-w-full text-sm text-left text-black">
        <thead class="bg-gray-100 border-b">
          <tr>
            <th class="px-4 py-2">ID</th>
            <th class="px-4 py-2">Product</th>
            <th class="px-4 py-2">Quantity</th>
            <th class="px-4 py-2">Weight</th>
            <th class="px-4 py-2">Type</th>
            <th class="px-4 py-2">Received By</th>
            <th class="px-4 py-2">Notes</th>
            <th class="px-4 py-2">Created At</th>
            <th class="px-4 py-2">Updated At</th>
            <th class="px-4 py-2 text-right">Actions</th>
          </tr>
        </thead>
        <tbody>
<tr v-for="delivery in paginatedDeliveries" :key="delivery.id">
<tr v-if="filteredDeliveries.length === 0">
  <td colspan="8" class="text-center px-4 py-6 text-gray-500">No deliveries found.</td>
</tr>

        <td class="px-4 py-2">{{ delivery.id }}</td>
        <td class="px-4 py-2">{{ delivery.product?.name }}</td>
        <td class="px-4 py-2">{{ delivery.quantity }}</td>  
        <td class="px-4 py-2">{{ delivery.weight_per_item }}</td>
        <td class="px-4 py-2">{{ delivery.type }}</td>
        <td class="px-4 py-2">{{ delivery.received_by }}</td>
        <td class="px-4 py-2">{{ delivery.notes }}</td>
        <td class="px-4 py-2"> {{ dayjs(delivery.created_at).format('MM/DD/YYYY') }} </td>
        <td class="px-4 py-2"> {{ dayjs(delivery.updated_at).format('MM/DD/YYYY') }} </td>
        <td class="px-4 py-2 text-right space-x-2">
          <button
            @click="openEditModal(delivery)"
            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
          >
            Edit
          </button>

  <EditDelivery
    v-if="showEditModal"
    :products="products"
    :delivery="selectedDelivery"
    @close="showEditModal = false"
  />
              <button
                @click="destroy(delivery.id)"
                class="text-red-600 hover:underline"
              >
                Delete
              </button>
            </td>
          </tr>
          <tr v-if="deliveries.length === 0">
            <td colspan="8" class="text-center px-4 py-6 text-gray-500">No deliveries found.</td>
          </tr>
        </tbody>
        
      </table>
      <div class="mt-4 flex justify-center items-center space-x-2 p-4 text-black">
  <button
    @click="currentPage--"
    :disabled="currentPage === 1"
    class="px-3 py-1 bg-gray-200 rounded disabled:opacity-50"
  >
    Prev
  </button>

  <span class="text-sm text-gray-700">
    Page {{ currentPage }} of {{ totalPages }}
  </span>

  <button
    @click="currentPage++"
    :disabled="currentPage === totalPages"
    class="px-3 py-1 bg-gray-200 rounded disabled:opacity-50"
  >
    Next
  </button>
</div>
          </div>
            </div>
    </AppLayout>
</template>