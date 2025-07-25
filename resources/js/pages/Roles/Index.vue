<template>
  <Head title="Roles" />
  <AppLayout>
    <div class="p-4">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Roles</h1>
        <button class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700" @click="showCreateModal = true">+ Add</button>
      </div>

      <div class="bg-white shadow rounded overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="text-left px-6 py-3 text-sm font-bold">Name</th>
              <th class="text-left px-6 py-3 text-sm font-bold">Created</th>
              <th class="text-center px-6 py-3 text-sm font-bold">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="role in roles" :key="role.id" class="hover:bg-gray-50">
              <td class="px-6 py-4">{{ role.name }}</td>
              <td class="px-6 py-4">{{ humanDate(role.created_at) }}</td>
              <td class="px-6 py-4 text-center">
                <button @click="editRole(role)" class="px-2 py-1 bg-blue-500 text-white rounded">Edit</button>
                <button @click="editRoute(role)" class="px-2 py-1 ml-4 bg-red-500 text-white rounded"> Permission</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Modals -->
      <CreateRoleModal v-if="showCreateModal" @close="showCreateModal = false" />
<EditRoleModal
  v-if="editingRole"
  :role="editingRole"
  @close="editingRole = null"
  @updated="updateRole"
/>

<EditRouteModal
  v-if="editingRoute"
  :role="editingRoute"
  :role_routes="editingRoute.role_routes"
  :routes="route"
  @close="editingRoute = null"
  @updated="updateRoute"
/>


    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import CreateRoleModal from '@/pages/Roles/Create.vue';
import EditRoleModal from '@/pages/Roles/Edit.vue';
import EditRouteModal from '@/pages/Roles/EditRoute.vue';

const page = usePage();
const roles = usePage().props.roles;
const route = usePage().props.routes;
const role_routes = usePage().props.role_routes;

const showCreateModal = ref(false);
const editingRole = ref(null);
const editingRoute = ref(null);



const updateRole = (updatedRole) => {
  const index = roles.findIndex(role => role.id === updatedRole.id);

  if (index !== -1) {
    // Manually update fields (instead of replacing the whole object)
    roles[index].name = updatedRole.name;
    roles[index].updated_at = updatedRole.updated_at;

    // Add more fields as needed
    // roles[index].permissions = updatedRole.permissions;
  }
};

const updateRoute = (updatedRoutes) => {
  if (!editingRoute.value) return;

  const roleIndex = roles.findIndex(role => role.id === editingRoute.value.id);

  if (roleIndex !== -1) {
    roles[roleIndex].role_routes = updatedRoutes;
  }

  // Clear editingRoute so modal closes if needed
  editingRoute.value = null;
};




function humanDate(date: string) {
  return new Date(date).toLocaleDateString();
}

function editRole(role) {
  editingRole.value = { ...role };
}


function editRoute(role) {
  editingRoute.value = {
    ...role,
    role_routes: role_routes.filter(rr => rr.role_id === role.id),
  };
}



</script>
