<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const expenses = ref([]);
const total = ref(0);
const count = ref(0);

onMounted(async () => {
    const response = await axios.get('/api/dashboard');
    /*console.log(response.data);*/
    expenses.value = response.data.expenses;
    total.value = response.data.total;
    count.value = response.data.count;
});
</script>

<template>
    <div>
        <h1 class="text-3xl font-bold mb-6">💸 Expense Tracker Dashboard</h1>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white p-4 shadow rounded">
                <h2 class="text-lg font-semibold">Total Expenses</h2>
                <p class="text-2xl font-bold text-green-600">{{ new Intl.NumberFormat('de-DE', { style: 'currency', currency: 'EUR' }).format(total) }}</p>
            </div>
            <div class="bg-white p-4 shadow rounded">
                <h2 class="text-lg font-semibold">Number of Expenses</h2>
                <p class="text-2xl font-bold text-blue-600">{{ count }}</p>
            </div>
        </div>
        <div class="mt-6">
            <h2 class="text-xl font-semibold mb-4">Expenses</h2>
            <ul>
<!--                <li v-for="expense in expenses" :key="expense.id" class="mb-2">
                    {{ expense.category.name }}: {{ expense.amount }} €
                </li>--><!--                <li v-for="expense in expenses" :key="expense.id" class="mb-2">
                    {{ expense.category.name }}: {{ expense.amount }} €
                </li>-->
            </ul>
        </div>
    </div>
</template>

<style scoped>

</style>
