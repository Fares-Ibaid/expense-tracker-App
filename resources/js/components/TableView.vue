<script setup>
import { defineProps } from 'vue';

const props = defineProps({
    columns: {
        type: Array,
        required: true,
    },
    rows: {
        type: Array,
        required: true,
    },
});

</script>

<template>
    <div class="overflow-x-auto">
        </div>
        <table class="table-auto w-full border-collapse border border-gray-300">
            <thead>
            <tr>
                <th v-for="column in columns" :key="column.key" class="border border-gray-300 px-4 py-2 text-left">
                    {{ column.label }}
                </th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(row, rowIndex) in rows" :key="rowIndex">
                <td v-for="column in columns" :key="column.key" class="border border-gray-300 px-4 py-2">
                    {{ column.formatter ? column.formatter(row[column.key]) : row[column.key] }}
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<style scoped>
/* Add any custom styles here */
</style>
