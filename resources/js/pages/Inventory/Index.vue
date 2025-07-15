<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import type { Product } from '@/types';
const page = usePage();
const products = ref<Product[]>(page.props.products);
const suppliers = ref<Supplier[]>(page.props.supplier);
const categories = ref<Category[]>(page.props.category);
const weight_units = ref<WeightUnit[]>(page.props.weight_units);
import { useToast } from 'vue-toastification'

const toast = useToast()
 function humanDate(date) {
    const parsedDate = new Date(date);
    const now = new Date();
    const diff = Math.floor((now - parsedDate) / 1000); // in seconds

    if (diff < 60) return 'just now';
    if (diff < 3600) return `${Math.floor(diff / 60)} minutes ago`;
    if (diff < 86400) return `${Math.floor(diff / 3600)} hours ago`;
    if (diff < 172800) return 'yesterday';

    // fallback: format like "Jul 9, 2025, 2:15 PM"
    return parsedDate.toLocaleString(undefined, {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
      hour: 'numeric',
      minute: '2-digit',
      hour12: true
    });
  }
const updateProduct = (product: Product, event?: Event) => {
  event?.preventDefault();

router.put(`/inventory/${product.id}`, {
  id: product.id,
  name: product.name,
supplier_ids: product.supplier_ids, // must be an array

  quantity: product.quantity,
  minimum_quantity: product.minimum_quantity,
  standard_order: product.standard_order,
  weight_unit_id: product.weight_unit_id,
}, {
  preserveScroll: true,
  onSuccess: () => {
    toast.success('Updated quantity for ' + product.name);
    product.errors = {};
  },
  onError: (errors) => {
    toast.error('Failed to update ' + product.name);
    product.errors = errors;
  }
});

};
products.value = products.value.map(product => ({
  ...product,
  supplier_ids: product.supplier_ids || [],  // ← must be unique per product
  showSuppliers: false,
}));

</script>


<template>
  <Head title="Manage Products" />
  <AppLayout>
    <div class="p-4 space-y-6">
      <h1 class="text-2xl font-bold">Inventory</h1>

<div v-for="category in categories" :key="category.id" class="mb-8">
  <!-- Category Header -->
  <h2 class="text-lg font-semibold text-blue-700 mb-2">{{ category.name }}</h2>

  <!-- Products Table -->
  <div class="w-full overflow-x-auto border rounded shadow">
    <table class="w-full table-fixed text-sm border-collapse">
      <thead class="bg-gray-100">
        <tr>
          <th class="p-2 border w-1/12">ID</th>
          <th class="p-2 border w-3/12">Name</th>
          <th class="p-2 border w-3/12">Supplier</th>
          <th class="p-2 border w-2/12">Minimum Balance</th>
          <th class="p-2 border w-2/12">Standard Order</th>
          <th class="p-2 border w-2/12">Current Balance</th>
          <th class="p-2 border w-2/12">Weight Unit</th>
          <th class="p-2 border w-2/12">Action</th>
          <th class="p-2 border w-2/12">Last Updated</th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="product in products.filter(p => p.category_id === category.id)"
          :key="product.id"
          class="bg-white even:bg-gray-50"
        >
          <td class="border px-2 py-1 truncate">{{ category.name }} - {{ product.id }}</td>
          <td class="border px-2 py-1">
                    <div class="flex justify-center">

            <input
              v-model="product.name"
              type="text"
  :class="[
    'w-full border rounded px-1 py-0.5',
    product.errors?.name ? 'border-red-500' : 'border-gray-300'
  ]"
            />
                                     </div>
          </td>

<td class="border px-2 py-1">
<div class="mb-4 border p-3 rounded shadow">
  <div class="flex justify-center items-center">
    <button @click="product.showSuppliers = !product.showSuppliers" class="text-blue-500 text-sm underline">
      {{ product.showSuppliers ? 'Hide Suppliers' : 'Select Suppliers' }}
    </button>
  </div>

  <div v-if="product.showSuppliers" class="ml-4 mt-2 max-h-60 overflow-y-auto border p-2 rounded">
    <label v-for="supplier in suppliers" :key="supplier.id" class="block">
      <input
        type="checkbox"
        :value="supplier.id"
        v-model="product.supplier_ids"
      />
      {{ supplier.name }}
    </label>
  </div>


  </div>
</td>
                                                  <td class="border px-2 py-1">
          <div class="flex justify-center items-center">
            <input
              v-model="product.minimum_quantity "
              type="text"
         step="0.01"
                      :class="[
    'w-full border rounded px-1 py-0.5 text-right',
    product.errors?.minimum_quantity ? 'border-red-500' : 'border-gray-300'
  ]"
            />
              </div>
          </td>
                                        <td class="border px-2 py-1">
          <div class="flex justify-center items-center">
            <input
              v-model="product.standard_order"
              type="number"
                                    :class="[
    'w-full border rounded px-1 py-0.5 text-right',
    product.errors?.standard_order ? 'border-red-500' : 'border-gray-300'
  ]"
            />
              </div>
          </td>
                    <td class="border px-2 py-1">
          <div class="flex justify-center items-center">
            <input
              v-model="product.quantity"
              type="number"
                                    :class="[
    'w-full border rounded px-1 py-0.5 text-right',
    product.errors?.quantity ? 'border-red-500' : 'border-gray-300'
  ]"
            />
              <p class="p-2">{{ product.weight_unit.quantity }}</p>
              </div>
          </td>

<td class="border px-2 py-1">
  <div class="flex justify-center items-center">
    <select
      v-model="product.weight_unit_id"
                                    :class="[
    'w-full border rounded px-1 py-0.5 text-right',
    product.errors?.weight_unit_id ? 'border-red-500' : 'border-gray-300'
  ]"
    >
      <option disabled value="">Select Unit</option>
      <option
        v-for="unit in weight_units"
        :key="unit.id"
        :value="unit.id"
      >
        {{ unit.name }}
      </option>
    </select>
  </div>
</td>


          <td class="border px-2 py-1 text-center">
            <button
            type="button"
              @click="updateProduct(product, $event)"
              class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700"
            >
              Update
            </button>
          </td>
          <td class="border px-2 py-1">
            <p>{{ humanDate(product.updated_at) }}</p>
          </td>
        </tr>

        <!-- If no products -->
        <tr v-if="products.filter(p => p.category_id === category.id).length === 0">
          <td colspan="6" class="text-center text-gray-500 py-2">
            No products found in this category.
          </td>
        </tr>
      </tbody>
    </table>
  </div>
  </div>
    </div>
  </AppLayout>
</template>
