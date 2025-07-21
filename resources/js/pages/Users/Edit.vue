<template>
  <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
      <h2 class="text-xl font-semibold mb-4">Edit User</h2>

      <form @submit.prevent="submit">
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1">Name</label>
          <input v-model="form.name" type="text" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" />
        </div>

        <div class="mb-4">
          <label class="block text-sm font-medium mb-1">Email</label>
          <input v-model="form.email" type="email" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" />
        </div>
                <div>
          <label for="role" class="block font-semibold mb-1">Role</label>
<select v-model="form.role_id" class="border px-3 py-2 rounded w-full">
  <option v-for="role in roles" :key="role.id" :value="role.id">
    {{ role.name }}
  </option>
</select>
        </div>
        <div class="flex justify-between space-x-2">
          <button type="button" @click="$emit('close')" class="btn">Cancel</button>
          <button type="submit" class="px-4 py-2 text-sm bg-green-600 text-white rounded">Update</button>
        </div>

      </form>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { reactive,ref } from 'vue'
import { router,usePage } from '@inertiajs/vue3'
import { defineProps } from 'vue'
const page = usePage();

import { useToast } from 'vue-toastification'
const toast = useToast()
const roles = ref(page.props.roles)

const props = defineProps<{
  user: User,
  roles: array
}>()
const emit = defineEmits(['updated', 'close'])
const form = reactive({
  name: props.user?.name ?? '',
  email: props.user?.email ?? '',
  role_id: props.user?.role_id ?? '',
})

const submit = () => {
  router.put(`/admin/users/${props.user.id}`, form, {
    onSuccess: () => {
      const updatedUser = {
        ...props.user,
        name: form.name,
        email: form.email,
        role_id: form.role_id,
        role: props.roles.find(r => r.id === form.role_id), // get role name
      }

      // Emit the updated user to the parent
      emit('updated', updatedUser)
  toast.success('Updated user successfully!')
    }
  })
}
</script>
