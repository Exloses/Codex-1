<script setup>
import StorefrontLayout from '@/Layouts/StorefrontLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({ vendor: { type: Object, required: true } });
const form = useForm({ store_name: props.vendor.store_name, description: props.vendor.description || '', province: props.vendor.province || '', city: props.vendor.city || '' });
</script>

<template>
    <StorefrontLayout>
        <Head title="Vendor settings" />
        <section class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold tracking-normal">Vendor settings</h1>
            <form class="mt-6 grid gap-4 rounded-lg border border-zinc-200 bg-white p-5" @submit.prevent="form.put(route('vendor.settings.update'))">
                <input v-model="form.store_name" class="rounded-md border-zinc-300 text-sm" />
                <textarea v-model="form.description" class="rounded-md border-zinc-300 text-sm" rows="4"></textarea>
                <div class="grid gap-4 sm:grid-cols-2">
                    <input v-model="form.province" class="rounded-md border-zinc-300 text-sm" placeholder="Province" />
                    <input v-model="form.city" class="rounded-md border-zinc-300 text-sm" placeholder="City" />
                </div>
                <button class="rounded-md bg-zinc-950 px-4 py-2 text-sm font-semibold text-white">Save settings</button>
            </form>
        </section>
    </StorefrontLayout>
</template>
