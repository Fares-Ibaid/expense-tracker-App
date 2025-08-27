<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import TableView from "@/components/TableView.vue";
import CsvUpload     from "@/components/CsvUpload.vue";
import SettingsPanel from "@/components/SettingsPanel.vue";

const expenses = ref([]);
const total = ref(0);
const count = ref(0);

/* ref for the paginations  */
const currentPage = ref(1);
const perPage     = ref(10);
const totalPages  = ref(0);

onMounted(async () => {
    const response = await
    axios.get(`/api/dashboard?page=${currentPage.value}&per_page=${perPage.value}`);
    expenses.value = response.data.expenses.data;
    total.value = response.data.total;
    count.value = response.data.count;
    totalPages.value = response.data.expenses.last_page;
});
// handle page change
const goToPage = async (page) => {
    if (page >= 1 && page <= totalPages.value) {
        const response = await axios.get(`/api/dashboard?page=${page}&per_page=${perPage.value}`);
        expenses.value = response.data.expenses.data;
        currentPage.value = response.data.expenses.current_page;
    }
};

// define the columns for the table-view
const columns = [
    { key: 'description', label: 'Description' },
    {
        key: 'category',
        label: 'Category',
        formatter: (value) => value ? value.name : 'No Category'
    },
    { key: 'amount', label: 'Amount (€)' },
    { key: 'date', label: 'Date' },
];

// show settings panel
const showSettings = ref(true)

const toggleSettingsPanel = () => {
    showSettings.value = !showSettings.value
}
</script>

<template>
    <div>
        <h1 class="text-3xl font-bold mb-6">💸 Expense Tracker Dashboard</h1>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white p-4 shadow rounded">
                <h2 class="text-lg font-semibold">Total Expenses</h2>
                <p class="text-2xl font-bold text-green-600">{{ new Intl.NumberFormat('de-DE', { style: 'currency', currency: 'EUR' }).format(total) }}</p>
            </div>
            <div class="bg-white p-4 shadow rounded">
                <h2 class="text-lg font-semibold">Number of Expenses</h2>
                <p class="text-2xl font-bold text-blue-600">{{ count }}</p>
            </div>
        </div>

        <!-- -- CSV Upload Component ------------->
        <CsvUpload />

        <!-------------------------------- table-view  ------------>
        <div class="mt-6">
            <h2 class="text-xl font-semibold mb-4">Expenses</h2>
            <TableView :columns="columns" :rows="expenses" />
        </div>

        <!-- Pagination Section  -->
        <div class="flex justify-center mt-4">
            <button
                class="px-4 py-2 mx-1 bg-gray-200 rounded hover:bg-gray-300"
                :disabled="currentPage === 1"
                @click="goToPage(currentPage - 1)"
            >
                Previous
            </button>
            <span class="px-4 py-2 mx-1">{{ currentPage }} / {{ totalPages }}</span>
            <button
                class="px-4 py-2 mx-1 bg-gray-200 rounded hover:bg-gray-300"
                :disabled="currentPage === totalPages"
                @click="goToPage(currentPage + 1)"
            >
                Next
            </button>
        </div>

        <!-- Right-Side Settings Panel (slide-over style) -->
        <div v-if="showSettings" class="w-80 bg-white shadow-lg p-4 border-l">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold">Settings</h2>
                <button @click="toggleSettingsPanel" class="text-gray-600 hover:text-gray-800">x</button>
            </div>

            <SettingsPanel />
        </div>
    </div>
</template>

<style scoped>

</style>
