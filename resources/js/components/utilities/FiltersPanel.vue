<script setup>
import {ref , defineEmits , computed , watch} from 'vue';
import { debounce } from 'lodash';

const props = defineProps({
   target : {
       type : String,
       required : true , // table or chart
   },
    categories : {
        type: Array,
        required: false,
        default: () => []
    } ,
monthsAndYears: {
    type: Array,
    required: true,
    default: () => [],
}
});

const emit = defineEmits(['apply-filters' , 'reset-filters','update-filters']);
const filters = ref({
            category: '',
            minAmount: '',
            maxAmount: '',
            startDate: '',
            endDate: '' ,
            month: '',
            year: ''
        });

// -------- filter by months , years ----------------
const months = ref([]);
const years = ref([]);
watch(
    () => props.monthsAndYears,
    (newData) => {
        months.value = [...new Set(newData.map(item => item.month))];
        years.value = [...new Set(newData.map(item => item.year))];
    },
    { immediate: true }
);

// formating months to full month name
const monthNames = [
    "January", "February", "March", "April", "May", "June",
    "July", "August", "September", "October", "November", "December"
];

// Example: Map months and years
const formattedMonthsAndYears = props.monthsAndYears.map(item => ({
    year: item.year,
    month: monthNames[item.month - 1] // Convert month number to name
}));

// ------------------------------ Reactvie watch for filters changes -------------------
const activeFilter = ref(null); // 'monthYear' or 'dateRange'
// Method to handle filter activation
const activateFilter = (filterType) => {
    activeFilter.value = filterType;

    // Reset the other filter's values
    if (filterType === 'monthYear') {
        filters.value.startDate = null;
        filters.value.endDate = null;
    } else if (filterType === 'dateRange') {
        filters.value.month = null;
        filters.value.year = null;
    }
};

// --------------------------------- autocomplete category input -----------------------
const searchQuery = ref('');
const dropdownVisible = ref(false);


// Filter categories based on the search query
const filteredCategories = computed(() =>
    searchQuery.value
        ? props.categories.filter((category) =>
            category.name.toLowerCase().includes(searchQuery.value.toLowerCase())
        )
        : props.categories
);

// Select a category from the dropdown
const selectCategory = (category) => {
    filters.value.category = category.name;
    emit('update-filters', { category: filters.value.category  });
    searchQuery.value = category.name; // Set the selected category name in the input
    dropdownVisible.value = false;
};

// Show dropdown on input focus
const showDropdown = () => {
    dropdownVisible.value = true;
};

// Hide dropdown on input blur (with a slight delay to allow click events)
const hideDropdown = () => {
    setTimeout(() => {
        dropdownVisible.value = false;
    }, 200);
};


// Method to reset filters
const resetFilters = (target) => {
    filters.value = {
        category: '',
        minAmount: '',
        maxAmount: '',
        startDate: '',
        endDate: '',
        month : '',
        year : ''
    };
    activeFilter.value = null; // Reset active filter
    searchQuery.value = ''; // Clear the search query for the category input


    emit('reset-filters', { target });

};


// Loading state
const loading = ref(false);
const buttonClass = computed(() =>{
   return loading.value ? 'bg-gray-500' : 'bg-blue-500' ;
});

// Define the applyFilters method
const applyFilters = async (target) => {
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
                <!-- toDO - refactor Category input  -->
                <div class="relative">
                    <input
                        v-model="searchQuery"
                        placeholder="Search Category"
                        class="p-2 border rounded w-full"
                        @focus="showDropdown"
                        @blur="hideDropdown"
                    />
                    <ul
                        v-if="dropdownVisible && filteredCategories.length"
                        class="absolute border rounded mt-1 bg-white w-full z-10"
                    >
                        <li
                            v-for="category in filteredCategories"
                            :key="category.id"
                            @click="selectCategory(category)"
                            class="p-2 hover:bg-gray-200 cursor-pointer"
                        >
                            {{ category.name }}
                        </li>
                    </ul>
                </div>
                <!-- Filters for date Range  -->
                <div class="flex gap-4">
                    <input
                        v-model="filters.startDate"
                        type="date"
                        placeholder="Start Date"
                        class="p-2 border rounded flex-1"
                        :disabled="activeFilter === 'monthYear'"
                        @change="activateFilter('dateRange')"
                    />
                    <input
                        v-model="filters.endDate"
                        type="date"
                        placeholder="End Date"
                        class="p-2 border rounded flex-1"
                        :disabled="activeFilter === 'monthYear'"
                        @change="activateFilter('dateRange')"
                    />
                </div>
                <!-- Filters for Amount  -->
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

            <!--  filter by months , year -->
            <div class="flex gap-4 mt-4">
                <select
                    v-model="filters.month"
                    :disabled="activeFilter === 'dateRange'"
                    @change="activateFilter('monthYear')"
                    class="p-2 border rounded flex-1"
                >
                    <option disabled selected>Select Month</option>
                    <option v-for="month in months" :key="month" :value="month">
                        {{ monthNames[month - 1] }}
                    </option>
                </select>

                <select
                    v-model="filters.year"
                    :disabled="activeFilter === 'dateRange'"
                    @change="activateFilter('monthYear')"
                    class="p-2 border rounded flex-1">
                    <option disabled selected>Select Year</option>
                    <option v-for="year in years" :key="year" :value="year">
                        {{ year }}
                    </option>
                </select>
            </div>
            <!-- Buttons -->
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

