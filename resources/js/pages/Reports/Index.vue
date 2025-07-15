<template>
  <Head title="Deliveries Chart" />

  <AppLayout :breadcrumbs="breadcrumbs">
  
    <div class="p-6">
      <h1 class="text-2xl font-bold mb-4">Deliveries Quantity Chart</h1>
<div class="mt-6 p-4 border rounded bg-gray-100 text-black" x-data>
  <h2 class="text-lg font-semibold mb-4 text-center">Latest End Delivery Quantities per Product</h2>

  <!-- Category Filter Dropdown -->
  <div class="mb-4 text-center">
    <label class="mr-2 font-medium">Filter by Category:</label>
    <select v-model="selectedCategoryLatestEnd" class="border rounded px-2 py-1">
      <option value="">All</option>
      <option v-for="category in categoriesLatestEnd" :key="category.id" :value="category.id">
        {{ category.name }}
      </option>
    </select>
  </div>

  <!-- Products Table -->
  <table class="min-w-1/4 bg-white border border-gray-300 rounded m-auto">
    <thead class="bg-gray-200">
      <tr>
        <th class="text-left py-2 px-4 border-b">Product Name</th>
        <th class="text-left py-2 px-4 border-b">Quantity</th>
      </tr>
    </thead>
    <tbody>
      <tr
        v-for="(entry, index) in filteredProductsLatestEnd"
        :key="index"
        class="hover:bg-gray-100"
      >
        <td class="py-2 px-4 border-b">{{ entry.name }}</td>
        <td class="py-2 px-4 border-b">{{ entry.quantity }}</td>
      </tr>
    </tbody>
  </table>
</div>

      <div class="flex flex-wrap gap-4 mb-6 items-end">
        <!-- Start datetime -->
<div>
  <label class="block mb-1 font-semibold">Start Date & Time:</label>
  <input
    type="datetime-local"
    v-model="startDateTime"
    class="border px-3 py-1 rounded"
  />

</div>

<div>
  <label class="block mb-1 font-semibold">End Date & Time:</label>
  <input
    type="datetime-local"
    v-model="endDateTime"
    class="border px-3 py-1 rounded"
  />

</div>


        <!-- Category filter -->
        <div>
          <label class="block mb-1 font-semibold">Category:</label>
          <select v-model="selectedCategory" class="border px-3 py-1 rounded ">
            <option value="" class="text-black">All Categories</option>
            <option
              v-for="category in categories"
              :key="category.id"
              :value="category.id"
              selected
              class="text-black"
            >
              {{ category.name }}
            </option>
          </select>
        </div>

        <!-- Product name filter -->
        <div>
<select v-model="selectedProduct" class="border px-3 py-1 rounded ">
  <option
    v-for="product in filteredProducts"
    :key="product.id"
    :value="product.id"
    class="text-black"
  >
    {{ product.name }}
  </option>
</select>
        </div>

        <button
          class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
          @click="fetchAndRenderChart"
        >
          Filter
        </button>
      </div>

      <canvas ref="chartCanvas"></canvas>


    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import { Chart, registerables } from 'chart.js'
import AppLayout from '@/layouts/AppLayout.vue'
import { Head } from '@inertiajs/vue3'
import { type BreadcrumbItem } from '@/types'

Chart.register(...registerables)

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Deliveries', href: '/deliveries' },
]

const chartCanvas = ref(null)
let chartInstance = null

const startDateTime = ref('')
const endDateTime = ref('')
const selectedProduct = ref('')
const selectedCategory = ref('')
const selectedCategoryLatestEnd = ref('')

const products = ref([])
const categories = ref([])
const categoriesLatestEnd = ref([])
const deliveries = ref([])
const lastEndQuantities = ref<{ productName: string; quantity: number }[]>([])
// Dynamically filter products by selected category
const filteredProducts = computed(() =>
  products.value.filter(p =>
    selectedCategory.value === '' || p.category_id === Number(selectedCategory.value)
  )
)

const filteredProductsLatestEnd = computed(() =>
  products.value.filter(p =>
    selectedCategoryLatestEnd.value === '' || p.category_id === Number(selectedCategoryLatestEnd.value)
  )
)
// Reset product when category changes (to avoid invalid selection)

