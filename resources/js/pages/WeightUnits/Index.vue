<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import EditModal from '@/pages/WeightUnits/Edit.vue'
import CreateModal from '@/pages/WeightUnits/Create.vue'

import { ref } from 'vue';
import type { Product } from '@/types';
const page = usePage();
const weight_units = ref<WeightUnit[]>(page.props.weight_units);
import { useToast } from 'vue-toastification'
const showCreateModal = ref(false)

const toast = useToast()
 function humanDate(date) {
    const parsedDate = new Date(date);
    const now = new Date();
    const diff = Math.floor((now - parsedDate) / 1000); // in seconds

    if (diff < 60) return 'just now';
    if (diff < 3600) return `${Math.floor(diff / 60)} minutes ago`;
    if (diff < 86400) return `${Math.floor(diff / 3600)} hours ago`;
    if (diff < 172800) return 'yesterday';

    // fallback: format like "Jul 9, 2025, 2:15 PM"
    return parsedDate.toLocaleString(undefined, {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
      hour: 'numeric',
      minute: '2-digit',
      hour12: true
    });
  }
  const showEditModal = ref(false)
const selectedUnit = ref({
  id: null,
  name: '',
  minimum_quantity: 0,
  quantity: ''
});


  const editForm = ref({ id: 0, name: '' ,quantity: '',minimum_quantity: ''});
const updateProduct = (weight_units: WeightUnit) => {
  router.put(`/weight-units/${weight_units.id}`, {
    name: product.name,
    description: product.description,
    quantity: product.quantity,
  }, {
    onSuccess: () => {
      toast.success('Added quantity to '+product.name)
      console.log('Product updated');
    },
    onError: (errors) => {
      console.error(errors);
    }
  });
};
const openEditModal = (unit) => {
  selectedUnit.value = { ...unit }
  showEditModal.value = true
}
const editWeightUnit = (id: number) => {
  const unit = weight_units.value.find(w => w.id === id);
  if (unit) {
    editForm.value = { id: unit.id, name: unit.name };
    showEditModal.value = true;
  }
};

const submitEdit = () => {
  router.put(`/weight-units/${editForm.value.id}`, {
    name: editForm.value.name
  }, {
    onSuccess: () => {
      toast.success('Weight unit updated successfully');
      showEditModal.value = false;
    },
    onError: () => {
      toast.error('Failed to update weight unit');
    }
  });
};

const deleteWeightUnit = (id: number) => {
  if (confirm('Are you sure you want to delete this weight unit?')) {
    router.delete(`/weight-units/${id}`, {
      onSuccess: () => toast.success('Weight unit deleted successfully.'),
      onError: (errors) => toast.error('Failed to delete weight unit.')
    });
  }
};


</script>


<template>
  <Head title="Weight Units" />
  <AppLayout>


    <div class="p-4 space-y-6">
    <div class="flex justify-between">
      <h1 class="text-2xl font-bold">Weight Units</h1>
  <button @click="showCreateModal = true" class="bg-green-600 text-white px-4 py-2 rounded mb-4">
  + Add
</button>
    </div>
<CreateModal :show="showCreateModal" @close="showCreateModal = false" />
      <table class="min-w-full table-auto border rounded overflow-hidden shadow text-sm">
        <thead class="bg-gray-100">
          <tr>
            <th class="p-2 border">ID</th>
            <th class="p-2 border">Name</th>
             <th class="p-2 border">Updated At</th>
            <th class="p-2 border">Action</th>
          </tr>
        </thead>
        <tbody>
<tr v-for="weight_unit in weight_units" :key="weight_unit.id" class="bg-white even:bg-gray-50">

            <td class="border px-2 py-1 font-bold">
            {{ weight_unit.id }}
            </td>
            <td class="border px-2 py-1 font-bold">
            {{ weight_unit.name }}
            </td>
                        <td class="border px-2 py-1 font-bold">
            {{ humanDate(weight_unit.updated_at) }}
            </td>

<td class="border px-2 py-1 flex items-center justify-center gap-2">
  <!-- Edit Button -->
  <button
    @click="openEditModal(weight_unit)"
    class="px-3 py-1 bg-blue-500 text-white text-sm rounded hover:bg-blue-600 transition"
  >
    Edit
  </button>

  <!-- Delete Button -->
  <button
    @click="deleteWeightUnit(weight_unit.id)"
    class="px-3 py-1 bg-red-500 text-white text-sm rounded hover:bg-red-600 transition"
  >
    Delete
  </button>
</td>




          </tr>
        </tbody>
      </table>
    </div>
      <!-- Include the EditModal -->
<EditModal 
  :show="showEditModal" 
  :unit="{ ...selectedUnit }" 
  @close="showEditModal = false" />
  </AppLayout>
</template>
