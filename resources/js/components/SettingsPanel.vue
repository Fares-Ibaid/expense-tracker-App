<script setup>

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
                    type="text"
                    placeholder="Keyword (e.g. Starbucks)"
                    class="border rounded p-2 w-full"
                />

                <!-- Field selection -->
                <select class="border rounded p-2 w-full">
                    <option disabled selected>Select field</option>
                    <option value="description">Description</option>
                    <option value="merchant">Merchant</option>
                    <option value="amount">Amount</option>
                </select>

                <!-- Match Type dropdown -->
                <select class="border rounded p-2 w-full">
                    <option disabled selected>Match Type</option>
                    <option value="contains">Contains</option>
                    <option value="equals">Equals</option>
                    <option value="regex">Regex</option>
                </select>

                <!-- Category dropdown -->
                <select class="border rounded p-2 w-full">
                    <option disabled selected>Select category</option>
                    <option>Groceries</option>
                    <option>Transport</option>
                </select>

                <!-- Add button -->
                <button class="bg-green-600 text-white px-4 rounded">Add</button>
            </div>

            <!-- Rules List -->
            <ul class="space-y-1">
                <li class="flex justify-between items-center border-b py-1">
                    <span>“Starbucks” (contains) → Coffee</span>
                    <button class="text-red-500 text-sm">Delete</button>
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
