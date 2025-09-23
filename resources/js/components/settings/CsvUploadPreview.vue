<script setup>
import {computed, ref} from 'vue'
import axios from 'axios'

const previewData = ref(null) // { file_id, headers[], preview[][] }
const loading = ref(false)
const columnMappings = ref({}) // e.g., { 0: 'Date', 1: 'Description' }


// computed prop

const isValidMapping = computed(() => {
  const selectedMappings = Object.values(columnMappings.value);
  const importantMappings = selectedMappings.filter(val =>
      ['Date', 'Description', 'Amount'].includes(val)
  );
  const uniqueMappings = [...new Set(importantMappings)];

  return importantMappings.length === 3 && uniqueMappings.length === 3;
});

async function onFileChange(event) {
    const file = event.target.files[0]
    if (!file) return

    loading.value = true

    const formData = new FormData()
    formData.append('file', file)

    try {
        const res = await axios.post('api/csv/preview', formData, {
            headers: { 'Content-Type': 'multipart/form-data' },
        })

        previewData.value = res.data

        // Reset mappings when new preview loaded
        columnMappings.value = {}
    } catch (error) {
        alert(error.response?.data?.message || error.message || 'Failed to get preview')
    } finally {
        loading.value = false
    }
}

function submitMappings() {

  const selectedMappings = Object.values(columnMappings.value);

  // Extract only Date, Description, Amount (ignore "Ignore" and empty)
  const importantMappings = selectedMappings.filter(val =>
      ['Date', 'Description', 'Amount'].includes(val)
  );

  const uniqueMappings = [...new Set(importantMappings)];

  // Validate count
  if (importantMappings.length !== 3) {
    alert('Please map exactly one column each to Date, Description, and Amount.');
    return;
  }

  // Validate no duplicates
  if (uniqueMappings.length !== 3) {
    alert('Each of Date, Description, and Amount must be mapped to a different column.');
    return;
  }

  // Passed validation
  console.log('Submitting mappings:', {
    file_id: previewData.value.file_id,
    mappings: columnMappings.value,
  });

  // TODO: POST to backend

    // TODO: POST to backend processing endpoint
}
</script>

<template>
    <div class="csv-upload-container">
        <h2>Upload CSV</h2>
        <input type="file" @change="onFileChange" accept=".csv" />

        <div v-if="loading">Loading preview...</div>

        <div v-if="previewData">
            <h3>Preview & Column Mapping</h3>
            <div class="table-container">
                <table border="1">
                    <thead>
                    <tr>
                        <th v-for="(header, index) in previewData.headers" :key="index">
                            <select v-model="columnMappings[index]">
                                <option disabled value="">-- Select Mapping --</option>
                                <option value="Date">Date</option>
                                <option value="Description">Description</option>
                                <option value="Amount">Amount</option>
                                <option value="Ignore">Ignore</option>
                            </select>
                            <div>{{ header }}</div>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(row, rowIndex) in previewData.preview" :key="rowIndex">
                        <td v-for="(cell, cellIndex) in row" :key="cellIndex">{{ cell }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>

          <button v-if="isValidMapping" @click="submitMappings" class="submit-mappings-button">Submit Mappings</button>
        </div>
    </div>
</template>

<style scoped>
.csv-upload-container {
    border: 3px solid #ccc;
    padding: 10px;
    margin-top: 20px;
    display: flex;
    flex-direction: column;
}

.table-container {
    overflow-x: auto;
    max-width: 100%;
}

.csv-upload-container table {
    border-collapse: collapse;
    width: 100%;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Add shadow for depth */
    border-radius: 8px; /* Rounded corners */
    overflow: hidden; /* Ensure rounded corners apply */
}

.csv-upload-container th,
.csv-upload-container td {
    padding: 12px; /* Add more padding for better spacing */
    text-align: left;
}

.csv-upload-container th {
    background-color: #4CAF50; /* Green header background */
    color: #f0f0f0; /* Light gray text for contrast */
    font-weight: bold;
    text-align: center; /* Center align header content */
    vertical-align: middle; /* Center vertically */
}

.csv-upload-container tbody td {
    text-align: center; /* Center align row content */
    vertical-align: middle; /* Center vertically */
}

.csv-upload-container tbody tr:nth-child(even) {
    background-color: #f9f9f9; /* Light gray for alternate rows */
}

.csv-upload-container tbody tr:hover {
    background-color: #f1f1f1; /* Highlight row on hover */
}

.submit-mappings-button {
    font-size: 1.5rem;
    background-color: deepskyblue;
    color: white;
    float: right;
    margin-top: 20px;
    border: 2px solid #000; /* Added border */
    padding: 10px 20px; /* Added padding */
}

.csv-upload-container h3 {
    margin-top: 20px;
}

option {
  color: black; /* Set the text color of options to black */
}

select {
  color: black; /* Ensure the selected option text is black */
}

</style>
