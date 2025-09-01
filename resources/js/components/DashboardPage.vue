<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import TableView from "@/components/TableView.vue";
import CsvUpload     from "@/components/CsvUpload.vue";
import FiltersPanel from "@/components/FiltersPanel.vue";
import SettingsPanel from "@/components/SettingsPanel.vue";

const expenses = ref([]);
const total = ref(0);
const count = ref(0);

/* ref for the paginations  */
const currentPage = ref(1);
const perPage     = ref(10);
const totalPages  = ref(0);

const appliedFilters = ref({});

// fetch expenses data on component mount and apply filters if any
const fetchExpenses = async (filters = {}) => {
    try {
        const response = await axios.get('/api/dashboard', {
            params: {
                page: currentPage.value,
                per_page: perPage.value,
                ...filters,
            },
        });

        console.log('API Response:', response.data); // Debugging the full response

        // Extract the data key for rows
        expenses.value = response.data.expenses.data; // Extract the array of expenses
        total.value = response.data.total;
        count.value = response.data.count;
        totalPages.value = response.data.expenses.last_page;

        console.log('Fetched expenses:', expenses.value); // Debugging the assigned value
    } catch (error) {
        console.error('Error fetching expenses:', error);
    }
};

onMounted(async () => {

    fetchExpenses();
});

// Handle page change
const goToPage = async (page) => {
    if (page >= 1 && page <= totalPages.value) {
        await fetchExpenses({ ...appliedFilters.value, page });
        currentPage.value = page;
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

// toggle filter panel
const showFilters = ref(false);
const toggleFiltersPanel = () =>{
    showFilters.value = !showFilters.value ;
}

// Handle filters applied from FiltersPanel
const handleAppFilters = (filters) => {
    console.log('Applied Filters:', filters); // Debugging the filters

    // Reset date fields if they are empty
    if (!filters.startDate) {
        filters.startDate = null;
    }
    if (!filters.endDate) {
        filters.endDate = null;
    }

    appliedFilters.value = filters; // Store the applied filters
    fetchExpenses(filters); // Fetch data with the new filters
};

const resetFilters = () => {
    appliedFilters.value = {}; // Clear all filters
    fetchExpenses(); // Fetch data without filters
};
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

        <!--  toggle filters panel -->
     <button
         class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 mt-4"
         @click="toggleFiltersPanel"
     >
            <label>
                {{ showFilters ? 'Hide Filters' : 'Show Filters' }}
            </label>
        </button>
        <!-- -- Filters Panel Component ------------->
        <div v-if="showFilters" class="pt-4">
            <FiltersPanel
                @apply-filters="handleAppFilters"
                @reset-filters="resetFilters"
            />
        </div>

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
