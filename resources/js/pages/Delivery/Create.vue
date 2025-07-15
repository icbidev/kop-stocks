<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { Link } from '@inertiajs/vue3'
import { defineProps,onMounted,ref } from 'vue'
import { useToast } from 'vue-toastification'

const toast = useToast()
    const products = ref([])
const showModal = ref(false);
    onMounted(async () => {
      try {
        const response = await fetch('http://192.168.2.185:8000/api/products')
        const data = await response.json()
        products.value = data
      } catch (error) {
        console.error('Failed to fetch categories:', error)
      }
    })
    const emit = defineEmits(['close', 'success']);
const form = useForm({
  received_by: '',
  notes: '',
  product_id: '',
  quantity: '',
  weight_per_item: '',
  type: '',
})

function submit() {
  form.post('delivery/create', {
      onSuccess: () => {
      form.reset()
      toast.success('Delivery Created successfully!')
      emit('success'); // Let parent know to close the modal
    },
  })
}
</script>

<template>
  <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
      <h2 class="text-lg font-semibold mb-4 text-black">Add Delivery</h2>

<form @submit.prevent="submit" class="space-y-4 text-black">
  <div>
    <label class="block font-semibold mb-1">Received By</label>
    <input
      v-model="form.received_by"
      type="text"
      class="border rounded px-3 py-2 w-full"
      :class="{ 'border-red-500': form.errors.received_by }"
    />
    <p v-if="form.errors.received_by" class="text-red-600 text-sm mt-1">
      {{ form.errors.received_by }}
    </p>
  </div>

  <div>
    <label class="block font-semibold mb-1">Notes</label>
    <textarea
      v-model="form.notes"
      class="border rounded px-3 py-2 w-full"
      :class="{ 'border-red-500': form.errors.notes }"
    ></textarea>
    <p v-if="form.errors.notes" class="text-red-600 text-sm mt-1">
      {{ form.errors.notes }}
    </p>
  </div>

  <div>
    <label class="block font-semibold mb-1">Product</label>
    <select
      v-model="form.product_id"
      class="border rounded px-3 py-2 w-full"
      :class="{ 'border-red-500': form.errors.product_id }"
    >
      <option value="">Select a product</option>
      <option v-for="product in products" :key="product.id" :value="product.id">
        {{ product.name }}
      </option>
    </select>
    <p v-if="form.errors.product_id" class="text-red-600 text-sm mt-1">
      {{ form.errors.product_id }}
    </p>
  </div>

  <div>
    <label class="block font-semibold mb-1">Quantity</label>
    <input
      v-model.number="form.quantity"
      type="number"
      class="border rounded px-3 py-2 w-full"
      :class="{ 'border-red-500': form.errors.quantity }"
    />
    <p v-if="form.errors.quantity" class="text-red-600 text-sm mt-1">
      {{ form.errors.quantity }}
    </p>
  </div>

  <div>
    <label class="block font-semibold mb-1">Weight per Item</label>
    <input
      v-model="form.weight_per_item"
      type="text"
      class="border rounded px-3 py-2 w-full"
      :class="{ 'border-red-500': form.errors.weight_per_item }"
    />
    <p v-if="form.errors.weight_per_item" class="text-red-600 text-sm mt-1">
      {{ form.errors.weight_per_item }}
    </p>
  </div>

  <div>
    <label class="block font-semibold mb-1">Type</label>
    <select
      v-model="form.type"
      class="border rounded px-3 py-2 w-full"
      :class="{ 'border-red-500': form.errors.type }"
    >
      <option value="">Select type</option>
      <option value="in">In</option>
      <option value="out">Out</option>
    </select>
    <p v-if="form.errors.type" class="text-red-600 text-sm mt-1">
      {{ form.errors.type }}
    </p>
  </div>

  <div class="flex justify-between pt-4">
    <button
      type="submit"
      class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
      :disabled="form.processing"
    >
      Submit
    </button>
    <button
      type="button"
      @click="$emit('close')"
      class="text-gray-600 hover:underline"
    >
      Cancel
    </button>
  </div>
</form>

    </div>
  </div>
</template>