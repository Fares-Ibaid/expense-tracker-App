<script setup>
import { ref } from 'vue'
import axios from 'axios'

const previewData = ref(null) // { file_id, headers[], preview[][] }
const loading = ref(false)
const columnMappings = ref({}) // e.g., { 0: 'Date', 1: 'Description' }

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
    // Example: just log to console for now
    console.log('Submitting mappings:', {
        file_id: previewData.value.file_id,
        mappings: columnMappings.value,
    })

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

            <button @click="submitMappings">Submit Mappings</button>
        </div>
    </div>
</template>

<style scoped>
.csv-upload-container {
    border: 3px solid #ccc;
    padding: 10px;
    margin-top: 20px;
}

.csv-upload-container table {
    border-collapse: collapse;
    width: 100%;
}

.csv-upload-container table,
.csv-upload-container th,
.csv-upload-container td {
    border: 1px solid #333;
    padding: 8px;
    text-align: left;
}
</style>
