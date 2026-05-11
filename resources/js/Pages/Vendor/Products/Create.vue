<script setup>
import StorefrontLayout from '@/Layouts/StorefrontLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

defineProps({ categories: { type: Array, default: () => [] } });
const form = useForm({ category_id: '', name: '', description: '', vendor_price: 0, selling_price: 0, stock: 0, weight: 0, is_active: true });
</script>

<template>
    <StorefrontLayout>
        <Head title="Create product" />
        <section class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold tracking-normal">Create product</h1>
            <form class="mt-6 grid gap-4 rounded-lg border border-zinc-200 bg-white p-5 sm:grid-cols-2" @submit.prevent="form.post(route('vendor.products.store'))">
                <input v-model="form.name" class="rounded-md border-zinc-300 text-sm" placeholder="Name" />
                <select v-model="form.category_id" class="rounded-md border-zinc-300 text-sm"><option value="">Category</option><option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option></select>
                <textarea v-model="form.description" class="rounded-md border-zinc-300 text-sm sm:col-span-2" rows="4" placeholder="Description"></textarea>
                <input v-model.number="form.vendor_price" type="number" class="rounded-md border-zinc-300 text-sm" placeholder="Vendor price" />
                <input v-model.number="form.selling_price" type="number" class="rounded-md border-zinc-300 text-sm" placeholder="Selling price" />
                <input v-model.number="form.stock" type="number" class="rounded-md border-zinc-300 text-sm" placeholder="Stock" />
                <input v-model.number="form.weight" type="number" class="rounded-md border-zinc-300 text-sm" placeholder="Weight" />
                <button class="rounded-md bg-zinc-950 px-4 py-2 text-sm font-semibold text-white sm:col-span-2">Save product</button>
            </form>
        </section>
    </StorefrontLayout>
</template>
