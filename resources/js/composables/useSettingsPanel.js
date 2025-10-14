import axios from 'axios';

export function useSettingsPanel() {
    async function fetchCategorizedCounts() {
        try {
            const response = await axios.get('/api/categories/categorized-counts');
            const { categorized, uncategorized } = response.data;

            return {
                categorized,
                uncategorized,
            };
        } catch (error) {
            console.error('Error fetching categorized counts:', error);
            throw error;
        }
    }

 return{
    fetchCategorizedCounts
 }
}

