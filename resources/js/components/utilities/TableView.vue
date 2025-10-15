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

    const query = String(searchQuery.value).toLowerCase();
      return props.rows.filter(row =>
          Object.values(row).some(value =>
              String(value).toLowerCase().includes(query)
          )
      );
  });

// sorting functionality
const sortKey = ref(null);
const sortOrder = ref(1); // 1 ASC , -1 DESC

const sortedRows = computed(() => {
    if (!sortKey.value) {
      return filteredRows.value;
    }

    return [...filteredRows.value].sort((a, b) => {
      const aValue = a[sortKey.value];
      const bValue = b[sortKey.value];

      // Handle cases where values are null or undefined
      if (aValue == null && bValue == null) return 0;
      if (aValue == null) return sortOrder.value;
      if (bValue == null) return -sortOrder.value;

      // Compare values for sorting
      return aValue > bValue ? sortOrder.value : -sortOrder.value;
    });
  });


// Handle column header click for sorting
const sortBy = (key) => {
  if (sortKey.value === key) {
    sortOrder.value *= -1; // Toggle sort order
  } else {
    sortKey.value = key;
    sortOrder.value = 1; // Default to ascending
  }

 /* console.log('sortKey:', sortKey.value, 'sortOrder:', sortOrder.value);*/
};


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
              <th v-for="column in columns"
                  :key="column.key"
                  class="border border-gray-300 px-4 py-2 text-left cursor-pointer"
                  @click="sortBy(column.key)"
              >
                {{ column.label }}
                <span v-if="sortKey === column.key">
<!--                {{ sortOrder.value === 1 ? 'ASC' : 'DESC' }}-->
                </span>
                <span v-else>
<!--                Debug: sortKey = {{ sortKey?.value }}, column.key = {{ column.key }}-->
                </span>
              </th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(row, rowIndex) in sortedRows" :key="rowIndex">
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
tbody tr:hover {
    background-color: #d4edda; /* Light green background on hover */
}
</style>
