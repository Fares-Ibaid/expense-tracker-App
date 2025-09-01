<script setup>
import {ref , defineEmits} from 'vue';

const emit = defineEmits(['apply-filters']);
const filters = ref({
            category: '',
            minAmount: '',
            maxAmount: '',
            startDate: '',
            endDate: ''});

// Define the applyFilters method
const applyFilters = () => {
    emit('apply-filters', filters.value);
}
</script>
<template>
    <div class="filters-panel p-4 bg-gray-100 rounded shadow">
        <h2 class="text-lg font-semibold mb-4">Filters</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <input
                v-model="filters.category"
                placeholder="Category"
                class="p-2 border rounded"
            />
            <div class="flex gap-4">
                <input
                    v-model="filters.startDate"
                    type="date"
                    placeholder="Start Date"
                    class="p-2 border rounded flex-1"
                />
                <input
                    v-model="filters.endDate"
                    type="date"
                    placeholder="End Date"
                    class="p-2 border rounded flex-1"
                />
            </div>
            <input
                v-model="filters.minAmount"
                type="number"
                placeholder="Min Amount"
                class="p-2 border rounded"
            />
            <input
                v-model="filters.maxAmount"
                type="number"
                placeholder="Max Amount"
                class="p-2 border rounded"
            />
        </div>
        <div class="mt-4 flex justify-end">
            <button
                @click="applyFilters"
                class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
            >
                Apply Filters
            </button>

            <!-- Reset Filters Button -->
            <button @click="$emit('reset-filters')" class="ml-2 bg-red-500 text-white px-4 py-2 rounded">
                Reset Filters
            </button>
        </div>
    </div>
</template>

<style scoped>
.filters-panel {
    max-width: 800px;
    border: 1px solid #ccc;
    background-color: #ffefd5;
}
</style>
