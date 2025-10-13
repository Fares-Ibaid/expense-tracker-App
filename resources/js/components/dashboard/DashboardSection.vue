<script setup>
import { ref, onMounted } from 'vue';
import SummaryCard from "@/components/dashboard/summary/SummaryCard.vue";
import ChartPanel from "@/components/dashboard/chart/ChartPanel.vue";
import FiltersPanel from "@/components/utilities/FiltersPanel.vue";
import  LoadingOverlay from '@/components/utilities/LoadingOverlay.vue';
import { useDashboardApi } from "@/composables/useDashboardApi.js";

// toDo -  <!-- summary cards , chartpanel , filterpanel  -->
// Use the composable
const {
    isLoading,
    chartData,
    totalExpenses,
    numberOfExpenses,
    fetchInitialData,
    fetchFilteredData
} = useDashboardApi();

// Handle filters
const handleAppFilters = (filters) => {
    if (!filters.startDate) filters.startDate = null;
    if (!filters.endDate) filters.endDate = null;
    fetchFilteredData(filters);
};

const resetFilters = () => {
    fetchInitialData();
};

// Fetch initial data on mount
onMounted(() => {
    fetchInitialData();
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
            :value="new Intl.NumberFormat('de-DE', { style: 'currency', currency: 'EUR' }).format(totalExpenses)"
            valueClass="text-2xl font-bold text-green-600"
        />

        <SummaryCard
            title="Number of Expenses"
            :value="numberOfExpenses"
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
