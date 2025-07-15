<!-- resources/js/Pages/Movements/Create.vue -->
<template>
  <div class="p-4">
    <h1 class="text-xl font-bold mb-4">Update Product Quantity</h1>

    <form @submit.prevent="submit">
      <div class="mb-4">
        <label class="block mb-1">Quantity</label>
        <input v-model="form.quantity" type="number" class="border p-2 w-full" />
      </div>

      <div class="mb-4">
        <label class="block mb-1">Type</label>
        <select v-model="form.type" class="border p-2 w-full">
          <option value="in">Add</option>
          <option value="out">Subtract</option>
        </select>
      </div>

      <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Submit</button>
    </form>
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import { usePage } from '@inertiajs/vue3'

const page = usePage()
const product = page.props.product

const form = useForm({
  quantity: '',
  type: 'in',
})

function submit() {
  const routeName = form.type === 'in' ? 'products.add' : 'products.subtract'
  form.post(route(routeName, product.id))
}
</script>