<template>
  <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
      <h2 class="text-xl font-semibold mb-4">Edit Role</h2>

      <form @submit.prevent="submit">
        <div class="mb-4">
          <label class="block mb-1 font-medium">Name</label>
          <input v-model="form.name" type="text" class="w-full border rounded px-3 py-2" />
        </div>

        <div class="flex justify-between space-x-2">
          <button type="button" class="btn" @click="$emit('close')">Cancel</button>
          <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Update</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import { useToast } from 'vue-toastification';

const emit = defineEmits(['close']);
const props = defineProps({
  role: Object
});
const toast = useToast();

const form = ref({
  name: ''
});

watch(() => props.role, (val) => {
  if (val) {
    form.value.name = val.name;
  }
}, { immediate: true });

const submit = () => {
  router.put(`/admin/roles/${props.role.id}`, form.value, {
    onSuccess: () => {
const updatedRole = {
  id: props.role.id,
  name: form.value.name,
  updated_at: new Date().toISOString(), // fallback
}
emit('updated', updatedRole)


      toast.success('Role updated successfully!');
      emit('updated', updatedRole); // ✅ send updated data to parent
      emit('close');
    }
  });
};

</script>
