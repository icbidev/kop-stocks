
<script setup>
import { useForm } from '@inertiajs/vue3'

const form = useForm({
  name: '',
  quantity: 0,
})

function submit() {
  form.post(route('products.store'), {
    onSuccess: () => {
      form.reset('name', 'quantity')
    },
  })
}
</script>
<template>
  <div class="p-6 max-w-md mx-auto">
    <h1 class="text-2xl font-bold mb-6">Add New Product</h1>

    <form @submit.prevent="submit" class="space-y-4">
      <div>
        <label for="name" class="block font-semibold mb-1">Product Name</label>
        <input
          v-model="form.name"
          id="name"
          type="text"
          class="border rounded px-3 py-2 w-full"
          :class="{ 'border-red-500': form.errors.name }"
          autofocus
        />
        <p v-if="form.errors.name" class="text-red-600 text-sm mt-1">{{ form.errors.name }}</p>
      </div>

      <div>
        <label for="quantity" class="block font-semibold mb-1">Initial Quantity</label>
        <input
          v-model.number="form.quantity"
          id="quantity"
          type="number"
          min="0"
          class="border rounded px-3 py-2 w-full"
          :class="{ 'border-red-500': form.errors.quantity }"
        />
        <p v-if="form.errors.quantity" class="text-red-600 text-sm mt-1">{{ form.errors.quantity }}</p>
      </div>

      <button
        type="submit"
        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
        :disabled="form.processing"
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
    </form>
  </div>
</template>

