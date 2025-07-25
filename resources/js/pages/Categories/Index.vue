<script setup>
import { ref } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import { useForm,Head } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue';
const categories = ref(usePage().props.categories)
const showModal = ref(false)
const editing = ref(null)
const page = usePage();
const user = page.props.auth.user;
const form = useForm({
  name: ''
})
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
const openModal = (category = null) => {
  editing.value = category
  form.name = category?.name || ''
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  form.reset()
  editing.value = null
}

const submit = () => {
  if (editing.value) {
    // UPDATE category
    const urlUpdate = cleanUrl(user.name, 'categories', editing.value.id);;
    form.put(urlUpdate+`/${editing.value.id}`, {
      onSuccess: () => {
        // Update the item in the list
        const index = categories.value.findIndex(c => c.id === editing.value.id)
        if (index !== -1) {
          categories.value[index].name = form.name
        }
        closeModal()
      }
    })
  } else {
    // CREATE category
        const urlUpdate = cleanUrl(user.name, 'categories');
    form.post(urlUpdate, {
      preserveScroll: true,
      onSuccess: (response) => {
        // If the server sends back the new category
        // Replace `response.props.category` with actual response if needed
        const newCategory = response?.props?.category || form.data()
        categories.value.unshift({
          id: categories.value[categories.length - 1], // If not available, you may need to reload
          name: form.name
        })
        closeModal()
      }
    })
  }
}


const destroy = (id) => {
  if (confirm('Are you sure?')) {
            const urlUpdate = cleanUrl(user.name, 'categories');
    router.delete(urlUpdate+`/${id}`, {
      onSuccess: () => {
        categories.value = categories.value.filter(c => c.id !== id)
      }
    })
  }
}

</script>

<template>
  <Head title="Categories" />

  <AppLayout :breadcrumbs="breadcrumbs">
  <div class="p-6 space-y-4">
    <div class="flex justify-between items-center">
      <h1 class="text-xl font-bold">Categories</h1>
      <button @click="openModal()" class="bg-green-600 text-white px-4 py-2 rounded">+ Add</button>
    </div>

    <table class="w-full table-auto border rounded shadow text-sm text-center">
      <thead class="bg-gray-100">
        <tr>
          <th class="border px-3 py-2 text-bold">Name</th>
          <th class="border px-3 py-2 text-bold">Action</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="category in categories" :key="category.id" class="bg-white even:bg-gray-50">
          <td class="border px-3 py-2 text-bold">{{ category.name }}</td>
          <td class="border px-3 py-2 space-x-2 flex justify-center">
            <button @click="openModal(category)" class="px-2 py-1 bg-blue-500 text-white rounded">Edit</button>
            <button @click="destroy(category.id)" class="px-2 py-1 bg-red-600 text-white rounded">Delete</button>
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
      <div class="bg-white p-6 rounded w-full max-w-sm">
        <h2 class="text-lg font-bold mb-4">
          {{ editing ? 'Edit Category' : 'Add Category' }}
        </h2>

        <input
          v-model="form.name"
          type="text"
          class="w-full border px-3 py-2 rounded"
          placeholder="Category name"
        />
        <p v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</p>

        <div class="flex justify-between mt-4 space-x-2">
          <button @click="closeModal" class="btn">Cancel</button>
          <button @click="submit" class="px-4 py-2 bg-green-600 text-white rounded">
            {{ editing ? 'Update' : 'Create' }}
          </button>
        </div>
      </div>
    </div>
  </div>
  </AppLayout>
</template>
