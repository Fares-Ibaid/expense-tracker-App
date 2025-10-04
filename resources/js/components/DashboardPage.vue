<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import TableView from "@/components/TableView.vue";
import CsvUpload     from "@/components/CsvUpload.vue";
import FiltersPanel from "@/components/FiltersPanel.vue";
import SettingsPanel from "@/components/SettingsPanel.vue";
import ChartPanel from "@/components/ChartPanel.vue";
import LoadingOverlay from '@/components/utilities/LoadingOverlay.vue';
import Navbar from "@/components/Navbar.vue";
import  SummaryCard from "@/components/SummaryCard.vue";


const chartData = ref({
    series: [],
    labels: [],
});

const isLoading = ref(false);
// toDo - adjust the code to be based on the ajax reauest

/*const startLoading = () => {
    isLoading.value = true;
    setTimeout(() => {
        isLoading.value = false; // Simulate loading completion
    }, 3000)*/

const expenses = ref([]);
const total = ref(0);
const count = ref(0);

/* ref for the paginations  */
const currentPage = ref(1);
const perPage     = ref(10);
const totalPages  = ref(0);

const appliedFilters = ref({});

// fetch expenses data on component mount and apply filters if any
const fetchExpenses = async (filters = {} , target = 'table') => {
    try {

        if(target === 'table') {

        const response = await axios.get('/api/dashboard', {
            params: {
                page: currentPage.value,
                per_page: perPage.value,
                ...filters,
            },
        });

        // Extract the data key for rows
        expenses.value = response.data.expenses.data; // Extract the array of expenses
        total.value = response.data.total;
        count.value = response.data.count;
        totalPages.value = response.data.expenses.last_page;
        }else if(target === 'chart') {
            const response = await axios.get('/api/expenses/summary-by-category', {
                params: filters,
            });

            chartData.value = {
                series: response.data.map(item => Math.abs(parseFloat(item.total))),
                labels: response.data.map(item => item.category),
            };

        }

       // console.log('Fetched expenses:', expenses.value); // Debugging the assigned value

    } catch (error) {
        console.error('Error fetching expenses:', error);
    }
};

onMounted(async () => {

    fetchExpenses({} , 'chart'); // Fetch initial chart data without filters

    fetchExpenses({}, 'table');
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

// toggle filter panel
const showFilters = ref(false);
const showChartFilters = ref(true);
const toggleFiltersPanel = () =>{
    showFilters.value = !showFilters.value ;
}

// Handle filters applied from FiltersPanel
const handleAppFilters = ({target , filters}) => {
   // console.log('target:', target); // Debugging the filters

    // Reset date fields if they are empty
    if (!filters.startDate) {
        filters.startDate = null;
    }
    if (!filters.endDate) {
        filters.endDate = null;
    }

    if (target === 'table') {
        appliedFilters.value = filters; // Store the applied filters for the table
        fetchExpenses(filters, 'table'); // Fetch data for the table
    } else if (target === 'chart') {
        fetchExpenses(filters, 'chart'); // Fetch data for the chart
    }
};

const resetFilters = () => {
    appliedFilters.value = {}; // Clear all filters
    fetchExpenses({} , 'table'); // Fetch data without filters
};
</script>

<template>
    <div>
        <LoadingOverlay :isLoading="isLoading" />
        <navbar/>

        <SettingsPanel />

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <SummaryCard
                title="Total Expenses"
                :value="new Intl.NumberFormat('de-DE', { style: 'currency', currency: 'EUR' }).format(total)"
                valueClass="text-2xl font-bold text-green-600"
            />
            <SummaryCard
                title="Number of Expenses"
                :value="count"
                valueClass="text-2xl font-bold text-blue-600"
            />
        </div>

        <!-- --------------- Chart panel  ------------->
        <div>
            <ChartPanel
            :chart-data = "chartData"
            />

            <div v-if="showChartFilters" class="pt-4">
                <FiltersPanel
                    target="chart"
                    @apply-filters="handleAppFilters"
                    @reset-filters="resetFilters"
                />
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
                target="table"
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

    </div>
</template>

<style scoped>

</style>
