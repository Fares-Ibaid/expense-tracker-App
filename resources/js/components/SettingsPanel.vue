<script setup>
import { ref , onMounted , nextTick  } from 'vue'
import ConfirmModal from "@/components/utilities/ConfirmModal.vue";
import ToastNotification from "@/components/utilities/ToastNotification.vue";
import axios  from "axios";

const showConfirm = ref(false);
const ruleToDelete = ref(null);

// Toast notification
const toastMessage = ref('')
const toastType = ref('success')
const showToast = ref(false)

// Flags
const isEditing = ref(false) ;

const rules = ref([]) ;
const categories = ref([]) ;

const editingRule = ref(null) ;

// form data for track the new rule
const activeRule = ref({
    value: '',
    field: '',
    match_type: '',
    category_id: '',
})


onMounted(async () =>{

    try{
        // fetching categories
        const catResponse = await axios.get('/api/categories')
        categories.value = catResponse.data


        // fetching rules
        const response = await axios.get('/api/rules')
        rules.value = response.data
    }catch (error){
        console.log(error)
    }



})

// add new rule through axios
const addRule = async() => {
    try {
        const response = await axios.post('/api/rules', activeRule.value)
        // update the rules list directly without refetching
        rules.value.push(response.data)
        triggerToast('Rule added successfully.','success')

        activeRule.value = {
            value: '',
            field: '',
            match_type: '',
            category_id: '',
        }
    }catch (error){
        console.log(error)
    }
}

// function called when user confirms deletion in modal
async function deleteRule() {
    if (!ruleToDelete.value) return

    try {
        await axios.delete(`/api/rules/${ruleToDelete.value.id}`)

        rules.value = rules.value.filter(r => r.id !== ruleToDelete.value.id)
        triggerToast('Rule deleted successfully.', 'success')


        showConfirm.value = false
        ruleToDelete.value = null

        // Optionally show success toast here
    } catch (error) {
        console.error(error)

        showConfirm.value = false ;
        triggerToast('Failed to delete rule.', 'error')
        // Optionally show error toast here
    }
}

// ------------------------------------------  update section  --------------------------

// prepare the rule for editing
const startEdit = (rule) => {
    activeRule.value = { ...rule } // clone to avoid live binding
    isEditing.value = true
}

const updateRule = async () => {
    try {
        const { id, ...data } = activeRule.value
        const response = await axios.put(`/api/rules/${id}`, data)

        // Replace in the list
        const index = rules.value.findIndex(r => r.id === id)
        if (index !== -1) rules.value[index] = response.data
        resetForm()
        triggerToast('Rule updated successfully.', 'success')
    } catch (error) {
        console.error('Failed to update rule:', error)
    }
}

// Reset form to default (used after add/edit/cancel)
const resetForm = () => {
    activeRule.value = {
        value: '',
        field: '',
        match_type: '',
        category_id: ''
    }
    isEditing.value = false
}

//  Cancel editing and reset form
const cancelEdit = () => {
    resetForm()
}

// ------------------------------- helper functions ------------------------

// Helper to get category name from ID
const getCategoryName = (id) => {
    const cat = categories.value.find(c => c.id === id)
    return cat ? cat.name : 'Unknown'
}

function  askDeleteRule(rule) {
    ruleToDelete.value = { ...rule }
    showConfirm.value = true
}

// function called when user cancels deletion
function cancelDelete() {
    showConfirm.value = false
    ruleToDelete.value = null
}

function triggerToast(msg, type = 'success') {
    toastMessage.value = msg
    toastType.value = type
    showToast.value = false // reset to allow re-trigger
    nextTick(() => showToast.value = true)
}

</script>

<template>
    <div class="space-y-8">
        <!-- Category Section -->
        <section>
            <h3 class="text-lg font-semibold mb-2">Manage Categories</h3>

            <!-- Add New Category -->
            <div class="flex gap-2 mb-3">
                <input type="text" placeholder="New category" class="border rounded p-2 w-full" />
                <button class="bg-blue-600 text-white px-4 rounded">Add</button>
            </div>

            <!-- Categories List -->
            <ul class="space-y-1">
                <li class="flex justify-between items-center border-b py-1">
                    <span>Groceries</span>
                    <button class="text-red-500 text-sm">Delete</button>
                </li>
                <!-- Repeat... -->
            </ul>
        </section>

        <!-- Rules Section -->
        <section>
            <h3 class="text-lg font-semibold mb-2">Category Rules</h3>

            <!-- Add New Rule -->
            <div class="flex gap-2 mb-3">
                <!-- Keyword input -->
                <input
                    v-model="activeRule.value"
                    type="text"
                    placeholder="Keyword (e.g. Starbucks)"
                    class="border rounded p-2 w-full"
                />

                <!-- Field selection -->
                <select
                    v-model="activeRule.field"
                    class="border rounded p-2 w-full"
                >
                    <option disabled selected>Select field</option>
                    <option value="description">Description</option>
                    <option value="merchant">Merchant</option>
                    <option value="amount">Amount</option>
                </select>

                <!-- Match Type dropdown -->
                <select
                    v-model="activeRule.match_type"
                    class="border rounded p-2 w-full">
                    <option disabled selected>Match Type</option>
                    <option value="contains">Contains</option>
                    <option value="equals">Equals</option>
                    <option value="regex">Regex</option>
                </select>

                <!-- Category dropdown -->
                <select
                    v-model="activeRule.category_id"
                    class="border rounded p-2 w-full"
                >
                    <option disabled selected>Select category</option>
                    <option
                    v-for="category in categories"
                    :key="category.id"
                    :value="category.id"
                    >
                     {{ category.name }}
                    </option>

                </select>

                <!-- ✅ Add or Save Button -->
                <button
                    @click="isEditing ? updateRule() : addRule()"
                    class="px-4 rounded text-white"
                    :class="isEditing ? 'bg-green-600' : 'bg-blue-600'"
                >
                    {{ isEditing ? 'Save' : 'Add' }}
                </button>

                <!-- ✅ Cancel Button (only in edit mode) -->
                <button
                    v-if="isEditing"
                    @click="cancelEdit"
                    class="text-gray-600 px-2"
                >
                    Cancel
                </button>
            </div>

            <!----------------------------  Rules List ----------------->
            <ul class="space-y-1">
                <li
                    v-for="rule in rules"
                    :key="rule.id"
                    class="flex justify-between items-center border-b py-1"
                >
                <span>
                “{{ rule.value }}” ({{ rule.match_type }}) in {{ rule.field }} →
                <strong>{{ rule.category?.name ?? 'Unknown' }}</strong>
                </span>
                    <button @click="startEdit(rule)" class="text-blue-500 text-sm">Edit</button>
                    <button @click="askDeleteRule(rule)" class="text-red-500 text-sm">Delete</button>
                </li>
                <!-- Repeat... -->
            </ul>
            <!-- Confirmation Modal -->
            <ConfirmModal
                v-if="showConfirm"
                :message="ruleToDelete.value ? `Are you sure you want to delete rule  : ${ruleToDelete.value}?` : ''"


                @confirm="deleteRule"
                @cancel="cancelDelete"
            />

            <!--  Toast Notification  -->
            <ToastNotification
                v-if="showToast"
                :message="toastMessage"
                :type="toastType"
            />
        </section>
    </div>

</template>

<style scoped>
div {
    border: 1px solid #ccc;
    margin-bottom: 16px;
    padding: 16px;
}
</style>
