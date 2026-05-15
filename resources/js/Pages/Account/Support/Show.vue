<script setup>
import InputError from '@/Components/InputError.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import StorefrontLayout from '@/Layouts/StorefrontLayout.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    ticket: {
        type: Object,
        required: true,
    },
});

const page = usePage();
const user = computed(() => page.props.auth?.user);
const form = useForm({ message: '' });
const formatDate = (value) => value
    ? new Intl.DateTimeFormat('en-US', { month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit' }).format(new Date(value))
    : '';
</script>

<template>
    <StorefrontLayout>
        <Head :title="ticket.subject" />
        <section class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">
            <Link :href="route('account.support.index')" class="text-sm font-semibold text-emerald-700">Back to tickets</Link>

            <div class="mt-5 rounded-lg border border-zinc-200 bg-white p-5">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-zinc-500">{{ ticket.ticket_number }}</p>
                        <h1 class="mt-1 text-2xl font-bold tracking-normal">{{ ticket.subject }}</h1>
                        <p class="mt-2 text-sm text-zinc-500">
                            {{ ticket.order?.order_number || 'General support' }} - {{ formatDate(ticket.created_at) }}
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <StatusBadge :value="ticket.status" />
                        <span class="inline-flex rounded-md bg-zinc-100 px-2.5 py-1 text-xs font-semibold capitalize text-zinc-700">{{ ticket.priority }}</span>
                    </div>
                </div>

                <div class="mt-5 rounded-md bg-zinc-50 p-4 text-sm leading-6 text-zinc-700">
                    {{ ticket.message }}
                </div>
            </div>

            <div class="mt-6 space-y-3">
                <div
                    v-for="reply in ticket.replies || []"
                    :key="reply.id"
                    class="rounded-lg border border-zinc-200 bg-white p-4"
                    :class="{ 'border-emerald-200 bg-emerald-50/50': reply.is_staff }"
                >
                    <div class="flex items-center justify-between gap-3">
                        <p class="text-sm font-semibold">
                            {{ reply.is_staff ? 'Support team' : (reply.user?.name || user?.name || 'You') }}
                        </p>
                        <p class="text-xs text-zinc-500">{{ formatDate(reply.created_at) }}</p>
                    </div>
                    <p class="mt-2 whitespace-pre-line text-sm leading-6 text-zinc-700">{{ reply.message }}</p>
                </div>
            </div>

            <form class="mt-6 rounded-lg border border-zinc-200 bg-white p-5" @submit.prevent="form.post(route('support.reply', ticket.id), { preserveScroll: true, onSuccess: () => form.reset() })">
                <label class="text-sm font-semibold text-zinc-700" for="reply">Reply</label>
                <textarea id="reply" v-model="form.message" class="mt-1 w-full rounded-md border-zinc-300 text-sm" rows="4" maxlength="5000" />
                <InputError class="mt-2" :message="form.errors.message" />
                <button class="mt-3 rounded-md bg-zinc-950 px-4 py-2 text-sm font-semibold text-white hover:bg-zinc-800" :disabled="form.processing">
                    Send reply
                </button>
            </form>
        </section>
    </StorefrontLayout>
</template>
