<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import EditModal from '@/pages/Supplier/Edit.vue'
import CreateModal from '@/pages/Supplier/Create.vue'

import { ref,watch } from 'vue';
import type { Product } from '@/types';
const page = usePage();
const suppliers = ref<Supplier[]>(page.props.suppliers);
import { useToast } from 'vue-toastification'
const showCreateModal = ref(false)
const user = page.props.auth.user;
const handleUpdatedSupplier = (updatedSupplier) => {
  const index = suppliers.value.findIndex(s => s.id === updatedSupplier.id);
  if (index !== -1) {
    suppliers.value[index].name = updatedSupplier.name;
    suppliers.value[index].updated_at = updatedSupplier.updated_at;
  }
  toast.success('Supplier updated successfully!');
};
const newSupplier = ref(null); // Will hold emitted data from CreateModal

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
  name: '',
});


  const editForm = ref({ id: 0, name: '' });
const updateProduct = (suppliers: Supplier) => {
  router.put(`/suppliers/${suppliers.id}`, {
    name: product.name,
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
const openEditModalsupplier = (supplier) => {
  selectedUnit.value = { ...supplier }
  showEditModal.value = true
}
const editSupplier = (id: number) => {
  const supplier = suppliers.value.find(w => w.id === id);
  if (supplier) {
    editForm.value = { id: supplier.id, name: supplier.name };
    showEditModal.value = true;
  }
};


const submitEdit = () => {
  router.put(`/suppliers/${editForm.value.id}`, {
    name: editForm.value.name
  }, {
    onSuccess: () => {
      toast.success('Supplier updated successfully');
      // ✅ Update the local list
      const index = suppliers.value.findIndex(s => s.id === editForm.value.id);
      if (index !== -1) {
        suppliers.value[index].name = editForm.value.name;
      }
      showEditModal.value = false;
    },
    onError: () => {
      toast.error('Failed to update supplier');
    }
  });
};

const deleteWeightUnit = (id: number) => {
  const url = cleanUrl(user.name, 'suppliers', id);
  if (confirm('Are you sure you want to delete this supplier?')) {
    router.delete(url+`/${id}`, {
      onSuccess: () => {
        toast.success('Supplier deleted successfully.');
        // ✅ Remove from local list
        suppliers.value = suppliers.value.filter(supplier => supplier.id !== id);
      },
      onError: (errors) => toast.error('Failed to delete supplier.')
    });
  }
};

const addSupplier = (newSupplier) => {
  console.log('Received new supplier:', newSupplier);

  const supplierPartial = {
    id: newSupplier.id,
    name: newSupplier.name,
    updated_at: humanDate(newSupplier.updated_at),
  };

  suppliers.value.unshift(supplierPartial); // ✅ Add to the top of the list
};

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



watch(newSupplier, (value) => {
  if (value) {
    suppliers.value.unshift({
      id: value.id,
      name: value.name,
      updated_at: humanDate(value.updated_at),
    });
    toast.success('Supplier added!');
    newSupplier.value = null; // Reset after use
  }
});

</script>


<template>
  <Head title="Suppliers" />
  <AppLayout>


    <div class="p-4 space-y-6">
    <div class="flex justify-between">
      <h1 class="text-2xl font-bold">Suppliers</h1>
  <button @click="showCreateModal = true" class="bg-green-600 text-white px-4 py-2 rounded mb-4">
  + Add
</button>
    </div>
<CreateModal
  :show="showCreateModal"
  v-model:newSupplier="newSupplier"
  @close="showCreateModal = false"
/>

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
<tr v-for="supplier in suppliers" :key="supplier.id" class="bg-white even:bg-gray-50">

            <td class="border px-2 py-1 font-bold">
            {{ supplier.id }}
            </td>
            <td class="border px-2 py-1 font-bold">
            {{ supplier.name }}
            </td>
                        <td class="border px-2 py-1 font-bold">
            {{ humanDate(supplier.updated_at) }}
            </td>
<td class="border px-2 py-1 flex items-center justify-center gap-2">
  <!-- Edit Button -->
  <button
    @click="openEditModalsupplier(supplier)"
    class="px-3 py-1 bg-blue-500 text-white text-sm rounded hover:bg-blue-600 transition"
  >
    Edit
  </button>

  <!-- Delete Button -->
  <button
    @click="deleteWeightUnit(supplier.id)"
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
  @close="showEditModal = false" 
  @updated="handleUpdatedSupplier"
/>

  </AppLayout>
</template>
