<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import type { Product } from '@/types';
import { useToast } from 'vue-toastification';

const page = usePage();
const products = ref<Product[]>(page.props.products);
const toast = useToast();
const selectedStockStatus = ref<string | null>(null);

// Selected supplier filter
const selectedSupplier = ref<string | null>(null);

// Get unique supplier names
const suppliers = computed(() => {
  const all = products.value.flatMap(p => p.suppliers.map(s => s.name));
  return [...new Set(all)];
});


const updateProduct = (product: Product) => {
  router.put(`/low-stocks/${product.id}`, {
    name: product.name,
    description: product.description,
    quantity: product.quantity,
  }, {
    onSuccess: () => {
      toast.success('Added quantity to ' + product.name);
      console.log('Product updated');
    },
    onError: (errors) => {
      console.error(errors);
    }
  });
};

// Filter and sort products
const sortedProducts = computed(() => {
  let filtered = [...products.value];

  // Filter by supplier
  if (selectedSupplier.value) {
    filtered = filtered.filter(p =>
      p.suppliers.some(s => s.name === selectedSupplier.value)
    );
  }

  // Filter by stock status
  if (selectedStockStatus.value === 'low') {
    filtered = filtered.filter(p =>
      p.quantity > 0 && p.quantity < p.minimum_quantity
    );
  } else if (selectedStockStatus.value === 'out') {
    filtered = filtered.filter(p => p.quantity == 0.00);
  }

  // Sort by category name
  return filtered.sort((a, b) => {
    const nameA = a.category.name.toLowerCase();
    const nameB = b.category.name.toLowerCase();
    return nameA.localeCompare(nameB);
  });
});

</script>



<template>
  <Head title="Manage Products" />
  <AppLayout>
    <div class="p-4 space-y-6">
      <h1 class="text-2xl font-bold">Stocks</h1>

      <!-- Supplier Filter -->
      <div>
        <label for="supplier" class="block text-sm font-medium text-gray-700">Filter by Supplier:</label>
        <select
          id="supplier"
          v-model="selectedSupplier"
          class="mt-1 block w-full max-w-xs border-gray-300 rounded-md shadow-sm"
        >
          <option value="">All Suppliers</option>
          <option v-for="name in suppliers" :key="name" :value="name">{{ name }}</option>
        </select>
      </div>
      <!-- Stock Status Filter -->
<div>
  <label for="stockStatus" class="block text-sm font-medium text-gray-700">Filter by Stock Status:</label>
  <select
    id="stockStatus"
    v-model="selectedStockStatus"
    class="mt-1 block w-full max-w-xs border-gray-300 rounded-md shadow-sm"
  >
    <option value="">All</option>
    <option value="low">Low Stock</option>
    <option value="out">Out of Stock</option>
  </select>
</div>


      <!-- Products Table -->
      <table class="min-w-full table-auto border rounded overflow-hidden shadow text-sm">
        <thead class="bg-gray-100">
          <tr>
            <th class="p-2 border">ID</th>
            <th class="p-2 border">Name</th>
            <th class="p-2 border">Minimum Balance</th>
            <th class="p-2 border">Current Balance</th>
            <th class="p-2 border">Standard Order</th>
          </tr>
        </thead>
        <tbody>
<tr
  v-for="product in sortedProducts"
  :key="product.id"
  :class="{
    'bg-red-100': product.quantity == 0.00,
    'bg-yellow-100': (parseFloat(product.quantity) > 0) && parseFloat(product.quantity) < parseFloat(product.minimum_quantity),
  }"
>
<td class="border px-2 py-1 font-bold">
  <div class="text-xs text-gray-600">
    <span v-for="supplier in product.suppliers" :key="supplier.id" class="inline-block bg-blue-100 text-blue-800 rounded px-2 py-0.5 mr-1 mb-1">
      {{ supplier.name }}
    </span>
  </div>
  <div>{{ product.category.name }} - #{{ product.id }}</div>
</td>
            <td class="border px-2 py-1 font-bold">
              <p>{{ product.name }}</p>
            </td>
            <td class="border px-2 py-1 font-bold">
              <p>{{ product.minimum_quantity }} {{ product.weight_unit.name }}</p>
            </td>
            <td class="border px-2 py-1 text-red-500 font-bold">
              <p>{{ product.quantity }}</p>
            </td>
            <td class="border px-2 py-1 text-yellow-500 font-bold">

              <p>{{ (product.standard_order - product.quantity).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}</p>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </AppLayout>
</template>

