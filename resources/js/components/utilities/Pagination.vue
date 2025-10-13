<script setup>
import { defineProps, defineEmits } from 'vue';

const props = defineProps({
    currentPage: {
        type: Number,
        required: true,
    },
    totalPages: {
        type: Number,
        required: true,
    },
});

const emit = defineEmits(['pageChange']);

const goToPage = (page) => {
    if (page >= 1 && page <= props.totalPages) {
        emit('pageChange', page);
    }
};
</script>

<template>
 <div class="flex justify-center mt-4">
     <button
         class="px-4 py-2 mx-1 bg-blue-500 text-white rounded hover:bg-blue-600 disabled:bg-gray-300 disabled:text-gray-500"
         :disabled="currentPage === 1"
         @click="goToPage(currentPage - 1)"
     >
         Previous
     </button>
     <span class="px-4 py-2 mx-1">{{ currentPage }} / {{ totalPages }}</span>
     <button
         class="px-4 py-2 mx-1 bg-green-500 text-white rounded hover:bg-green-600 disabled:bg-gray-300 disabled:text-gray-500"
         :disabled="currentPage === totalPages"
         @click="goToPage(currentPage + 1)"
     >
         Next
     </button>
 </div>
</template>

<style scoped>
/* Add any custom styles here */
</style>
