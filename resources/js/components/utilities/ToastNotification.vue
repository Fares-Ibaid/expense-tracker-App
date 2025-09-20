<script setup>
import { ref, watch, onMounted } from 'vue'

const props = defineProps({
    message: String,
    type: {
        type: String,
        default: 'success', // or 'error'
    },
    duration: {
        type: Number,
        default: 3000, // milliseconds
    },
})

const visible = ref(false)

onMounted(() => {
    visible.value = true
    setTimeout(() => (visible.value = false), props.duration)
})


</script>

<template>
    <div
        v-if="visible"
        :class="[
      'fixed top-5 right-5 z-50 px-4 py-3 rounded shadow-lg text-white transition-all duration-300',
            type === 'success' ? 'bg-green-600'  :
            type === 'error' ? 'bg-red-600'      :
            type === 'warning' ? 'bg-yellow-400' :
           'bg-blue-400' // info
    ]"
    >
        {{ message }}
    </div>
</template>

<style scoped>

</style>
