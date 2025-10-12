<script setup>
import CsvUpload from "@/components/CsvUpload.vue";
import FiltersPanel from "@/components/FiltersPanel.vue";
import TableView from "@/components/utilities/TableView.vue";
import {useExpenses} from "@/composables/useExpenses.js";

// toDO - refactor the logic of expenses from Dashboad here
 /* ADd the components  :
            csv upload ,
            filterspanel ,
            tableview comp ,
            paginations
     */


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
} = useExpenses();

fetchExpenses();
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
 </div>


</template>

<style scoped>

</style>
