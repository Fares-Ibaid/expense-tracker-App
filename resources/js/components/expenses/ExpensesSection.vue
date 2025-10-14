<script setup>
import CsvUpload from "@/components/expenses/CsvUpload.vue";
import FiltersPanel from "@/components/utilities/FiltersPanel.vue";
import TableView from "@/components/utilities/TableView.vue";
import {useExpenses} from "@/composables/useExpenses.js";
import Pagination from "@/components/utilities/Pagination.vue";
import  SummaryCard from "@/components/dashboard/summary/SummaryCard.vue";

// noinspection JSUnusedLocalSymbols
const {
    expenses,
    total,
    count,
    filters,
    pagination,
    fetchExpenses,
    applyFilters,
    resetFilters,
    changePage,
    categories,
    fetchCategories
} = useExpenses();

fetchExpenses();
fetchCategories();


// Define the updateFilters method
const updateFilters = (updatedFilter) => {
    Object.assign(filters.value, updatedFilter);
};
</script>

<template>

 <div class="csv-filters-container">
<CsvUpload class="mb-4 border border-gray-300 p-2" />

     <!-- Summary Card -->
     <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
         <SummaryCard title="Total Expenses" :value="total" valueClass="text-xl font-bold text-blue-600" />
         <SummaryCard title="Filtered Count" :value="count" valueClass="text-xl font-bold text-green-600" />
<!--
         <SummaryCard title="Other Metric" :value="123" valueClass="text-xl font-bold text-red-600" />
-->
     </div>


     <!-- Filters Panel -->
     <FiltersPanel
         class="mb-4 border border-gray-300 p-2"
         :filters="filters"
         @applyFilters="applyFilters"
         @resetFilters="resetFilters"
         @update-filters="updateFilters"
         :categories="categories"
         target="table"

     />
     <!-- Rows Per Page Dropdown -->
     <div class="mb-4">
         <label for="rowsPerPage" class="mr-2">Rows per page:</label>
         <select
             id="rowsPerPage"
             v-model="pagination.perPage"
             @change="changePage(1)"
             class="border p-2 rounded"
         >
            <option :value="5">5</option>
            <option :value="10">10</option>
            <option :value="50">50</option>
            <option :value="100">100</option>
         </select>
         <!-- Reset to page 1 when rows per page changes -->
     </div>
     <!-- Table View -->
     <TableView
         :rows="expenses"
        :columns="[
              { key: 'description', label: 'Description' },
              {
                  key: 'category',
                  label: 'Category',
                  formatter: (value) => value ? value.name : 'No Category'
              },
              { key: 'amount', label: 'Amount (€)' },
              { key: 'date', label: 'Date' },
          ]"
         class="border border-gray-300 p-2"
     />

     <Pagination
         :currentPage="pagination.page"
         :totalPages="Math.ceil(total / pagination.perPage)"
         @pageChange="changePage"
     />
 </div>


</template>

<style scoped>

</style>
