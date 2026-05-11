<script setup>
import StatusBadge from '@/Components/StatusBadge.vue';
import StorefrontLayout from '@/Layouts/StorefrontLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps({ returnRequest: { type: Object, required: true } });
</script>

<template>
    <StorefrontLayout>
        <Head title="Return request" />
        <section class="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:px-8">
            <div class="rounded-lg border border-zinc-200 bg-white p-5">
                <div class="flex items-center justify-between">
                    <h1 class="text-2xl font-bold tracking-normal">{{ returnRequest.return_number }}</h1>
                    <StatusBadge :value="returnRequest.status" />
                </div>
                <p class="mt-4 text-sm text-zinc-600">{{ returnRequest.description }}</p>
            </div>
        </section>
    </StorefrontLayout>
</template>
