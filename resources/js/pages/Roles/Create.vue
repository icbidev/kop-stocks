<template>
  <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
      <h2 class="text-xl font-semibold mb-4">Create Role</h2>

      <form @submit.prevent="submit">
        <div class="mb-4">
          <label class="block mb-1 font-medium">Name</label>
          <input v-model="form.name" type="text" class="w-full border rounded px-3 py-2" />
                  <p v-if="form.errors.name" class="text-red-600 text-sm mt-1">{{ form.errors.name }}</p>
        </div>

        <div class="flex justify-between space-x-2">
          <button type="button" class="btn" @click="$emit('close')">Cancel</button>
          <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
          Create
        </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3'
import { useToast } from 'vue-toastification';

const emit = defineEmits(['close']);
const toast = useToast();

// Initialize form with useForm
const form = useForm({
  name: ''
})

const submit = () => {
  form.post('/admin/roles', {
    onSuccess: () => {
      toast.success('Role created successfully!')
      emit('close')
      form.reset() // Optionally reset the form
    }
  })
}
</script>
