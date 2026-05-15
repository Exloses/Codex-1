<script setup>
import InputError from '@/Components/InputError.vue';
import StorefrontLayout from '@/Layouts/StorefrontLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    orders: {
        type: Array,
        default: () => [],
    },
    priorities: {
        type: Array,
        default: () => ['low', 'normal', 'high', 'urgent'],
    },
});

const form = useForm({
    subject: '',
    message: '',
    priority: 'normal',
    order_id: '',
});
</script>

<template>
    <StorefrontLayout>
        <Head title="New support ticket" />
        <section class="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:px-8">
            <Link :href="route('account.support.index')" class="text-sm font-semibold text-emerald-700">Back to tickets</Link>
            <h1 class="mt-3 text-3xl font-bold tracking-normal">New support ticket</h1>

            <form class="mt-6 rounded-lg border border-zinc-200 bg-white p-5" @submit.prevent="form.post(route('support.store'))">
                <div class="grid gap-4">
                    <div>
                        <label class="text-sm font-semibold text-zinc-700" for="subject">Subject</label>
                        <input id="subject" v-model="form.subject" class="mt-1 w-full rounded-md border-zinc-300 text-sm" maxlength="255" />
                        <InputError class="mt-2" :message="form.errors.subject" />
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="text-sm font-semibold text-zinc-700" for="priority">Priority</label>
                            <select id="priority" v-model="form.priority" class="mt-1 w-full rounded-md border-zinc-300 text-sm">
                                <option v-for="priority in priorities" :key="priority" :value="priority">{{ priority }}</option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.priority" />
                        </div>

                        <div>
                            <label class="text-sm font-semibold text-zinc-700" for="order_id">Order</label>
                            <select id="order_id" v-model="form.order_id" class="mt-1 w-full rounded-md border-zinc-300 text-sm">
                                <option value="">General support</option>
                                <option v-for="order in orders" :key="order.id" :value="order.id">{{ order.order_number }}</option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.order_id" />
                        </div>
                    </div>

                    <div>
                        <label class="text-sm font-semibold text-zinc-700" for="message">Message</label>
                        <textarea id="message" v-model="form.message" class="mt-1 w-full rounded-md border-zinc-300 text-sm" rows="7" maxlength="5000" />
                        <InputError class="mt-2" :message="form.errors.message" />
                    </div>
                </div>

                <div class="mt-5 flex items-center justify-end gap-3">
                    <Link :href="route('account.support.index')" class="rounded-md border border-zinc-300 px-4 py-2 text-sm font-semibold hover:bg-zinc-50">Cancel</Link>
                    <button class="rounded-md bg-zinc-950 px-4 py-2 text-sm font-semibold text-white hover:bg-zinc-800" :disabled="form.processing">
                        Create ticket
                    </button>
                </div>
            </form>
        </section>
    </StorefrontLayout>
</template>
