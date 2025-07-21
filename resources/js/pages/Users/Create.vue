<template>
  <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
      <h2 class="text-xl font-semibold mb-4">Create User</h2>

      <form @submit.prevent="submit">
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1">Name</label>
          <input v-model="form.name" type="text" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" />

        </div>

        <div class="mb-4">
          <label class="block text-sm font-medium mb-1">Email</label>
          <input v-model="form.email" type="email" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" />

        </div>

        <div class="mb-4">
          <label class="block text-sm font-medium mb-1">Password</label>
          <input v-model="form.password" type="password" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" />
        </div>

        <div class="flex justify-end space-x-2">
          <button type="button" @click="$emit('close')" class="px-4 py-2 text-sm border rounded">Cancel</button>
          <button type="submit" class="px-4 py-2 text-sm bg-blue-600 text-white rounded">Save</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { reactive } from 'vue'
import { router } from '@inertiajs/vue3'

const form = reactive({
  name: '',
  email: '',
  password: '',
})

const submit = () => {
router.post(route('admin.users.store'), form, {
  onSuccess: () => {
    form.name = ''
    form.email = ''
    form.password = ''
    // Close the modal after success
    // Emit close to parent
  }
})

}
</script>
