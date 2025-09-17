<script setup>
defineProps({
    message: {
        type: String,
        default: 'Are you sure?'
    }
})
defineEmits(['confirm', 'cancel'])
</script>

<template>
    <div class="fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center">
        <div class="bg-white rounded shadow p-6 w-full max-w-md">
            <p class="text-gray-800 text-lg mb-6">{{ message }}</p>

            <div class="flex justify-end gap-3">

                <button
                    @click="$emit('confirm')"
                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded"
                >
                    Confirm
                </button>

                <button
                    @click="$emit('cancel')"
                    class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded"
                >
                    Cancel
                </button>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
