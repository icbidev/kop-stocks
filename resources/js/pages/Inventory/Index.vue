<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import type { Product } from '@/types';
const page = usePage();
const products = ref<Product[]>(page.props.products);
const isLoading = ref(false);
const suppliers = ref<Supplier[]>(page.props.supplier);
const categories = ref<Category[]>(page.props.category);
const weight_units = ref<WeightUnit[]>(page.props.weight_units);
import { useToast } from 'vue-toastification'
const user = page.props.auth.user;
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

function cleanUrl(...segments) {
  const result = [];
  for (let segment of segments) {
    if (typeof segment === 'string') {
      segment = segment.replace(/^\/+|\/+$/g, ''); // trim slashes
      if (result[result.length - 1] !== segment) {
        result.push(segment);
      }
    }
  }
  return '/' + result.join('/');
}

const updateAllProducts = async (categoryId: number) => {
  isLoading.value = true;

  const categoryProducts = products.value.filter(p => p.category_id === categoryId);
  const total = categoryProducts.length;
  let current = 0;
  let estimatedTimePerProduct = 500; // initial guess
  let progressToastId = null;

  try {
    progressToastId = toast.info(`⏳ Updating products in category…`, { timeout: false });
    const startTime = Date.now();

    for (const product of categoryProducts) {
      const url = cleanUrl(user.name, 'inventory', product.id, 'edit');
      const productStartTime = Date.now();

      await new Promise((resolve, reject) => {
        router.put(
          url,
          {
            id: product.id,
            name: product.name,
            supplier_ids: product.supplier_ids,
            quantity: product.quantity,
            minimum_quantity: product.minimum_quantity,
            standard_order: product.standard_order,
            weight_unit_id: product.weight_unit_id,
          },
          {
            preserveScroll: true,
            preserveState: true,
            replace: true,
            onSuccess: () => {
              product.errors = {};
              resolve(null);
            },
            onError: (errors) => {
              toast.error(`❌ Failed to update ${product.name}`);
              product.errors = errors;
              reject(errors);
            },
          }
        );
      });

      current++;
      const timeSpent = Date.now() - productStartTime;
      estimatedTimePerProduct = (Date.now() - startTime) / current;
      const timeLeft = Math.round(((total - current) * estimatedTimePerProduct) / 1000);

      toast.update(progressToastId, {
        content: `⏳ Updating ${current}/${total}… (${timeLeft}s left)`,
        timeout: 5000,
      });
    }
toast.update(progressToastId, {
  id: progressToastId,
  content: '✅ Category products updated!',
  type: 'success',
  timeout: 3000,           // <-- this re-enables auto-dismiss
  hideProgressBar: false,  // optional, shows the progress bar
});
setTimeout(() => {
  toast.dismiss(progressToastId);
}, 3000);

  } catch (e) {
    console.error(e);
    toast.update(progressToastId, {
      content: '⚠️ Error during update.',
      type: 'error',
      timeout: 5000,
    });
  } finally {
    isLoading.value = false;
  }
};




products.value = products.value.map(product => ({
  ...product,
  supplier_ids: product.supplier_ids || [],  // ← must be unique per product
  showSuppliers: false,
}));

</script>


<template>
  <Head title="Inventory" />
  <AppLayout>
    <div class="p-4 space-y-6">
      <h1 class="text-2xl font-bold">Inventory</h1>

<div v-for="category in categories" :key="category.id" class="mb-8">
  <!-- Category Header -->
  <div class="flex justify-between items-center m-4">
    <h2 class="text-lg font-semibold text-blue-700">{{ category.name }}</h2>
    <button
      @click="updateAllProducts(category.id)"
      class="bg-green-600 text-white px-3 py-1.5 rounded hover:bg-green-700"
    >
      Update All {{ category.name }}
    </button>
  </div>

  <!-- Products Table -->
  <div class="w-full overflow-x-auto border rounded shadow">
    <table class="w-full table-fixed text-sm border-collapse">
      <thead class="bg-gray-100">
        <tr>
          <th class="p-2 border w-1/12">ID</th>
          <th class="p-2 border w-3/12">Name</th>
          <th class="p-2 border w-2/12">Current Balance</th>
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

            <p class="w-full border rounded px-1 py-0.5"
            >{{ product.name }}</p>
                                     </div>
          </td>

<td class="border px-2 py-1">
  <div class="flex items-center gap-2">
    <input
      v-model.number="product.quantity"
      type="number"
      step="1"
      min="0"
      :class="[
        'border rounded px-1 py-0.5 text-right',
        product.errors?.standard_order ? 'border-red-500' : 'border-gray-300'
      ]"
    />
    <p class="text-sm text-gray-700 whitespace-nowrap">{{ product.weight_unit.name }}</p>
  </div>
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
