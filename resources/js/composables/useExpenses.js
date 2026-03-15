// utilities/useExpenses.js
import { ref } from 'vue';
import axios from 'axios';

export function useExpenses() {
    const expenses = ref([]);
    const total = ref(0);
    const count = ref(0);
    const totalAmount = ref(0);
    const filters = ref({});
    const pagination = ref({ page: 1, perPage: 5 });
    const categories = ref([]);
    const monthsAndYears = ref([]);
    const fetchExpenses = async () => {
        try {
            if (Object.keys(filters.value.filters || {}).length > 0) {
            }
            const response = await axios.get('/api/dashboard', {
                params: {
                    ...filters.value.filters,
                    page: pagination.value.page,
                    per_page: pagination.value.perPage,
                },
            });
            expenses.value = response.data.expenses.data;
            total.value = response.data.total;
            count.value = response.data.count;
            totalAmount.value = response.data.totalAmount;
        } catch (error) {
            console.error('Error fetching expenses:', error);
        }

    };
    const applyFilters = (newFilters) => {
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
        } catch (error) {
            console.error('Error fetching categories:', error);
        }
    }

    const fetchMonthsAndYears = async () => {
        try {
            const response = await axios.get('/api/expenses/available-months-years');
            monthsAndYears.value = response.data;
        } catch (error) {
            console.error('Failed to fetch months and years:', error);
        }
    };

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
        fetchCategories ,
        monthsAndYears,
        fetchMonthsAndYears ,
        totalAmount
    };
}
