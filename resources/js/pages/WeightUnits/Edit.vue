<template>
  <div v-if="show" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded shadow w-full max-w-md">
      <h2 class="text-lg font-bold mb-4">Edit Weight Unit</h2>

      <!-- Name -->
      <label class="block text-sm mb-1">Name</label>
      <input
        v-model="form.name"
        type="text"
        class="w-full border px-3 py-2 rounded mb-1"
        placeholder="Enter name"
      />
      <p v-if="form.errors.name" class="text-red-600 text-sm mt-1">
        {{ form.errors.name }}
      </p>


      <!-- Buttons -->
      <div class="flex justify-end mt-4 space-x-2">
        <button @click="emit('close')" class="px-4 py-2 bg-gray-300 rounded">Cancel</button>
        <button @click="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Save</button>
      </div>
    </div>
  </div>
</template>


<script setup lang="ts">
import { ref, watch,defineEmits } from 'vue'
import { router } from '@inertiajs/vue3'
import { useToast } from 'vue-toastification'
import { useForm } from '@inertiajs/vue3';
const toast = useToast()

const props = defineProps({
  show: Boolean,
  unit: Object
});

const emit = defineEmits(['close'])

const form = useForm({
  id: null,
  name: '',
});

watch(() => props.unit, (newUnit) => {
  if (newUnit) {
    form.id = newUnit.id;
    form.name = newUnit.name;
  }
}, { immediate: true });

const submit = () => {
form.put(`/weight-units/${form.id}`, {
  onSuccess: () => {
    emit('close');
  },
  onError: () => {
    // errors are automatically populated into `form.errors`
  }
});
};
</script>
