<script setup>
import { ref, defineEmits } from 'vue';
import axios from 'axios'

const file = ref(null)
const successMessage = ref(null)
const error = ref(null)
const categories = ref([])  // will be fetched from backend
const rows = ref([])


// emit event to parent component to refresh expenses list after successful upload
const emit = defineEmits(['save-success']);

const onFileChange = (event) => {
    file.value = event.target.files[0]
    successMessage.value = null
    error.value = null
    rows.value = []
}

const handleUpload = async () => {
    if (!file.value) return

    const formData = new FormData() // need it because we are sending binary file  ( as multipart/form-data)
    formData.append('csv', file.value)

    try {
        const response = await axios.post('api/expenses/upload', formData) ;
        const parseRows = response.data.data || [];

        // Fetch categories/rules after upload
        await fetchCategoriesAndRules();

       // Map rows and ensure auto-categorized categories are selected
        rows.value = parseRows.map(row => {
            const isValid = validateRow(row); // validate each row

            // Match the category from the fetched categories
            const matchedCategory = categories.value.find(
                (cat) => cat.name === row.category
            );

            return {
                ...row,
                isValid,
                category: matchedCategory || null
            };
        });

       successMessage.value = response.data.message || 'Upload successful!';
       setTimeout(() => {
           successMessage.value = null;
       }, 2000); // Hide after 5 seconds
        file.value = null
        error.value = null

    } catch (err) {
        console.error('Upload failed:', err)
        error.value = err.response?.data?.message || 'Something went wrong.'
        rows.value = []
    }
}

// Handle saving  parsed data to expenses
const handleSave = async() => {

    const validExpenses = rows.value.filter(row => row.description && row.description.trim() !== '')

    try {
        //console.log('Sending expenses:', rows.value) ;

        // sending data as json
        const response = await axios.post('api/expenses/save',{
            expenses : validExpenses
        })

        successMessage.value = response.data.message || 'Expenses saved successfully!'
        error.value = null
        rows.value = []

        // Emit the event to notify the parent
        emit('save-success');

    } catch (err) {
        error.value = err.response?.data?.message || 'Saved failed.' // return undefined is not set
        console.error('Saving error:', err)
    }
}


// ------------------- Helper function to validate rows
const validateRow = (row) => {
    const isDateValid = row.date && !isNaN(Date.parse(row.date)); // Check if date is valid
    const isDescriptionValid = typeof row.description === 'string' && row.description.trim() !== '';
    const isAmountValid = row.amount !== undefined && !isNaN(row.amount); // Ensure amount is a valid number
    return isDateValid && isDescriptionValid && isAmountValid; // Combine all checks
};

// Fetch categories/rules
const fetchCategoriesAndRules = async () => {
    try {
        const response = await axios.get('api/categories');
        categories.value = response.data; // Update the categories state
    } catch (err) {
        console.error('Failed to fetch categories:', err);
    }
};


</script>

<template>
    <div class="bg-white p-4 shadow rounded mt-6">
        <div v-if="successMessage" class="bg-green-100 text-green-800 p-2 rounded mb-4">
            {{ successMessage }}
        </div>

        <div v-if="error" class="bg-red-100 text-red-800 p-2 rounded mb-4">
            {{ error }}
        </div>

        <form @submit.prevent="handleUpload" enctype="multipart/form-data">
        <div class="mb-4 flex flex-col items-center justify-center">
            <h2 class="text-lg font-semibold mb-4">Upload a Bank CSV file </h2>
            <div class="flex items-center">
                <input type="file" id="csv" @change="onFileChange" class="border rounded p-2" required />

                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-blue-600 ml-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5V18a2.25 2.25 0 0 0 2.25 2.25h13.5A2.25 2.25 0 0 0 21 18v-1.5M7.5 10.5 12 6m0 0 4.5 4.5M12 6v12" />
                </svg>
            </div>
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded mt-4">
                Upload CSV
            </button>
        </div>


        </form>

        <!-- Preview Table -->
        <div v-if="rows.length > 0" class="mt-6">
            <h3 class="font-semibold text-lg mb-2">Preview Parsed Data</h3>
            <!-- ------------------------ Table for displaying parsed CSV data ------------------------ -->
            <table class="w-full text-sm border">
                <thead class="bg-gray-100">
                <tr>
                    <th class="border px-2 py-2 text-center align-middle">Date</th>
                    <th class="border px-2 py-2 text-center align-middle">Description</th>
                    <th class="border px-2 py-2 text-center align-middle">Amount</th>
                    <th class="border px-2 py-2 text-center align-middle">Category</th>
                </tr>
                </thead>
                <tbody>
                <tr
                    v-for="(row, index) in rows"
                    :key="index":class="[
                        'border-b',
                        row.duplicated ? 'bg-yellow-100' : (row.isValid ? 'bg-green-800 text-white hover:bg-green-600' : 'bg-red-50 text-red-800')
                    ]"

                    @click="console.log('Row:', row, 'Duplicated:', row.duplicated)"
                >
                    <td class="border px-2 py-2 text-center align-middle">{{ row.date || '—' }}</td>

                    <td class="border px-2 py-2 text-center align-middle">{{ row.description || '—' }}
                        <!-- Show icon if row is duplicated -->
                        <template v-if="row.duplicated">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 0 1 1.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 0 0-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 0 1-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H9.75" />
                            </svg>
                        </template>
                    </td>
                    <td class="border px-2 py-2 text-center align-middle">
                        {{ row.amount !== undefined ? row.amount.toFixed(2) : '—' }}
                    </td>
                    <!-- resolve category rules  -->
                    <td
                        class="border px-2 py-2 text-center align-middle"
                     :class="row.isValid ? 'text-black' : ''"
                    >
                        <select
                            v-model="row.category"
                            class="w-full border rounded p-1"
                        >
                            <option disabled value="">Select a category</option>
                            <option v-for="cat in categories" :key="cat" :value="cat">
                                {{ cat.name }}
                            </option>
                        </select>
                    </td>
                </tr>
                </tbody>
            </table>
            <!-- Save Button -->
            <div class="mt-4 text-right" v-if="rows.length > 0">
                <button
                    @click="handleSave"
                    class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700"
                >
                    Save to Expenses
                </button>

                <button
                    @click="$emit('reset')"
                    class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 ml-2"
                >
                    Cancel
                </button>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
