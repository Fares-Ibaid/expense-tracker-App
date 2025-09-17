<script setup>
import { ref , onMounted } from 'vue'
import axios  from "axios";
import {rule} from "postcss";


const rules = ref([]) ;
const categories = ref([]) ;

const editingRule = ref(null) ;

// form data for track the new rule
const ruleForm = ref({
    value: '',
    field: '',
    match_type: '',
    category_id: '',
})


onMounted(async () =>{

    try{
        // fetching categories
        const catResponse = await axios.get('/api/categories')
        categories.value = catResponse.data


        // fetching rules
        const response = await axios.get('/api/rules')
        rules.value = response.data
    }catch (error){
        console.log(error)
    }



})

// add new rule through axios
const addRule = async() => {
    try {
        const response = await axios.post('/api/rules', ruleForm.value)
        // update the rules list directly without refetching
        rules.value.push(response.data)

        ruleForm.value = {
            value: '',
            field: '',
            match_type: '',
            category_id: '',
        }
    }catch (error){
        console.log(error)
    }
}
const deleteRule =  async (rule) =>{
    try{
        console.log(rule , 'to be deleted');
        const response = await axios.delete(`/api/rules/${rule.id}`)
        rules.value = rules.value.filter(r => r.id !== rule.id)

    }catch (error){
        console.log('Delete Failed ',error);
    }
}
</script>

<template>
    <div class="space-y-8">
        <!-- Category Section -->
        <section>
            <h3 class="text-lg font-semibold mb-2">Manage Categories</h3>

            <!-- Add New Category -->
            <div class="flex gap-2 mb-3">
                <input type="text" placeholder="New category" class="border rounded p-2 w-full" />
                <button class="bg-blue-600 text-white px-4 rounded">Add</button>
            </div>

            <!-- Categories List -->
            <ul class="space-y-1">
                <li class="flex justify-between items-center border-b py-1">
                    <span>Groceries</span>
                    <button class="text-red-500 text-sm">Delete</button>
                </li>
                <!-- Repeat... -->
            </ul>
        </section>

        <!-- Rules Section -->
        <section>
            <h3 class="text-lg font-semibold mb-2">Category Rules</h3>

            <!-- Add New Rule -->
            <div class="flex gap-2 mb-3">
                <!-- Keyword input -->
                <input
                    v-model="ruleForm.value"
                    type="text"
                    placeholder="Keyword (e.g. Starbucks)"
                    class="border rounded p-2 w-full"
                />

                <!-- Field selection -->
                <select
                    v-model="ruleForm.field"
                    class="border rounded p-2 w-full"
                >
                    <option disabled selected>Select field</option>
                    <option value="description">Description</option>
                    <option value="merchant">Merchant</option>
                    <option value="amount">Amount</option>
                </select>

                <!-- Match Type dropdown -->
                <select
                    v-model="ruleForm.match_type"
                    class="border rounded p-2 w-full">
                    <option disabled selected>Match Type</option>
                    <option value="contains">Contains</option>
                    <option value="equals">Equals</option>
                    <option value="regex">Regex</option>
                </select>

                <!-- Category dropdown -->
                <select
                    v-model="ruleForm.category_id"
                    class="border rounded p-2 w-full"
                >
                    <option disabled selected>Select category</option>
                    <option
                    v-for="category in categories"
                    :key="category.id"
                    :value="category.id"
                    >
                     {{ category.name }}
                    </option>

                </select>

                <!-- Add button -->
                <button
                    @click="addRule"
                    class="bg-green-600 text-white px-4 rounded"
                >Add</button>
            </div>

            <!-- Rules List -->
            <ul class="space-y-1">
                <li
                    v-for="rule in rules"
                    :key="rule.id"
                    class="flex justify-between items-center border-b py-1"
                >
                <span>
                “{{ rule.value }}” ({{ rule.match_type }}) in {{ rule.field }} →
                <strong>{{ rule.category?.name ?? 'Unknown' }}</strong>
                </span>
                    <button @click="editingRule" class="text-blue-500 text-sm">Edit</button>
                    <button @click="deleteRule(rule)" class="text-red-500 text-sm">Delete</button>
                </li>
                <!-- Repeat... -->
            </ul>
        </section>
    </div>

</template>

<style scoped>
div {
    border: 1px solid #ccc;
    margin-bottom: 16px;
    padding: 16px;
}
</style>
