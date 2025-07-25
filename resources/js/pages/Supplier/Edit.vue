<template>
  <div v-if="show" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded shadow w-full max-w-md">
      <h2 class="text-lg font-bold mb-4">Edit Supplier</h2>

      <!-- Name -->
      <label class="block text-sm mb-1">Name</label>
      <input
        v-model="form.name"
        type="text"
        class="w-full border px-3 py-2 rounded mb-1"
        placeholder="Enter name"
      />
      <!-- Contact Number -->      
      <label class="block text-sm mb-1">Contact Number</label>
      <input
        v-model="form.contact_number"
        type="text"
        class="w-full border px-3 py-2 rounded mb-1"
        placeholder="Enter Contact Number"
      />
      <p v-if="form.errors.contact_number" class="text-red-600 text-sm mt-1">
        {{ form.errors.contact_number }}
      </p>


      <!-- Buttons -->
      <div class="flex justify-between mt-4 space-x-2">
        <button @click="emit('close')" class="btn">Cancel</button>
        <button @click="submit" class="px-4 py-2 text-sm bg-green-600 text-white rounded">Update</button>
      </div>
    </div>
  </div>
</template>


<script setup lang="ts">
import { ref, watch,defineEmits } from 'vue'
import { router } from '@inertiajs/vue3'
import { useToast } from 'vue-toastification'
import { useForm,usePage } from '@inertiajs/vue3';
const toast = useToast()
const page = usePage();
const user = page.props.auth.user;
const props = defineProps({
  show: Boolean,
  unit: Object
});

const emit = defineEmits(['close', 'updated']); // Add 'updated'


const form = useForm({
  id: null,
  contact_number: null,
  name: '',
});

watch(() => props.unit, (newUnit) => {
  if (newUnit) {
    form.id = newUnit.id;
    form.name = newUnit.name;
    form.contact_number = newUnit.contact_number;
  }
}, { immediate: true });

// Helper to clean up URL segments
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

const url = cleanUrl(user.name, 'suppliers',form.id);
const submit = () => {
form.put(url+`/${form.id}`, {
  onSuccess: () => {
  emit('updated', { id: form.id, name: form.name, contact_number: form.contact_number, updated_at: new Date().toISOString() });
  emit('close');
  },
  onError: () => {
    // errors are automatically populated into `form.errors`
  }
});
};
</script>
