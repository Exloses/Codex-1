<script setup>
defineProps({
    src: {
        type: String,
        required: true,
    },
    alt: {
        type: String,
        default: '',
    },
});
</script>

<template>
    <div class="group overflow-hidden rounded-lg border border-zinc-200 bg-white">
        <img :src="src" :alt="alt" class="aspect-square w-full object-cover transition duration-300 group-hover:scale-110" />
    </div>
</template>
