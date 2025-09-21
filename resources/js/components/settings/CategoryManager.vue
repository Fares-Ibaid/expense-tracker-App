<script setup>
import {ref, onMounted, nextTick} from 'vue'
import ToastNotification from "@/components/utilities/ToastNotification.vue";
import axios from "axios";

// state for categories
const categories = ref([]) ;
const newCategory = ref('');
// editing & deleteing states
const editingCategory = ref(null) ;
const categoryToDelete = ref(null) ;

// toaste state
const showToast = ref(false)
const toastMessage = ref('')
const toastType = ref('success')


// toDo - refactor this
onMounted(async () => {
    await loadCategories()
})

const loadCategories = async() => {
const response = await axios.get('/api/categories')
categories.value = response.data
}

// Add new category
const AddCategory = async() => {
    if(newCategory.value.trim() === ''){
        triggerToast('Enter a category name', 'warning');
        return;
    }
    try {
        const response = await axios.post('/api/categories', { name: newCategory.value });
        await loadCategories();
        newCategory.value = '';
        triggerToast('Category added successfully.', 'success');
    } catch (error) {
        triggerToast('Error adding category.', 'error');
        console.error('Error adding category:', error);
    }
}
// --- editing category

const startEditing = (category) => {
    editingCategory.value = { ...category }; // Create a copy to edit
}

const cancelEditing = () => {
    editingCategory.value = null;
}

const saveEdit = async () => {

    if(!editingCategory.value.name.trim()) return
    try {
        await axios.put(`/api/categories/${editingCategory.value.id}`, { name: editingCategory.value.name });
      await loadCategories();
        editingCategory.value = null;
        triggerToast('Category updated successfully.', 'success');
    } catch (error) {
        triggerToast('Error updating category.', 'error');
        console.error('Error updating category:', error);
    }
}


// delete Category
const askDelete = (category) => {
    console.log('get triggered' , category)
    categoryToDelete.value = category;
    console.log(categoryToDelete.value);
}


//confirm delete

const confirmDelete = async () => {
    if (!categoryToDelete.value) return;
    console.log('delete cofirm', categoryToDelete.value);
    try {
        await axios.delete(`/api/categories/${categoryToDelete.value.id}`);
        triggerToast('Category deleted successfully.', 'success');
        await loadCategories();
    } catch (error) {
        if (error.response && error.response.status === 400) {
            triggerToast(error.response.data.error, 'warning');
        } else {
            triggerToast('Error deleting category.', 'error');
        }
    } finally {
        categoryToDelete.value = null;
    }
};

// uitlity to trigger toast
const triggerToast =  async (message, type = 'success') => {
    toastMessage.value = message;
    toastType.value = type;
    showToast.value = false;

    await nextTick()
    showToast.value = true;
    setTimeout(()=> {
        showToast.value = false;
    }, 2500)
}

</script>

<template>
    <section>
        <h3 class="text-lg font-semibold mb-4">Manage Categories</h3>

        <!-- Add New Category -->
        <div class="flex gap-2 mb-4">
            <input
                type="text"
                placeholder="New category"
                v-model="newCategory"
                class="border rounded p-2 w-full"
            />
            <button
                @click="AddCategory"
                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition"
            >
                Add
            </button>
        </div>

        <!-- Categories List -->
        <ul class="space-y-2">
            <li
                v-for="category in categories"
                :key="category.id"
                class="flex justify-between items-center border-b py-2"
            >
                <!-- Editing Mode -->
                <template v-if="editingCategory && editingCategory.id === category.id">
                    <input
                        v-model="editingCategory.name"
                        class="border px-2 py-1 rounded w-1/2"
                    />
                    <div class="flex gap-2">
                        <button
                            class="text-green-600 text-sm"
                            @click="saveEdit"
                        >
                            Save
                        </button>
                        <button
                            class="text-gray-500 text-sm"
                            @click="cancelEditing"
                        >
                            Cancel
                        </button>
                    </div>
                </template>

                <!-- Normal Mode -->
                <template v-else>
             <span>
               {{ category.name }}
               <span class="px-2 py-1 rounded text-white" :class="`bg-${category.rules.length > 0 ? 'green' : 'gray'}-500`">
                 {{ category.rules.length }}
               </span>
             </span>
                    <div class="flex gap-2">
                        <button
                            class="text-blue-600 text-sm hover:underline"
                            @click="startEditing(category)"
                        >
                            Edit
                        </button>
                        <button
                            class="text-red-600 text-sm hover:underline"
                            @click="askDelete(category)"
                        >
                            Delete
                        </button>
                    </div>
                </template>
            </li>
        </ul>

        <!-- Delete Confirmation -->
        <div
            v-if="categoryToDelete"
            class="mt-6 p-4 bg-red-100 border border-red-300 rounded"
        >
            <p class="mb-2 text-red-700">
                Are you sure you want to delete
                <strong>{{ categoryToDelete.name }}</strong>?
            </p>
            <div class="flex gap-3">
                <button
                    class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700 transition"
                    @click="confirmDelete"
                >
                    Yes, Delete
                </button>
                <button
                    class="bg-gray-300 px-3 py-1 rounded hover:bg-gray-400 transition"
                    @click="categoryToDelete = null"
                >
                    Cancel
                </button>
            </div>
        </div>

        <!-- Toast Notification -->
        <ToastNotification
            v-if="showToast"
            :message="toastMessage"
            :type="toastType"
        />
    </section>
</template>

<style scoped>

</style>
