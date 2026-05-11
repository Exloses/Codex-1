<script setup>
import StorefrontLayout from '@/Layouts/StorefrontLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({ product: { type: Object, required: true }, categories: { type: Array, default: () => [] } });
const form = useForm({
    category_id: props.product.category_id,
    name: props.product.name,
    description: props.product.description,
    vendor_price: props.product.vendor_price,
    selling_price: props.product.selling_price,
    stock: props.product.stock,
    weight: props.product.weight,
    is_active: props.product.is_active,
});
</script>

<template>
    <StorefrontLayout>
        <Head :title="`Edit ${product.name}`" />
        <section class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold tracking-normal">Edit product</h1>
            <form class="mt-6 grid gap-4 rounded-lg border border-zinc-200 bg-white p-5 sm:grid-cols-2" @submit.prevent="form.put(route('vendor.products.update', product.id))">
                <input v-model="form.name" class="rounded-md border-zinc-300 text-sm" />
                <select v-model="form.category_id" class="rounded-md border-zinc-300 text-sm"><option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option></select>
                <textarea v-model="form.description" class="rounded-md border-zinc-300 text-sm sm:col-span-2" rows="4"></textarea>
                <input v-model.number="form.vendor_price" type="number" class="rounded-md border-zinc-300 text-sm" />
                <input v-model.number="form.selling_price" type="number" class="rounded-md border-zinc-300 text-sm" />
                <input v-model.number="form.stock" type="number" class="rounded-md border-zinc-300 text-sm" />
                <input v-model.number="form.weight" type="number" class="rounded-md border-zinc-300 text-sm" />
                <button class="rounded-md bg-zinc-950 px-4 py-2 text-sm font-semibold text-white sm:col-span-2">Update product</button>
            </form>
        </section>
    </StorefrontLayout>
</template>
