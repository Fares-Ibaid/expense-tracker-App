// utilities/useExpenses.js
import { ref } from 'vue';
import axios from 'axios';

export function useExpenses() {
    const expenses = ref([]);
    const total = ref(0);
    const count = ref(0);
    const filters = ref({});
    const pagination = ref({ page: 1, perPage: 5 });
    const categories = ref([]);
    const fetchExpenses = async () => {
        try {
           // console.log('Filters being sent:', JSON.stringify(filters.value, null, 2));
            console.log('Filters being sent to API before send :', filters.value.filters);

            const response = await axios.get('/api/dashboard', {
                params: {
                    ...filters.value.filters,
                    page: pagination.value.page,
                    per_page: pagination.value.perPage,
                },
            });
            console.log(response.data);
            expenses.value = response.data.expenses.data;
            total.value = response.data.total;
            count.value = response.data.count;
        } catch (error) {
            console.error('Error fetching expenses:', error);
        }

    };
    const applyFilters = (newFilters) => {
        // console.log('new filters ' , newFilters);
        filters.value = newFilters;
        pagination.value.page = 1; // Reset to the first page
        fetchExpenses();

    };
    const resetFilters = () => {
        filters.value = {};
        pagination.value.page = 1; // Reset to the first page
        fetchExpenses();
    };

    const changePage = (newPage) => {
        pagination.value.page = newPage;
        fetchExpenses();
    };

    const fetchCategories = async () => {
        try {
            const response = await axios.get('/api/categories');
            categories.value = response.data;
         console.log('categoires ',categories.value);
        } catch (error) {
            console.error('Error fetching categories:', error);
        }
    }
    return {
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

    };
}
