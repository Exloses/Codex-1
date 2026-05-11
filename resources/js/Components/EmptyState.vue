<script setup>
defineProps({
    title: {
        type: String,
        required: true,
    },
    message: {
        type: String,
        default: '',
    },
});
</script>

<template>
    <div class="rounded-lg border border-dashed border-zinc-300 bg-white p-8 text-center">
        <p class="text-sm font-semibold text-zinc-950">{{ title }}</p>
        <p v-if="message" class="mt-2 text-sm text-zinc-600">{{ message }}</p>
    </div>
</template>
