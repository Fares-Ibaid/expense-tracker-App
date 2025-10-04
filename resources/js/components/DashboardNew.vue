<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import SummaryCard from "@/components/SummaryCard.vue";
import ChartPanel from "@/components/ChartPanel.vue";
import FiltersPanel from "@/components/FiltersPanel.vue";
import  LoadingOverlay from '@/components/utilities/LoadingOverlay.vue';


// loader overlay
const isLoading = ref(false);

// for the summary card
const expenses = ref([]);
const total = ref(0);
const count = ref(0);


// for chart comp.
const chartData = ref({
    series: [],
    labels: [],
});

// for filters panel

const appliedFilters = ref({});


const fetchInitialChartData = async () => {
    try {
        isLoading.value = true;
        const response = await axios.get('/api/expenses/summary-by-category');
        chartData.value = {
            series: response.data.map(item => Math.abs(parseFloat(item.total))),
            labels: response.data.map(item => item.category),
        };
    } catch (error) {
        console.error('Error fetching initial chart data:', error);
    }finally {
        isLoading.value = false;
    }
};


const fetchFilteredChartData = async (filters) => {
    try {
        isLoading.value = true;
        const response = await axios.get('/api/expenses/summary-by-category', {
            params: filters,
        });
        chartData.value = {
            series: response.data.map(item => Math.abs(parseFloat(item.total))),
            labels: response.data.map(item => item.category),
        };
    } catch (error) {
        console.error('Error fetching filtered chart data:', error);
    }finally {
        isLoading.value = false;
    }
};



const handleAppFilters = (filters) => {
    // Reset date fields if they are empty
    if (!filters.startDate) {
        filters.startDate = null;
    }
    if (!filters.endDate) {
        filters.endDate = null;
    }

    // Fetch filtered chart data based on the provided filters
    fetchFilteredChartData(filters);
};

const resetFilters = () => {
    appliedFilters.value = {}; // Clear all filters
    fetchInitialChartData();
};


onMounted(() => {
    fetchInitialChartData();
});

</script>

<template>
    <!-- summary cards
     , chart-panel ,
     filter-panel
     -->
    <LoadingOverlay
        v-if="isLoading"
        :isLoading="isLoading"
    />
    <!-- 1- summary card -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
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

    <div class="container mt-6 border border-gray-300 rounded p-4">
        <ChartPanel
        :chart-data="chartData"
        />
    </div>

    <div class="container mt-6 border border-gray-300 rounded p-4">
        <FiltersPanel
            target="chart"
            @apply-filters="handleAppFilters"
            @reset-filters="resetFilters"
        />
    </div>
</template>

<style scoped>

</style>
