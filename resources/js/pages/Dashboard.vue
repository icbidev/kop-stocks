<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head,usePage } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import { Chart, registerables, ChartType, ChartOptions } from 'chart.js'
Chart.register(...registerables)
const page = usePage()
const apiUser = page.props.auth.user;
const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Dashboard', href: '/dashboard' },
];
const supplierContacts = page.props.auth.supplier;
const totalProducts = ref(0)
const lowStockCount = ref(0)
const outOfStockCount = ref(0)
const recentlyStockedProduct = ref(0)
const fastMovingItems = ref([])
const newlyAdded = ref([])
const mostStockedProduct = ref({ name: '', quantity: 0 })

onMounted(async () => {
  const productsRes = await fetch(`http://192.168.2.185:8000/${apiUser.name}/api/products`);
  const products = await productsRes.json()

  totalProducts.value = products.length
  lowStockCount.value = products.filter(p => Number(p.quantity) < Number(p.minimum_quantity) && Number(p.quantity) > 0).length
  outOfStockCount.value = products.filter(p => Number(p.quantity) === 0).length
  recentlyStockedProduct.value = products
  .slice()
  .sort((a, b) => new Date(b.updated_at) - new Date(a.updated_at))[0]


fastMovingItems.value = products
  .map(p => {
    const daysSinceStocked = (new Date() - new Date(p.updated_at)) / (1000 * 60 * 60 * 24)
    const lowStockRatio = Number(p.quantity) / Number(p.standard_order || 1)
    return {
      ...p,
      score: (1 - lowStockRatio) / daysSinceStocked  // higher = faster-moving
    }
  })
  .filter(p => isFinite(p.score) && p.score > 0)
  .sort((a, b) => b.score - a.score)
  .slice(0, 5)  // Top 5 fast-moving items


  // Find most stocked product
  const sorted = [...products].sort((a, b) => Number(b.quantity) - Number(a.quantity))
  if (sorted[0]) {
    mostStockedProduct.value = {
      name: sorted[0].name,
      quantity: Number(sorted[0].quantity),
    }
  }
    newlyAdded.value =[...products]
    .slice()
    .sort((a, b) => new Date(b.created_at).getTime() - new Date(a.created_at).getTime())
    .slice(0, 4)
// Sort products by quantity descending
const topProducts = [...products]
  .sort((a, b) => Number(b.quantity) - Number(a.quantity))
  .slice(0, 20); // Adjust to show top 10, 20, etc.



})

</script>



<template>
  <Head title="Dashboard" />
  <AppLayout :breadcrumbs="breadcrumbs">
<div class="flex flex-col gap-4 p-4 rounded-xl h-full">
  <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-100">
    Greetings, {{ page.props.auth.authUser.name }}
  </h1>

  <!-- Summary Cards -->
  <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
    <!-- Total Products -->
  <a
    :href="route(page.props.auth.user.name + '.products')"
    v-if="page.props.auth.user.name == 'admin' || page.props.auth.user.name == 'manager'"
    class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6"
  >
    <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-2">Total Products</h2>
    <p class="text-5xl font-bold text-indigo-600 dark:text-indigo-400">{{ totalProducts }}</p>
  </a>
<div v-else 
class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
  <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-2">Total Products</h2>
  <p class="text-5xl font-bold text-indigo-600 dark:text-indigo-400">{{ totalProducts }}</p>

</div>


    <!-- Low Stock Items -->
<a
  :href="`http://192.168.2.185:8000/${page.props.auth.user.name}/low-stocks?stock-status=low`"
  class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6"
>
  <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-2">Low Stock Items</h2>
  <p class="text-5xl font-bold text-yellow-500 dark:text-yellow-400">{{ lowStockCount }}</p>
</a>


    <!-- Out of Stock -->
<a
  :href="`http://192.168.2.185:8000/${page.props.auth.user.name}/low-stocks?stock-status=out`"
  class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6"
>
  <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-2">Out of Stock Items</h2>
  <p class="text-5xl font-bold text-red-500 dark:text-red-400">{{ outOfStockCount }}</p>
</a>

    <!-- Most Stocked Product -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
      <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-2">Most Stocked Product</h2>
      <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ mostStockedProduct.name }}</p>
      <p class="text-sm text-gray-500 dark:text-gray-400">Quantity: {{ mostStockedProduct.quantity }}</p>
    </div>
  </div>

  <!-- Lower Panels -->
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Left Column: Recently Stocked + Supplier Contacts -->
    <div>
      <!-- Recently Stocked -->
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
        <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-2">Recently Stocked Item</h2>
        <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ recentlyStockedProduct.name }}</p>
        <p class="text-sm text-gray-500 dark:text-gray-400">Quantity: {{ recentlyStockedProduct.quantity }}</p>
      </div>

      <!-- Supplier Contacts Table -->
      
      <div >
        <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mt-4">Supplier Contacts</h2>
        <div class="overflow-y-auto  md:h-144 dark:bg-gray-800 rounded-xl shadow-lg md:p-6 md:m-6">
          <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 p-4">
            <thead class="bg-gray-50 dark:bg-gray-700">
              <tr>
                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Supplier Name</th>
                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Contact Number</th>
                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Created At</th>
              </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
              <tr v-for="item in supplierContacts" :key="item.id">
                <td class="px-4 py-2 text-sm text-gray-800 dark:text-gray-200">{{ item.name }}</td>
                <td class="px-4 py-2 text-sm text-gray-800 dark:text-gray-200">{{ item.contact_number }}</td>
                <td class="px-4 py-2 text-sm text-gray-800 dark:text-gray-200">{{ new Date(item.created_at).toLocaleString() }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Right Column: Newly Added + Fast Moving -->
    <div class="flex flex-col gap-6">
      <!-- Newly Added Items -->
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
        <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-4">Newly Added Items</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div 
            v-for="item in newlyAdded" 
            :key="item.id" 
            class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4"
          >
            <p class="text-xl font-bold text-green-600 dark:text-green-400">{{ item.name }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">Quantity: {{ item.quantity }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">Stocked on: {{ item.created_at }}</p>
          </div>
        </div>
      </div>

      <!-- Fast Moving Items -->
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
        <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-4">Fast-Moving Items</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div 
            v-for="item in fastMovingItems" 
            :key="item.id" 
            class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4"
          >
            <p class="text-xl font-bold text-green-600 dark:text-green-400">{{ item.name }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">Quantity: {{ item.quantity }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">Stocked on: {{ item.created_at }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

  </AppLayout>
</template>


