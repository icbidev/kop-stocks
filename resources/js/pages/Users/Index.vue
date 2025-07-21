<template>
  <AppLayout>
    <Head title="Users" />

    <div class="container mx-auto py-10">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-semibold text-gray-800">Users</h1>
      </div>

      <div class="overflow-x-auto bg-white rounded shadow">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-sm font-bold">Name</th>
              <th class="px-6 py-3 text-left text-sm font-bold">Role</th>
              <th class="px-6 py-3 text-left text-sm font-bold">Email</th>
              <th class="px-6 py-3 text-right text-sm font-bold">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="user in users" :key="user.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap">{{ user.name }}</td>
<td class="px-6 py-4 whitespace-nowrap">{{ user.role.name }}</td>
              <td class="px-6 py-4 whitespace-nowrap">{{ user.email }}</td>
              <td class="px-6 py-4 text-right m-4">
                <button
                  @click="editUser(user)"
                  class="px-2 py-1 bg-blue-500 mr-2 text-white rounded"
                >
                  Edit
                </button>
                <button
                  @click="deleteUser(user.id)"
                  class="px-2 py-1 bg-red-600 text-white rounded"
                >
                  Delete
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Modals -->
      <CreateUserModal v-if="showCreate" @close="showCreate = false" />
      <EditUserModal v-if="editingUser" :user="editingUser" :roles="roles" @close="editingUser = null"   @updated="updateUserInList"/>

    </div>
  </AppLayout>
</template>

<script lang="ts" setup>
import { ref,reactive } from 'vue'
import { Head, usePage, router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import CreateUserModal from '@/pages/Users/Create.vue'
import EditUserModal from '@/pages/Users/Edit.vue'

const users = reactive<User[]>(usePage().props.users)


const page = usePage()
const roles = ref<Role[]>(page.props.roles as Role[])
const showCreate = ref(false)
const editingUser = ref(null)


const editUser = (user) => {
  editingUser.value = user
}


const updateUserInList = (updatedUser) => {

  const index = users.findIndex(user => user.id === updatedUser.id)
  if (index !== -1) {
    users[index] = updatedUser
  }
  editingUser.value = null
}

const deleteUser = (id) => {
  if (confirm('Are you sure you want to delete this user?')) {
    router.delete(route('admin.users.destroy', id))
  }
}
</script>
