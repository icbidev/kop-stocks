<template>
  <div class="relative w-full" @click.outside="isOpen = false">
    <button
      @click="isOpen = !isOpen"
      class="w-full border border-gray-300 rounded px-3 py-2 text-left bg-white"
    >
      <span v-if="selectedNames.length">
        {{ selectedNames.join(', ') }}
      </span>
      <span v-else class="text-gray-400">Select suppliers</span>
    </button>

    <div
      v-show="isOpen"
      class="absolute mt-1 w-full max-h-60 overflow-y-auto border border-gray-300 bg-white shadow-md z-50 rounded"
    >
      <label
        v-for="supplier in options"
        :key="supplier.id"
        class="flex items-center px-3 py-2 hover:bg-gray-100 cursor-pointer"
      >
        <input
          type="checkbox"
          :value="supplier.id"
          v-model="modelValue"
          class="form-checkbox h-4 w-4 text-blue-600"
        />
        <span class="ml-2">{{ supplier.name }}</span>
      </label>
    </div>
  </div>
</template>

<script setup lang="ts">
defineProps<{
  modelValue: number[];
  options: { id: number; name: string }[];
}>();
defineEmits(['update:modelValue']);

const selectedNames = computed(() =>
  props.options
    .filter(opt => props.modelValue.includes(opt.id))
    .map(opt => opt.name)
);
</script>
