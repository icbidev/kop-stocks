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
      <div class="flex justify-between mt-4 space-x-2">
        <button @click="emit('close')" class="btn">Cancel</button>
        <button @click="handleCreate" class="px-4 py-2 bg-green-600 text-white rounded">Create</button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useForm,router,usePage } from '@inertiajs/vue3'
import { defineEmits, defineProps,ref } from 'vue'
import { useToast } from 'vue-toastification'
const page = usePage();
const user = page.props.auth.user;
function cleanUrl(...segments) {
  const result = [];
  for (let segment of segments) {
    if (typeof segment === 'string') {
      segment = segment.replace(/^\/+|\/+$/g, ''); // trim slashes
      if (result[result.length - 1] !== segment) {
        result.push(segment);
      }
    }
  }
  return '/' + result.join('/');
}

const url = cleanUrl(user.name, 'suppliers');
const toast = useToast()
const props = defineProps({
  show: Boolean
})

const emit = defineEmits(['close', 'update:newSupplier']);
const form = useForm({
  name: '',
});


const handleCreate = () => {
  router.post(url, form, {
    onSuccess: (page) => {
      const newSupplier = page.props.supplier ?? {
        id: form.id,
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
