<script setup>
import {ref , defineEmits , computed} from 'vue';

const props = defineProps({

   target : {
       type : String,
       required : true , // table or chart
   }
});

const emit = defineEmits(['apply-filters' , 'reset-filters']);
const filters = ref({
            category: '',
            minAmount: '',
            maxAmount: '',
            startDate: '',
            endDate: ''});

// Method to reset filters
const resetFilters = (target) => {
    filters.value = {
        category: '',
        minAmount: '',
        maxAmount: '',
        startDate: '',
        endDate: '',
    };
    emit('reset-filters', { target });

};


// Loading state
const loading = ref(false);
const buttonClass = computed(() =>{
   return loading.value ? 'bg-gray-500' : 'bg-blue-500' ;
});

// Define the applyFilters method
const applyFilters = async (target) => {
   // console.log('applyFilters get triggered ');
    loading.value = true; // Show spinner
    try {
        await new Promise(resolve => setTimeout(resolve, 2000)); // Simulate delay
         emit('apply-filters', {target , filters : filters.value });
    } finally {
        loading.value = false; // Hide spinner
    }
};
</script>
<template>
    <div class="filters-panel-container">
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
                    v-if="target === 'table'"
                    @click="applyFilters('table')"
                    :class="['px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600', buttonClass]"
                    :disabled="loading"
                >
                    <span v-if="loading" class="mr-2">Loading...</span>
                    <span v-else>Apply to Table</span>
                </button>

                <!-- Apply to chart  -->
                <button
                    v-if="target === 'chart'"
                    @click="applyFilters('chart')"
                class="ml-2 px-4 py-2 bg-green-500 text-white rounded"
                >
                Apply to Chart <!-- **Add**: New button for Chart -->
                </button>

                <!-- Reset Table Button -->
                <button
                    v-if="target === 'table'"
                    @click="resetFilters('table')"
                    class="ml-2 bg-red-500 text-white px-4 py-2 rounded">
                    Reset Filters
                </button>
                <!-- Reset Chart -->
                <button
                    v-if="target === 'chart'"
                    @click="resetFilters('chart')"
                class="ml-2 bg-yellow-500 text-white px-4 py-2 rounded"
                >
                Reset Chart Filters <!-- **Add**: New button for Chart -->
                </button>
            </div>
        </div>
    </div>
</template>


<style scoped>
.filters-panel-container {
    display: flex;
    justify-content: center;
    align-items: center;
    height: auto; /* Adjust height to fit content */
    padding: 10px; /* Reduce padding around the container */
    background-color: rgba(0, 0, 0, 0.05); /* Optional lighter background */
}

.filters-panel {
    max-width: 700px; /* Further reduce the width */
    border: 1px solid #ccc;
    background-color: #ffefd5;
    padding: 15px; /* Reduce padding inside the panel */
}

</style>

