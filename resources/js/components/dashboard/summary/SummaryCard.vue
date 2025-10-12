<script setup>
import { defineProps } from 'vue';

const props = defineProps({
    title: String,
    value: [String, Number],
    valueClass: String,
});
</script>

<template>
    <div class="bg-white p-4 shadow rounded">
        <h2 class="text-lg font-semibold">{{ title }}</h2>
        <p :class="valueClass">{{ value }}</p>
    </div>
</template>

<style scoped>

</style>
