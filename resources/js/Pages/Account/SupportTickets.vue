<script setup>
import StatusBadge from '@/Components/StatusBadge.vue';
import StorefrontLayout from '@/Layouts/StorefrontLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

defineProps({ ticket: { type: Object, required: true } });
const form = useForm({ message: '' });
</script>

<template>
    <StorefrontLayout>
        <Head title="Support ticket" />
        <section class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">
            <div class="rounded-lg border border-zinc-200 bg-white p-5">
                <div class="flex items-center justify-between">
                    <div><p class="text-sm text-zinc-500">Ticket</p><h1 class="text-2xl font-bold tracking-normal">{{ ticket.subject }}</h1></div>
                    <StatusBadge :value="ticket.status" />
                </div>
                <p class="mt-4 text-sm text-zinc-600">{{ ticket.message }}</p>
                <div class="mt-6 space-y-3">
                    <div v-for="reply in ticket.replies || []" :key="reply.id" class="rounded-md bg-zinc-50 p-3 text-sm">{{ reply.message }}</div>
                </div>
                <form class="mt-5" @submit.prevent="form.post(route('support.reply', ticket.id), { preserveScroll: true, onSuccess: () => form.reset() })">
                    <textarea v-model="form.message" class="w-full rounded-md border-zinc-300 text-sm" rows="3" placeholder="Reply"></textarea>
                    <button class="mt-2 rounded-md bg-zinc-950 px-4 py-2 text-sm font-semibold text-white">Send reply</button>
                </form>
            </div>
        </section>
    </StorefrontLayout>
</template>
