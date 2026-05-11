<script setup>
import EmptyState from '@/Components/EmptyState.vue';
import ProductCard from '@/Components/ProductCard.vue';
import StorefrontLayout from '@/Layouts/StorefrontLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { reactive } from 'vue';

const props = defineProps({
    products: {
        type: Object,
        default: () => ({ data: [] }),
    },
    categories: {
        type: Array,
        default: () => [],
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
});

const form = reactive({
    q: props.filters.q || '',
    category: props.filters.category || '',
    sort: props.filters.sort || '',
});

const submit = () => {
    router.get(route('products.index'), form, {
        preserveState: true,
        preserveScroll: true,
    });
};
</script>

<template>
    <StorefrontLayout>
        <Head title="Products" />

        <section class="border-b border-zinc-200 bg-white">
            <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                <h1 class="text-3xl font-bold tracking-normal">Products</h1>
                <p class="mt-2 text-sm text-zinc-600">Browse active storefront inventory without exposing vendor cost.</p>
            </div>
        </section>

        <section class="mx-auto grid max-w-7xl gap-8 px-4 py-8 sm:px-6 lg:grid-cols-[260px_1fr] lg:px-8">
            <aside class="self-start rounded-lg border border-zinc-200 bg-white p-4">
                <form class="space-y-4" @submit.prevent="submit">
                    <div>
                        <label class="text-sm font-semibold">Search</label>
                        <input v-model="form.q" class="mt-1 w-full rounded-md border-zinc-300 text-sm" placeholder="Product name" />
                    </div>
                    <div>
                        <label class="text-sm font-semibold">Category</label>
                        <select v-model="form.category" class="mt-1 w-full rounded-md border-zinc-300 text-sm">
                            <option value="">All categories</option>
                            <option v-for="category in categories" :key="category.id" :value="category.slug">{{ category.name }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-sm font-semibold">Sort</label>
                        <select v-model="form.sort" class="mt-1 w-full rounded-md border-zinc-300 text-sm">
                            <option value="">Newest</option>
                            <option value="price_asc">Price low to high</option>
                            <option value="price_desc">Price high to low</option>
                        </select>
                    </div>
                    <button class="w-full rounded-md bg-zinc-950 px-4 py-2 text-sm font-semibold text-white">Apply</button>
                </form>
            </aside>

            <div>
                <div v-if="products.data?.length" class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                    <ProductCard v-for="product in products.data" :key="product.id" :product="product" />
                </div>
                <EmptyState v-else title="No products found" message="Try another search or category." />

                <div v-if="products.links?.length" class="mt-8 flex flex-wrap gap-2">
                    <Link
                        v-for="link in products.links"
                        :key="link.label"
                        :href="link.url || '#'"
                        v-html="link.label"
                        class="rounded-md border px-3 py-2 text-sm"
                        :class="link.active ? 'border-zinc-950 bg-zinc-950 text-white' : 'border-zinc-300 bg-white text-zinc-700'"
                    />
                </div>
            </div>
        </section>
    </StorefrontLayout>
</template>
