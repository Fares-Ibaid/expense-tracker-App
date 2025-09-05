<script setup>
import VueApexCharts from "vue3-apexcharts";
import { ref } from "vue";

const chartOptions = ref({
    chart: {
        type: "pie", // Change chart type to pie
    },
    series: [10, 20, 30, 40], // Data for the pie chart
    labels: ["Category 1", "Category 2", "Category 3", "Category 4"], // Labels for the pie chart
});
</script>

<template>
    <div>
        <apexchart type="pie" height="350" :options="chartOptions.chart" :series="chartOptions.series" :labels="chartOptions.labels" />
    </div>
</template>

<style scoped>

</style>
