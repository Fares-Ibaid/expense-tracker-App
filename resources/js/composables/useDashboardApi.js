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

    // Generic function to fetch data (initial or filtered)
    const fetchData = async (filters = {}) => {
        try {
            isLoading.value = true;
            const response = await axios.get('/api/expenses/summary-by-category', {
                params: filters,
            });

            // Update state with the response data
            chartData.value = {
                series: response.data.map(item => Math.abs(parseFloat(item.total))),
                labels: response.data.map(item => item.category),
            };
            totalExpenses.value = response.data.totalExpenses || 0;
            numberOfExpenses.value = response.data.numberOfExpenses || 0;
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

    return {
        isLoading,
        chartData,
        totalExpenses,
        numberOfExpenses,
        fetchInitialData,
        fetchFilteredData,
    };
}
