<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head,useForm,usePage  } from '@inertiajs/vue3';
import { ref,onMounted,computed  } from 'vue';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';
import AddProduct from '@/components/Products/Create.vue';
const page = usePage();
import Edit from '@/pages/Products/Edit.vue';
import { useToast } from 'vue-toastification'

const toast = useToast()
const weight_units = ref<WeightUnits[]>(page.props.weight_units);
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Products',
        href: '/products',
    },
];


const props = defineProps<{
  products: Array<{ id: number; name: string; category?: any; quantity: number,minimum_quantity: number,weight_unit_id: Array }>;
  showModal2: boolean;
}>()

const showModal1 = ref(false)
const showModal2 = ref(false)
const showAddProductModal = ref(false)
const showAddModal = ref(true)
const searchCategory = ref('') // User input for filtering
const selectedCategory = ref('') // The actual selected category
    const categories = ref([])

onMounted(async () => {
  const res = await fetch('http://192.168.2.185:8000/api/category')
  categories.value = await res.json()
})

const categoryForm = useForm({
  name: '',
})
const productForm = useForm({
  name: '',
   category_id: null, // should store the category ID
   weight_unit_id : "",
   quantity : 0,
   minimum_quantity : 0

})

function submitCategory() {
  categoryForm.post('/products/create-category', {
    onSuccess: () => {
      toast.success('Added category successfully!')
      categoryForm.reset()
      showModal1.value = false,
            showModal2.value = false
    }
  })
}

function submitProduct() {
  productForm.post('/products/create-product', {
    onSuccess: () => {
      productForm.reset()
      showModal1.value = false,
            showModal2.value = false
    }
  })
}

const search = ref('')

const filteredProducts = computed(() => {
  return props.products.filter(product => {
    const matchesName = product.name.toLowerCase().includes(search.value.toLowerCase())
    const matchesCategory = selectedCategory.value
      ? product.category_id === selectedCategory.value
      : true
    return matchesName && matchesCategory
  })
})

// Filter categories by name
const filteredCategories = computed(() => {
  if (!searchCategory.value) return categories.value
  return categories.value.filter(c =>
    c.name.toLowerCase().includes(searchCategory.value.toLowerCase())
  )
})
</script>

<template>
    <Head title="Products" />

    <AppLayout :breadcrumbs="breadcrumbs">

<!-- Modal -->
<div

  v-if="showModal1"
  class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
>

  <div class="bg-white rounded-lg p-6 w-full max-w-md">
    <h2 class="text-lg font-semibold mb-4 text-black">Create New Category</h2>

    <form @submit.prevent="submitCategory" class="space-y-4">
      <div>
        <label class="block font-semibold mb-1 text-black">Category Name</label>
        <input
          v-model="categoryForm.name"
          type="text"
          class="w-full border rounded px-3 py-2 text-black"
          :class="{ 'border-red-500': categoryForm.errors.name }"
        />
        <p v-if="categoryForm.errors.name" class="text-red-600 text-sm mt-1">
          {{ categoryForm.errors.name }}
        </p>
      </div>

      <div class="flex justify-end space-x-2">
        <button
          type="button"
          class="text-gray-600 hover:underline"
          @click="showModal1 = false"
        >
          Cancel
        </button>
        <button
          type="submit"
          class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
          :disabled="categoryForm.processing"
        >
          Save
        </button>
      </div>
    </form>
  </div>
</div>
<div
  v-if="showModal2"
  class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
>
  <div class="bg-white rounded-lg p-6 w-full max-w-md">
    <h2 class="text-lg font-semibold mb-4 text-black">Create New Product</h2>

    <form @submit.prevent="submitProduct" class="space-y-4 text-black">
      <div>
      
        <label for="name" class="block font-semibold mb-1">Product Name</label>
        <input
          v-model="productForm.name"
          id="name"
          value=""
          type="text"
          class="border rounded px-3 py-2 w-full"
          :class="{ 'border-red-500': productForm.errors.name }"
          autofocus
        />
        <p v-if="productForm.errors.name" class="text-red-600 text-sm mt-1">{{ productForm.errors.name }}</p>
      </div>
<div>
  <label for="weight_unit_id" class="block font-semibold mb-1">Weight Unit</label>
          <input
    v-model="productForm.weight_unit_id"
    id="weight_unit_id"
          value=""
          type="text"
          class="border rounded px-3 py-2 w-full"
          :class="{ 'border-red-500': productForm.errors.name }"
          autofocus
        />
  <p v-if="productForm.errors.weight_unit_id" class="text-red-600 text-sm mt-1">
    {{ productForm.errors.weight_unit_id }}
  </p>
</div>

    <div>
      <label for="category" class="block font-semibold mb-1">Category</label>
  <select
    v-model="productForm.category_id" 
    class="border rounded px-3 py-2 w-full"
  >
    <option class="text-black" value="">Select category</option>
    <option v-for="cat in filteredCategories" class="text-black" :key="cat.id" :value="cat.id">
      {{ cat.name }}
    </option>
  </select>
          <p v-if="productForm.errors.category_id" class="text-red-600 text-sm mt-1">{{ productForm.errors.category_id }}</p>
    </div>
      <div>
        <label for="minimum_quantity" class="block font-semibold mb-1">Minimum Balance</label>
        <input
          v-model.number="productForm.minimum_quantity"
          id="quantity"
          type="number"
          step="any"
          class="border rounded px-3 py-2 w-full"
          :class="{ 'border-red-500': productForm.errors }"
        />
        <p v-if="productForm.errors.minimum_quantity" class="text-red-600 text-sm mt-1">{{ productForm.errors.minimum_quantity }}</p>
      </div>

      <div class="flex justify-between items-center pt-4">
        <button
          type="submit"
          class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
          :disabled="productForm.processing"
        >
          Add Product
        </button>
        <button
          type="button"
          class="text-gray-600 hover:underline"
          @click="showModal2 = false"
        >
          Cancel
        </button>
      </div>
    </form>
  </div>

</div>
   <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <div class="p-6">
        <h1 class="text-2xl font-bold text-black">Product List</h1>
        <div class="flex flex-wrap justify-between gap-5">
<div class="flex flex-wrap justify-center gap-5">
<div>
  <input
  v-model="search"
  type="text"
  placeholder="Search product's name..."
  class="border px-3 py-2 rounded w-64 mt-8 "
/>
</div>
    <!-- Categories Dropdown -->
<div>
      <label for="category" class="block font-semibold mb-1">Category</label>
  <select
    v-model="selectedCategory"
    class="border px-3 py-2 rounded w-64 mt-2 block"
  >
    <option class="text-black" value="">Select category</option>
    <option v-for="cat in filteredCategories" class="text-black" :key="cat.id" :value="cat.id">
      {{ cat.name }}
    </option>
  </select>
      </div>

    </div>
    <div>
        <button
    class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700"
    @click="showModal2 = true"
  >
    + Create
  </button>
    </div>
        </div>
        <table class="w-full mt-4 border rounded">
          <thead>
            <tr class="bg-gray-100 text-black">
              <th class="text-left p-2">Category</th>
              <th class="text-left p-2">Name</th>
              <th class="text-left p-2">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="product in filteredProducts"
              :key="product.id"
              class="border-t hover:bg-gray-50"
            >
                          <td class="p-2">{{ product.category?.name ?? 'Uncategorized' }}</td>
              <td class="p-2">{{ product.name }}</td>
              <td class="p-2">
              <Edit 
              
  :product="product"
  :categories="categories"
  ></Edit>

              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    </AppLayout>
</template>