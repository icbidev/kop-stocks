<script setup lang="ts">
import { ref, watch, onMounted } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { useToast } from 'vue-toastification';

const toast = useToast();
const page = usePage();
const user = usePage().props.auth.user
const products = ref<Product[]>(page.props.products);
const suppliers = ref<Supplier[]>(page.props.supplier);
const categories = ref<Category[]>(page.props.category);
const weight_units = ref<WeightUnit[]>(page.props.weight_units);
// Sample props for categories and initial product
const props = defineProps({
  categories: Array,
  product: Object
});

const showEditModal = ref(false);
const product = ref(props.product || {});
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


const productForm = useForm({
  name: '',
  category_id: '',
  standard_order: 0,
  weight_unit_id: [],
  minimum_quantity: 0,
  supplier_ids: [], // Initialize supplier_ids as an empty array
  showSuppliers: false, // Handle showSuppliers in form state
});

onMounted(() => {
  if (page.props.flash?.success) {
    toast.success(page.props.flash.success);
  }
  if (page.props.flash?.error) {
    toast.error(page.props.flash.error);
  }
});

watch(() => props.product, (newProduct) => {
  if (newProduct) {
    productForm.name = newProduct.name;
    productForm.supplier_ids = newProduct.supplier_ids; 
    productForm.weight_unit_id = newProduct.weight_unit_id; 
    productForm.minimum_quantity = newProduct.minimum_quantity; 
    productForm.standard_order = newProduct.standard_order ;
    productForm.category_id = newProduct.category_id ; 
  }
}, { immediate: true });


// Open modal and fill form with product data
function openEditModal(selectedProduct) {
  productForm.name = selectedProduct.name;
  productForm.category_id = selectedProduct.category_id;
      productForm.weight_unit_id = selectedProduct.weight_unit_id;
    productForm.standard_order = selectedProduct.standard_order;
  productForm.minimum_quantity = selectedProduct.minimum_quantity;
  productForm.supplier_ids = selectedProduct.supplier_ids || []; // Populate suppliers
  productForm.showSuppliers = false; // Reset showSuppliers for new modal open
  showEditModal.value = true;
}

// Submit product update
function submitProductUpdate() {
  const urlEdit = cleanUrl(user.name, 'products');
  productForm.put(urlEdit+`/${product.value.id}/edit-product`, {
    onSuccess: (response) => {
      productForm.reset();
      
const selectedProduct = response.props.products.find(p => p.id === product.value.id);
if (selectedProduct) {
  productForm.name = selectedProduct.name;
  productForm.weight_unit_id = selectedProduct.weight_unit_id;
  productForm.category_id = selectedProduct.category_id;
  productForm.standard_order = selectedProduct.standard_order;
  productForm.minimum_quantity = selectedProduct.minimum_quantity;
}
      toast.success('Updated product successfully!');
      showEditModal.value = false;
    }
  });
}
</script>

<template>
  <button
    @click="openEditModal(product)"
    class="px-2 py-1 bg-blue-500 text-white rounded"
  >
    Edit
  </button>

  <div
    v-if="showEditModal"
    class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
  >
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
      <h2 class="text-lg font-semibold mb-4 text-black">Edit Product</h2>

      <form @submit.prevent="submitProductUpdate" class="space-y-4 text-black">
        <div>
          <label for="name" class="block font-semibold mb-1">Product Name</label>
          <input
            v-model="productForm.name"
            id="name"
            type="text"
            class="border rounded px-3 py-2 w-full"
            :class="{ 'border-red-500': productForm.errors.name }"
            autofocus
          />
          <p v-if="productForm.errors.name" class="text-red-600 text-sm mt-1">
            {{ productForm.errors.name }}
          </p>
        </div>

        <!-- Supplier Handling -->
        <div class="mb-4 border p-3 rounded shadow">
          <label for="name" class="block font-semibold mb-1">Choose Suppliers</label>
          <div  class="ml-4 mt-2 max-h-60 overflow-y-auto border p-2 rounded">
            <label v-for="supplier in suppliers" :key="supplier.id" class="block">
              <input
                type="checkbox"
                :value="supplier.id"
                v-model="productForm.supplier_ids"
              />
              {{ supplier.name }}
            </label>
          </div>
        </div>

        <!-- Category Selection -->
        <div>
          <label for="category" class="block font-semibold mb-1">Category</label>
          <select
            v-model="productForm.category_id"
            id="category_id"
            class="border rounded px-3 py-2 w-full"
            :class="{ 'border-red-500': productForm.errors.category_id }"
          >
            <option
              v-for="category in categories"
              :key="category.id"
              :value="category.id"
            >
              {{ category.name }}
            </option>
          </select>
          <p v-if="productForm.errors.category_id" class="text-red-600 text-sm mt-1">
            {{ productForm.errors.category_id }}
          </p>
        </div>
        <!-- Weight Unit Selection -->
        <div>
          <label for="weight_unit" class="block font-semibold mb-1">Weight Unit</label>
          <select
            v-model="productForm.weight_unit_id"
            id="weight_unit_id"
            class="border rounded px-3 py-2 w-full"
            :class="{ 'border-red-500': productForm.errors.weight_unit_id }"
          >
            <option
              v-for="weight_unit in weight_units"
              :key="weight_unit.id"
              :value="weight_unit.id"
            >
              {{ weight_unit.name }}
            </option>
          </select>
          <p v-if="productForm.errors.weight_unit_id" class="text-red-600 text-sm mt-1">
            {{ productForm.errors.weight_unit_id }}
          </p>
        </div>
        <!-- Standard Order -->
        <div>
          <label for="standard_order" class="block font-semibold mb-1">Standard Order</label>
          <input
            v-model="productForm.standard_order"
            id="minimum_quantity"
            step="any"
            type="number"
            class="border rounded px-3 py-2 w-full"
            :class="{ 'border-red-500': productForm.errors.standard_order }"
          />
          <p v-if="productForm.errors.standard_order" class="text-red-600 text-sm mt-1">
            {{ productForm.errors.standard_order }}
          </p>
        </div>
        <!-- Minimum Balance -->
        <div>
          <label for="minimum_quantity" class="block font-semibold mb-1">Minimum Balance</label>
          <input
            v-model="productForm.minimum_quantity"
            id="minimum_quantity"
            step="any"
            type="number"
            class="border rounded px-3 py-2 w-full"
            :class="{ 'border-red-500': productForm.errors.minimum_quantity }"
          />
          <p v-if="productForm.errors.minimum_quantity" class="text-red-600 text-sm mt-1">
            {{ productForm.errors.minimum_quantity }}
          </p>
        </div>

        <!-- Submit and Cancel Buttons -->
        <div class="flex justify-between items-center pt-4">
          <button
            type="button"
            class="text-gray-600 hover:underline"
            @click="showEditModal = false"
          >
            Cancel
          </button>
                    <button
            type="submit"
            class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700"
            :disabled="productForm.processing"
          >
            Update
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
