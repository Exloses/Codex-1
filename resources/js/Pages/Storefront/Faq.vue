<script setup>
import StorefrontLayout from '@/Layouts/StorefrontLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    faqs: {
        type: Object,
        default: () => ({ data: [] }),
    },
});
</script>

<template>
    <StorefrontLayout>
        <Head title="FAQ" />
        <section class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold tracking-normal">FAQ</h1>
            <div class="mt-6 space-y-3">
                <details v-for="faq in faqs.data || faqs" :key="faq.id" class="rounded-lg border border-zinc-200 bg-white p-4">
                    <summary class="cursor-pointer text-sm font-semibold">{{ faq.question }}</summary>
                    <p class="mt-3 text-sm leading-6 text-zinc-600">{{ faq.answer }}</p>
                </details>
            </div>
        </section>
    </StorefrontLayout>
</template>
