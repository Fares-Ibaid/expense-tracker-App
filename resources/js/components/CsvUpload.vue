<script setup>
import { ref , onMounted } from 'vue'
import axios from 'axios'

const successMessage = ref(null);
const error = ref(null);
const categories = ref([]);
const rows = ref([]);

const validateRow = (row) => {
    const isDateValid = row.date && !isNaN(Date.parse(row.date));
    const isDescriptionValid = typeof row.description === 'string' && row.description.trim() !== '';
    const isAmountValid = row.amount !== undefined && !isNaN(row.amount);
    return isDateValid && isDescriptionValid && isAmountValid;
};

const fetchCategoriesAndRules = async () => {
    try {
        const response = await axios.get('/api/categories');
        categories.value = response.data;
    } catch (err) {
        console.error('Failed to fetch categories:', err);
        error.value = 'Failed to load categories.';
    }
};

const fetchTempData = async () => {
    try {
        const response = await axios.get('/api/csv/fetch-mapper');
        rows.value = response.data.map(row => ({
            ...row,
            isValid: validateRow(row),
        }));
        await fetchCategoriesAndRules();
    } catch (err) {
        console.error('Failed to fetch temp data:', err);
        error.value = 'Failed to load data.';
    }
};

const handleSave = async () => {
    const validExpenses = rows.value.filter(row => row.isValid);
    try {
        const response = await axios.post('/api/expenses/save', { expenses: validExpenses });
        successMessage.value = response.data.message || 'Expenses saved successfully!';
        rows.value = [];
    } catch (err) {
        console.error('Saving error:', err);
        error.value = 'Failed to save expenses.';
    }
};

onMounted(fetchTempData);

</script>

<template>
    <div>
        <h2>Preview Data</h2>
        <div v-if="error" class="error">{{ error }}</div>
        <div v-if="successMessage" class="success">{{ successMessage }}</div>
        <div v-if="!rows.length" class="empty-state">No data available to preview.</div>
        <table v-if="rows.length">
            <thead>
            <tr>
                <th>Date</th>
                <th>Description</th>
                <th>Amount</th>
                <th>Category</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(row, index) in rows" :key="index" :class="row.isValid ? 'valid' : 'invalid'">
                <td>{{ row.date }}</td>
                <td>{{ row.description }}</td>
                <td>{{ row.amount }}</td>
                <td>
                    <select v-model="row.category">
                        <option disabled value="">Select</option>
                        <option v-for="cat in categories" :key="cat.id" :value="cat">{{ cat.name }}</option>
                    </select>
                </td>
            </tr>
            </tbody>
        </table>
        <button @click="handleSave" :disabled="!rows.length">Save</button>
    </div>
</template>

<style scoped>
.valid { background-color: #d4edda; }
.invalid { background-color: #f8d7da; }
.error { color: red; margin-bottom: 10px; }
.success { color: green; margin-bottom: 10px; }
.empty-state { text-align: center; color: gray; margin-top: 20px; }
</style>
