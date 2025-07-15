<script setup lang="ts">
import AuthLayout from '@/layouts/auth/AuthSimpleLayout.vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const notifications = page.props.notifications || [];

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div v-if="notifications.length > 0" class="mb-4 space-y-2">
      <div
        v-for="notification in notifications"
        :key="notification.id"
        class="bg-yellow-100 text-yellow-900 border border-yellow-300 p-2 rounded"
      >
        {{ notification.data.message }}
      </div>
    </div>
    <AuthLayout :title="title" :description="description">
        <slot />
    </AuthLayout>
</template>
