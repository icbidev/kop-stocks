<template>
  <div v-if="show" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded shadow w-full max-w-md">
      <h2 class="text-lg font-bold mb-4">Create Supplier</h2>

      <!-- Name -->
      <label class="block text-sm mb-1">Name</label>
      <input
        v-model="form.name"
        type="text"
        class="w-full border px-3 py-2 rounded mb-1"
        placeholder="Enter name"
      />
      <p v-if="form.errors.name" class="text-red-600 text-sm mt-1">{{ form.errors.name }}</p>
      <!-- Buttons -->
      <div class="flex justify-end mt-4 space-x-2">
        <button @click="emit('close')" class="px-4 py-2 bg-gray-300 rounded">Cancel</button>
        <button @click="handleCreate" class="px-4 py-2 bg-green-600 text-white rounded">Create</button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useForm,router } from '@inertiajs/vue3'
import { defineEmits, defineProps,ref } from 'vue'
import { useToast } from 'vue-toastification'

const toast = useToast()
const props = defineProps({
  show: Boolean
})

const emit = defineEmits(['close', 'update:newSupplier']);
const form = useForm({
  name: '',
});


const handleCreate = () => {
  router.post('/suppliers', form, {
    onSuccess: (page) => {
      const newSupplier = page.props.supplier ?? {
        id: Date.now(),
        name: form.name,
        updated_at: new Date().toISOString()
      }

      emit('update:newSupplier', newSupplier)
      emit('close')
    },
    onError: () => {
      console.error('Failed to create supplier')
    }
  })
}
</script>