watch(selectedCategory, () => {
  const filtered = products.value.filter(p => selectedCategory.value === '' || p.category_id === Number(selectedCategory.value))
  if (filtered.length > 0) {
    selectedProduct.value = filtered[0].id
  } else {
    selectedProduct.value = ''
  }
})


const fetchProducts = async () => {
  try {
    const res = await fetch('http://192.168.2.185:8000/api/products')
    const data = await res.json()
    products.value = data

    // Set the first product as selected
    if (products.value.length > 0) {
      selectedProduct.value = products.value[0].id
    }
  } catch (error) {
    console.error('Error fetching products:', error)
  }
}

const fetchCategories = async () => {
  try {
    const res = await fetch('http://192.168.2.185:8000/api/category')
    categories.value = await res.json()
  } catch (error) {
    console.error('Error fetching categories:', error)
  }
}

const fetchCategoriesLatestEnd = async () => {
  try {
    const res = await fetch('http://192.168.2.185:8000/api/category')
    categoriesLatestEnd.value = await res.json()
  } catch (error) {
    console.error('Error fetching categories:', error)
  }
}

const toPHTime = (datetimeStr: string): string => {
  const date = new Date(datetimeStr)
  return new Intl.DateTimeFormat('en-PH', {
    timeZone: 'Asia/Manila',
    year: 'numeric',
    month: '2-digit',
    day: '2-digit',
    hour: '2-digit',
    minute: '2-digit',
    hour12: true,
  }).format(date)
}

const fetchAndRenderChart = async () => {
  try {
    const res = await fetch('http://192.168.2.185:8000/api/delivery')
    deliveries.value = await res.json()

const filtered = deliveries.value
  .filter(d => {
    if (startDateTime.value && new Date(d.created_at) < new Date(startDateTime.value)) return false
    if (endDateTime.value && new Date(d.created_at) > new Date(endDateTime.value)) return false

    // ✅ Add this line to filter by selected product
    if (selectedProduct.value && d.product_id !== Number(selectedProduct.value)) return false

    return true
  })
  .sort((a, b) => a.created_at.localeCompare(b.created_at))

    const labels = filtered.map(d => toPHTime(d.created_at))
    const values = filtered.map(d => d.quantity)

    // ✅ Extract last `end` deliveries per product
    const productMap = new Map<number, { created_at: string; quantity: number }>()
    deliveries.value.forEach(d => {
      if (d.type === 'end') {
        if (
          !productMap.has(d.product_id) ||
          productMap.get(d.product_id)!.created_at < d.created_at
        ) {
          productMap.set(d.product_id, {
            created_at: d.created_at,
            quantity: d.quantity,
          })
        }
      }
    })

    // Build the summary array
    lastEndQuantities.value = Array.from(productMap.entries()).map(([productId, info]) => {
      const product = products.value.find(p => p.id === productId)
      return {
        productName: product ? product.name : `ID ${productId}`,
        quantity: info.quantity,
      }
    })

    if (chartInstance) chartInstance.destroy()

    const selectedProductName = selectedProduct.value
      ? products.value.find(p => p.id === Number(selectedProduct.value))?.name
      : 'All Products'

    chartInstance = new Chart(chartCanvas.value.getContext('2d'), {
      type: 'line',
      data: {
        labels,
        datasets: [
          {
            label: `Quantity - ${selectedProductName}`,
            data: values,
            borderColor: 'green',
            color: 'black',
            backgroundColor: 'rgba(34,197,94,0.2)',
            fill: true,
            tension: 0.3,
          },
        ],
      },
      options: {
        responsive: true,
        plugins: {
          title: {
            display: true,
            text: 'Delivery Quantities by Product and Time',
          },
        },
        scales: {
          x: {
            title: { display: true, text: 'Date & Time' },
            ticks: { maxRotation: 60, minRotation: 45, autoSkip: true ,color: 'black'},
          },
          y: {
            beginAtZero: true,
            title: { display: true, text: 'Quantity',color: 'black'},
          },
        },
      },
    })
  } catch (error) {
    console.error('Error fetching deliveries:', error)
  }
}


onMounted(async () => {
  await fetchProducts()
  await fetchCategories()
    await fetchCategoriesLatestEnd()
  await fetchAndRenderChart()
})
</script>
