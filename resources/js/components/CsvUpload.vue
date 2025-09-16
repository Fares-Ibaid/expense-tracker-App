<script setup>
import { ref } from 'vue'
import axios from 'axios'

const file = ref(null)
const successMessage = ref(null)
const error = ref(null)
// toDo - read the categories from backend
const categories = [
  'Lebensmittel',
    'Essen & Trinken',
   'Strom',
    'Transport',
    'Abonnements'
]

const rows = ref([])

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
        // Mark each row with isValid flag
        rows.value = (response.data.data || []).map(row => {
            const isValid = validateRow(row);
            return {
                ...row,
                isValid,
                category: row.category || null
            };
        });

        successMessage.value = response.data.message || 'Upload successful!'
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
</script>

<template>
    <div class="bg-white p-4 shadow rounded mt-6">
        <h2 class="text-lg font-semibold mb-4">Upload Bank CSV</h2>

        <div v-if="successMessage" class="bg-green-100 text-green-800 p-2 rounded mb-4">
            {{ successMessage }}
        </div>

        <div v-if="error" class="bg-red-100 text-red-800 p-2 rounded mb-4">
            {{ error }}
        </div>

        <form @submit.prevent="handleUpload" enctype="multipart/form-data">
            <div class="mb-4">
                <label for="csv" class="block mb-1 font-medium">CSV File</label>
                <input type="file" id="csv" @change="onFileChange" class="w-full border rounded p-2" required />
            </div>

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
                Upload CSV
            </button>
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
                                {{ cat }}
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
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
