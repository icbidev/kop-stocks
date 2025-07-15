<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { Link } from '@inertiajs/vue3'
import { useToast } from 'vue-toastification'

const toast = useToast()

const props = defineProps({
  delivery: Object,
  products: Array,
})

const form = useForm({
  received_by: props.delivery.received_by || '',
  notes: props.delivery.notes || '',
  product_id: props.delivery.product_id || '',
  quantity: props.delivery.quantity || 1,
  weight_per_item: props.delivery.weight_per_item || 0,
  type: props.delivery.type || '',
})

function submit() {
    form.put(`/delivery/${props.delivery.id}`, {
    onSuccess: () => {
      toast.success('Delivery updated successfully!')

    },
  })
}
</script>

<template>
  <!-- Modal backdrop -->
  <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <!-- Modal box -->
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-2xl relative text-left">
      <button
        @click="emit('close')"
        class="absolute top-2 right-2 text-gray-500 hover:text-gray-800"
      >
        ✕
      </button>

      <h1 class="text-2xl font-bold mb-4">Edit Delivery</h1>

      <form @submit.prevent="submit" class="space-y-4">
        <div>
          <label class="block font-semibold mb-1">Received By</label>
          <input
            v-model="form.received_by"
            type="text"
            class="border px-3 py-2 rounded w-full"
          />
          <p v-if="form.errors.received_by" class="text-red-500 text-sm">{{ form.errors.received_by }}</p>
        </div>

        <div>
          <label class="block font-semibold mb-1">Notes</label>
          <textarea
            v-model="form.notes"
            class="border px-3 py-2 rounded w-full"
          ></textarea>
          <p v-if="form.errors.notes" class="text-red-500 text-sm">{{ form.errors.notes }}</p>
        </div>

        <div>
          <label class="block font-semibold mb-1">Product</label>
          <select
            v-model="form.product_id"
            class="border px-3 py-2 rounded w-full"
          >
            <option value="">Select a product</option>
            <option
              v-for="product in products"
              :key="product.id"
              :value="product.id"
            >
              {{ product.name }}
            </option>
          </select>
          <p v-if="form.errors.product_id" class="text-red-500 text-sm">{{ form.errors.product_id }}</p>
        </div>

        <div>
          <label class="block font-semibold mb-1">Quantity</label>
          <input
            v-model.number="form.quantity"
            type="number"
            min="1"
            class="border px-3 py-2 rounded w-full"
          />
          <p v-if="form.errors.quantity" class="text-red-500 text-sm">{{ form.errors.quantity }}</p>
        </div>

        <div>
          <label class="block font-semibold mb-1">Weight per Item</label>
          <input
            v-model.number="form.weight_per_item"
            type="number"
            step="0.01"
            min="0"
            class="border px-3 py-2 rounded w-full"
          />
          <p v-if="form.errors.weight_per_item" class="text-red-500 text-sm">{{ form.errors.weight_per_item }}</p>
        </div>

        <div>
          <label class="block font-semibold mb-1">Type</label>
          <input
            v-model="form.type"
            type="text"
            class="border px-3 py-2 rounded w-full"
          />
          <p v-if="form.errors.type" class="text-red-500 text-sm">{{ form.errors.type }}</p>
        </div>

        <div class="flex justify-between items-center pt-4">
          <button
            type="submit"
            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
            :disabled="form.processing"
          >
            Save Changes
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