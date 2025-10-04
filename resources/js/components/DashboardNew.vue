<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import SummaryCard from "@/components/SummaryCard.vue";



// for the summary card
const expenses = ref([]);
const total = ref(0);
const count = ref(0);


// for chart comp.
const chartData = ref({
    series: [],
    labels: [],
});

</script>

<template>
    <!-- summary cards
     , chart-panel ,
     filter-panel
     -->

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
</template>

<style scoped>

</style>
