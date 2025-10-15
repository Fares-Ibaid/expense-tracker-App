<script setup>
import { ref , watch } from "vue";

const props = defineProps({
    chartData: {
        type: Object,
        required: true,
    },
});


// ApexCharts expects:
// - `series` as a separate prop
// - `labels` inside the `options` object
const series = ref([]);
const chartOptions = ref({
    chart: {
        type: 'pie',
    },
    labels: [],
    legend: {
        position: 'bottom',
    },
    title: {
        text: 'Expenses by Category',
        align: 'center',
    },
});



// Watch for changes in chartData and update the chart
watch(
    () => props.chartData,
    (newData) => {
        console.log('chartData:', newData);
        if (newData && newData.series && newData.labels) {
            series.value = newData.series.map(value => Number(value));

            // Reassign the entire chartOptions object to trigger reactivity properly
            chartOptions.value = {
                chart: {
                    type: 'pie',
                },
                labels: newData.labels,
                legend: {
                    position: 'bottom',
                },
                title: {
                    text: 'Expenses by Category',
                    align: 'center',
                },
                tooltip: {
                    y: {
                        formatter: (value) => {
                            return new Intl.NumberFormat('de-DE', {
                                style: 'currency',
                                currency: 'EUR',
                            }).format(value);
                        },
                    },
                },
            };
        }
    },
    { immediate: true }
);
</script>

<template>
    <div>
        <apexchart
            type="pie"
            height="350"
            :options="chartOptions"
            :series="series"
        />
    </div>
</template>

<style scoped>

</style>
