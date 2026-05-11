<script setup>
import EmptyState from '@/Components/EmptyState.vue';
import StorefrontLayout from '@/Layouts/StorefrontLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({ products: { type: Object, default: () => ({ data: [] }) } });
</script>

<template>
    <StorefrontLayout>
        <Head title="Vendor products" />
        <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold tracking-normal">Vendor products</h1>
                <Link :href="route('vendor.products.create')" class="rounded-md bg-zinc-950 px-4 py-2 text-sm font-semibold text-white">Create</Link>
            </div>
            <div class="mt-6 overflow-hidden rounded-lg border border-zinc-200 bg-white">
                <table class="w-full text-left text-sm">
                    <thead class="bg-zinc-50 text-zinc-500"><tr><th class="p-3">Name</th><th class="p-3">Category</th><th class="p-3">Price</th><th class="p-3">Stock</th><th class="p-3"></th></tr></thead>
                    <tbody>
                        <tr v-for="product in products.data" :key="product.id" class="border-t border-zinc-100">
                            <td class="p-3 font-semibold">{{ product.name }}</td>
                            <td class="p-3">{{ product.category?.name }}</td>
                            <td class="p-3">${{ product.selling_price }}</td>
                            <td class="p-3">{{ product.stock }}</td>
                            <td class="p-3 text-right"><Link :href="route('vendor.products.edit', product.id)" class="font-semibold text-emerald-700">Edit</Link></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <EmptyState v-if="!products.data?.length" class="mt-6" title="No products" />
        </section>
    </StorefrontLayout>
</template>
