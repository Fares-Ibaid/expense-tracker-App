import { ref } from "vue";
import axios from "axios";

export function useDashboardApi() {
    const isLoading = ref(false);
    const chartData = ref({
        series: [],
        labels: [],
    });
    const totalExpenses = ref(0);
    const numberOfExpenses = ref(0);
    const categories = ref([]);
    const filters = ref({});
    const monthsAndYears = ref([]);

    // Generic function to fetch data (initial or filtered)
    const fetchData = async (filters = {}) => {
        try {
            isLoading.value = true;
            console.log('Filterssss : ', filters.filters);
          if (filters?.filters && Object.keys(filters.filters).length > 0) {
              console.log('filtering inside dashboardComp :', filters.filters);
          }
            const response = await axios.get('/api/expenses/summary-by-category', {
                params: {
            ...filters.filters,
                },
            });

    console.log('Response Chart Data ttttt:', JSON.stringify(response.data, null, 2));
            // Update state with the response data
            chartData.value = {
                series: response.data.map(item => Math.abs(parseFloat(item.total))),
                labels: response.data.map(item => item.category),
            };
       totalExpenses.value = response.data.reduce((sum, item) => sum + Math.abs(parseFloat(item.total)), 0);
      // numberOfExpenses.value = response.data.length;
            numberOfExpenses.value = response.data.reduce((sum, item) => sum + item.count, 0);
        } catch (error) {
            console.error('Error fetching data:', error);
        } finally {
            isLoading.value = false;
        }
    };

    // Fetch initial data
  const fetchInitialData = async () => {
      await fetchData(); // Call fetchData without filters
  };

    // Fetch filtered data
    const fetchFilteredData = async (filters) => {
        await fetchData(filters); // Call fetchData with filters
    };

    // Fetch categories on composable initialization
    const fetchCategories = async () => {
        try {
            const response = await axios.get('/api/categories');
            categories.value = response.data;
            console.log('categoires ',categories.value);
        } catch (error) {
            console.error('Error fetching categories:', error);
        }
    }

    // fetch months and years for the expense filters
    const fetchMonthsAndYears = async () => {
        try {
            const response = await axios.get('/api/expenses/available-months-years');
            console.log('months and years response', response.data);
            monthsAndYears.value = response.data;
        } catch (error) {
            console.error('Failed to fetch months and years:', error);
        }
    };

    return {
        isLoading,
        chartData,
        totalExpenses,
        numberOfExpenses,
        fetchInitialData,
        fetchFilteredData,
        categories,
        fetchCategories,
        filters ,
        monthsAndYears,
        fetchMonthsAndYears
    };
}
