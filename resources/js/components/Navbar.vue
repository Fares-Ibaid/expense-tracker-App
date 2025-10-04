<script setup>
const  props = defineProps({
    currentView: {
        type: String,
        required: true,
    },
})
const links = [
    { name: 'DashboardNew', href: '#dashboard', icon: '🏠' },
    { name: 'Expenses', href: '#expenses', icon: '💸' },
  { name: 'Reports', href: '#reports', icon: '📊', note: 'Available in v2' },
    { name: 'Settings', href: '#settings', icon: '⚙️' },
];
</script>

<template>
    <nav class="bg-gray-800 text-white p-4">
        <div class="container mx-auto flex items-center justify-between">
            <!-- Logo -->
            <div class="flex items-center space-x-2">
                <span class="text-2xl">🌟</span>
                <h1 class="text-xl font-bold">Expense Tracker</h1>
            </div>

            <!-- Navigation Links -->
            <ul class="flex space-x-6">
                <li v-for="link in links" :key="link.name">
                    <a
                        :href="link.href"
                        @click.prevent="$emit('navigate', link.name.toLowerCase())"
                        :class="[
                            'flex items-center space-x-2 hover:text-gray-300 px-3 py-2 rounded',
                            currentView === link.name.toLowerCase()
                                ? 'bg-gray-700 border-b-4 border-blue-500'
                                : ''
                        ]"
                    >
                        <span>{{ link.icon }}</span>
                        <span>{{ link.name }}</span>
                    </a>
                    <span v-if="link.note" class="text-sm text-gray-400">{{ link.note }}</span>
                </li>
            </ul>
        </div>
    </nav>
</template>

<style scoped>

</style>
