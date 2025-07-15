<script setup lang="ts">
import { ref, watch, onMounted } from 'vue'
import { useForm,usePage } from '@inertiajs/vue3'
import { useToast } from 'vue-toastification'

const toast = useToast()
const page = usePage()

// Sample props for categories and initial product
const props = defineProps({
  categories: Array,
  product: Object
})

const showEditModal = ref(false)
const product = ref(props.product || {})

const productForm = useForm({
  name: '',
  category_id: '',
  minimum_quantity: 0,
})

onMounted(() => {
  if (page.props.flash?.success) {
    toast.success(page.props.flash.success)
  }
  if (page.props.flash?.error) {
    toast.error(page.props.flash.error)
  }
})
watch(() => props.product, (newProduct) => {
  if (newProduct) {
    productForm.category_id = newProduct.category_id || ''
  }
}, { immediate: true })
// 🟡 Open modal and fill form with product data
function openEditModal(selectedProduct) {

  productForm.name = product.value.name
  productForm.category = product.value.category
    productForm.minimum_quantity = product.value.minimum_quantity
  showEditModal.value = true
}

function submitProductUpdate() {
  productForm.put(`/products/${product.value.id}/edit-product`, {
    onSuccess: () => {
                  toast.success('Updated product successfully!')
      showEditModal.value = false
    }
  })
}

</script>
<template>
    <button
      @click="openEditModal(product)"
      class="px-2 py-1 bg-blue-500 text-white rounded"
    >
      Edit
    </button>
  <div
    v-if="showEditModal"
    class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
  >
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
      <h2 class="text-lg font-semibold mb-4 text-black">Edit Product</h2>

      <form @submit.prevent="submitProductUpdate" class="space-y-4 text-black">
        <div>
          <label for="name" class="block font-semibold mb-1">Product Name</label>
          <input
            v-model="productForm.name"
            id="name"
            type="text"

            class="border rounded px-3 py-2 w-full"
            :class="{ 'border-red-500': productForm.errors.name }"
            autofocus
          />
          <p v-if="productForm.errors.name" class="text-red-600 text-sm mt-1">
            {{ productForm.errors.name }}
          </p>
        </div>

        <div>
          <label for="category" class="block font-semibold mb-1">Category</label>
          <select
            v-model="productForm.category_id"
            id="category_id"
            class="border rounded px-3 py-2 w-full"
            :class="{ 'border-red-500': productForm.errors.category_id }"
          >

  <option
    v-for="category in categories"
    :key="category.id"
    :value="category.id"
      :selected="category.id === productForm.category_id"
  >
  
    {{ category.name }}
  </option>
          </select>
          <p v-if="productForm.errors.category_id" class="text-red-600 text-sm mt-1">
            {{ productForm.errors.category_id }}
          </p>
        </div>
                <div>
          <label for="name" class="block font-semibold mb-1">Minimum Balance</label>
          <input
            v-model="productForm.minimum_quantity"
            id="name"
            step="any"
            type="number"

            class="border rounded px-3 py-2 w-full"
            :class="{ 'border-red-500': productForm.errors.minimum_quantity }"
            autofocus
          />
          <p v-if="productForm.errors.minimum_quantity" class="text-red-600 text-sm mt-1">
            {{ productForm.errors.minimum_quantity }}
          </p>
        </div>
        <div class="flex justify-between items-center pt-4">
          <button
            type="submit"
            class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700"
            :disabled="productForm.processing"
          >
            Update Product
          </button>
          <button
            type="button"
            class="text-gray-600 hover:underline"
            @click="showEditModal = false"
          >
            Cancel
          </button>
        </div>
      </form>
    </div>
  </div>
</template>