<script setup>
import CsvUpload from "@/components/expenses/CsvUpload.vue";
import FiltersPanel from "@/components/utilities/FiltersPanel.vue";
import TableView from "@/components/utilities/TableView.vue";
import {useExpenses} from "@/composables/useExpenses.js";
import Pagination from "@/components/utilities/Pagination.vue";

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
