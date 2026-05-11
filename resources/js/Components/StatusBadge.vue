<script setup>
defineProps({
    value: {
        type: String,
        default: 'pending',
    },
});
</script>

<template>
    <span class="inline-flex rounded-md bg-emerald-50 px-2.5 py-1 text-xs font-semibold capitalize text-emerald-700">
        {{ value }}
    </span>
</template>
