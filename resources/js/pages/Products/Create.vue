<script setup>
import { useForm } from '@inertiajs/vue3'
import { ref } from 'vue'
import { useToast } from 'vue-toastification'

const toast = useToast()
const emit = defineEmits(['close']) // allows emitting "close" to parent
const weight_units = ref<WeightUnit[]>(page.props.weight_units);
const form = useForm({
  name: '',
  minimum_quantity: 0,
})

function submit() {
  form.post('/add/product', {
    onSuccess: () => {
      form.reset()
            toast.success('Added product successfully!')
      emit('close') // tell parent to close modal
    },
  })
}

function cancel() {
  emit('close') // also close modal if user cancels
}
</script>

<template>
  <div class="p-6 max-w-md mx-auto bg-white shadow rounded">
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
        <label for="minimum_quantity" class="block font-semibold mb-1">Minimum Quantity</label>
        <input
          v-model.number="form.minimum_quantity"
          id="quantity"
          type="number"

          class="border rounded px-3 py-2 w-full"
          :class="{ 'border-red-500': form.errors.minimum_quantity }"
        />
        <p v-if="form.errors.minimum_quantity" class="text-red-600 text-sm mt-1">{{ form.errors.minimum_quantity }}</p>
      </div>

      <div class="flex justify-between items-center pt-4">

        <button
          type="button"
          class="text-gray-600 hover:underline"
          @click="cancel"
        >
          Cancel
        </button>
                <button
          type="submit"
          class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700"
          :disabled="form.processing"
        >
          Create
        </button>
      </div>
    </form>
  </div>
</template>
