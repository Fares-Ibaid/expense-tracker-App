<script setup>
import { defineProps } from 'vue';
import { ref, computed } from 'vue';

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

// handling search functionality
const searchQuery = ref('');

// computed prop. to filter out rows onSearch
const filteredRows = computed(() => {
    if (!searchQuery.value) {
        return props.rows;
    }

    const query = searchQuery.value.toLowerCase();
    return props.rows.filter(row =>
        Object.values(row).some(value =>
            String(value).toLowerCase().includes(query)
        )
    );
});
</script>

<template>
    <div class="overflow-x-auto">
        <!-- Search Input -->
        <div class="mdb-4 mb-6">
            <input
                v-model="searchQuery"
                type="text"
                placeholder="Search..."
                class="border border-gray-300 rounded px-4 py-2 w-full"
            >
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
            <tr v-for="(row, rowIndex) in filteredRows" :key="rowIndex">
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
