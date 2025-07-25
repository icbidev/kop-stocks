<template>
<Head title="Roles - Edit Route" />
<form @submit.prevent="updateRoutes" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">

    <div class="bg-white rounded-lg shadow-lg w-8/10  p-6">
   <h1 class="text-3xl max-lg:text-base">Routes List</h1>
        <div class="overflow-x-auto h-144 mt-12">
        <table class="w-full mt-4 border rounded ">
          <thead>
            <tr class="bg-gray-100 text-black ">
              <th class="text-left p-2">Name</th>
              <th class="text-left p-2">Path</th>
              <th class="text-left p-2">Action</th>
            </tr>
          </thead>
<tbody>
  <tr v-for="route in routes" :key="route.path">
    <td>{{ route.name }}</td>
    <td>{{ route.path }}</td>
    <td class="p-2">

      <input
        type="checkbox"
  :value="route.id"
  v-model="selectedRoutes"
      />

    </td>
  </tr>
</tbody>

        </table>
      </div>
        <div class="flex justify-between space-x-2">
          <button type="button" class="btn" @click="$emit('close')">Cancel</button>
          <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Update</button>
        </div>

    </div>

</form>

</template>

<script setup lang="ts">
import { ref, watch, defineProps, defineEmits, onMounted } from 'vue';
import { Head, usePage,useForm  } from '@inertiajs/vue3';

import { useToast } from 'vue-toastification'
const toast = useToast()

const roles = usePage().props.roles;

const routes = usePage().props.routes;

const props = defineProps({
  role: Object,
  routes: Array,
  role_routes: Array,
});


const role_routes = props.role_routes;
const emit = defineEmits(['close', 'updated']);

const selectedRoutes = ref<number[]>([]);

onMounted(() => {
  if (props.role_routes) {
    selectedRoutes.value = props.role_routes.map(r => Number(r.route_id));
  }
});

const form = useForm({
  selectedRoutes: selectedRoutes.value, // not props.role_routes
});

// Function to submit updated routes back to backend or parent
const updateRoutes = () => {
  form.selectedRoutes = selectedRoutes.value;

  form.post(route('admin.roles.updateRoute', props.role.id), {
    preserveScroll: true,
    onSuccess: () => {
      toast.success('Route updated successfully');
      emit('updated');
      emit('close');
    },
  });
};


</script>