<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import { Chart, registerables } from 'chart.js'
Chart.register(...registerables)

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Dashboard', href: '/dashboard' },
];

const totalProducts = ref(0)
const lowStockCount = ref(0)
const outOfStockCount = ref(0)
const mostStockedProduct = ref({ name: '', quantity: 0 })

onMounted(async () => {
  const productsRes = await fetch('http://192.168.2.185:8000/api/products')
  const products = await productsRes.json()

  totalProducts.value = products.length
  lowStockCount.value = products.filter(p => Number(p.quantity) < Number(p.minimum_quantity) && Number(p.quantity) > 0).length
  outOfStockCount.value = products.filter(p => Number(p.quantity) === 0).length

  // Find most stocked product
  const sorted = [...products].sort((a, b) => Number(b.quantity) - Number(a.quantity))
  if (sorted[0]) {
    mostStockedProduct.value = {
      name: sorted[0].name,
      quantity: Number(sorted[0].quantity),
    }
  }
// Sort products by quantity descending
const topProducts = [...products]
  .sort((a, b) => Number(b.quantity) - Number(a.quantity))
  .slice(0, 20); // Adjust to show top 10, 20, etc.



const labels = topProducts.map(p => p.name);
const quantities = topProducts.map(p => Number(p.quantity));
  const ctx = document.getElementById('productChart')?.getContext('2d');
  if (ctx) {
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels,
        datasets: [{
          label: 'Quantity',
          data: quantities,
          backgroundColor: 'rgba(59, 130, 246, 0.6)',
          borderColor: 'rgba(59, 130, 246, 1)',
          borderWidth: 1,
          borderRadius: 6
        }]
      },
      options: {
        responsive: true,
        scales: {
          y: { beginAtZero: true, ticks: { color: 'black' } },
          x: { ticks: { color: 'black' } }
        },
        plugins: {
          legend: { labels: { color: 'black' } }
        }
      }
    });
  }
});
</script>



<template>
  <Head title="Dashboard" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">

      <!-- Summary Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
        <!-- Total Products -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
          <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-2">Total Products</h2>
          <p class="text-5xl font-bold text-indigo-600 dark:text-indigo-400">{{ totalProducts }}</p>
        </div>

        <!-- Low Stock -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
          <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-2">Low Stock Items</h2>
          <p class="text-5xl font-bold text-yellow-500 dark:text-yellow-400">{{ lowStockCount }}</p>
        </div>

        <!-- Out of Stock -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
          <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-2">Out of Stock Items</h2>
          <p class="text-5xl font-bold text-red-500 dark:text-red-400">{{ outOfStockCount }}</p>
        </div>

        <!-- Most Stocked Product -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
          <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-2">Most Stocked Product</h2>
          <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ mostStockedProduct.name }}</p>
          <p class="text-sm text-gray-500 dark:text-gray-400">Quantity: {{ mostStockedProduct.quantity }}</p>
        </div>
      </div>

      <!-- Chart -->
      <div class="relative flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border h-[400px] md:min-h-min mt-6">
        <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-200 m-4">Top Product Quantities</h2>
        <canvas id="productChart" class="w-full max-w-full h-[400px]"></canvas>
      </div>
    </div>
  </AppLayout>
</template>


