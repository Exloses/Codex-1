<script setup>
import ProductCard from '@/Components/ProductCard.vue';
import StorefrontLayout from '@/Layouts/StorefrontLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Autoplay, Pagination } from 'swiper/modules';
import { Swiper, SwiperSlide } from 'swiper/vue';
import 'swiper/css';
import 'swiper/css/pagination';

defineProps({
    banners: {
        type: Array,
        default: () => [],
    },
    categories: {
        type: Array,
        default: () => [],
    },
    featuredProducts: {
        type: Array,
        default: () => [],
    },
});

const modules = [Autoplay, Pagination];
const newsletterForm = useForm({ email: '' });

const subscribe = () => {
    newsletterForm.post(route('newsletter.subscribe'), {
        preserveScroll: true,
        onSuccess: () => newsletterForm.reset(),
    });
};

const fallbackBanners = [
    {
        title: 'Global dropship essentials',
        title_id: 'Produk pilihan lintas negara',
        image: 'https://images.unsplash.com/photo-1523381210434-271e8be1f52b?auto=format&fit=crop&w=1800&q=80',
        link: '/products',
    },
    {
        title: 'Verified Indonesian vendors',
        title_id: 'Supplier lokal, checkout global',
        image: 'https://images.unsplash.com/photo-1555529771-835f59fc5efe?auto=format&fit=crop&w=1800&q=80',
        link: '/products?sort=latest',
    },
];
</script>

<template>
    <StorefrontLayout>
        <Head title="Global storefront" />

        <section class="bg-white">
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                <Swiper
                    :modules="modules"
                    :pagination="{ clickable: true }"
                    :autoplay="{ delay: 4500 }"
                    :loop="true"
                    class="overflow-hidden rounded-lg"
                >
                    <SwiperSlide v-for="banner in banners.length ? banners : fallbackBanners" :key="banner.title">
                        <Link :href="banner.link || route('products.index')" class="relative block min-h-[420px] overflow-hidden rounded-lg sm:min-h-[520px]">
                            <img :src="banner.image" :alt="banner.title" class="absolute inset-0 h-full w-full object-cover" />
                            <div class="absolute inset-0 bg-gradient-to-r from-zinc-950/80 via-zinc-950/40 to-transparent"></div>
                            <div class="relative flex min-h-[420px] max-w-2xl flex-col justify-end px-6 pb-16 pt-20 text-white sm:min-h-[520px] sm:px-10">
                                <p class="text-sm font-semibold uppercase tracking-normal text-emerald-200">Global dropship platform</p>
                                <h1 class="mt-3 text-4xl font-bold tracking-normal sm:text-6xl">{{ banner.title }}</h1>
                                <p class="mt-4 max-w-xl text-base leading-7 text-zinc-100">{{ banner.title_id || 'Curated products, transparent checkout, and vendor-backed fulfillment from Indonesia to the world.' }}</p>
                            </div>
                        </Link>
                    </SwiperSlide>
                </Swiper>
            </div>
        </section>

        <section class="border-y border-zinc-200 bg-zinc-950 text-white">
            <div class="mx-auto grid max-w-7xl gap-4 px-4 py-6 sm:grid-cols-3 sm:px-6 lg:px-8">
                <div>
                    <p class="text-sm font-semibold">Verified supply</p>
                    <p class="mt-1 text-sm text-zinc-300">Vendor approval and admin moderation keep catalog quality tight.</p>
                </div>
                <div>
                    <p class="text-sm font-semibold">USD-first pricing</p>
                    <p class="mt-1 text-sm text-zinc-300">A stable base currency keeps checkout and reporting clean.</p>
                </div>
                <div>
                    <p class="text-sm font-semibold">Order tracking</p>
                    <p class="mt-1 text-sm text-zinc-300">Buyer and guest tracking paths are ready from the storefront.</p>
                </div>
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <div class="flex items-end justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold tracking-normal">Shop by category</h2>
                    <p class="mt-2 text-sm text-zinc-600">Browse active storefront categories.</p>
                </div>
                <Link :href="route('products.index')" class="text-sm font-semibold text-emerald-700">All products</Link>
            </div>
            <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <Link
                    v-for="category in categories.slice(0, 8)"
                    :key="category.id"
                    :href="route('category.show', category.slug)"
                    class="rounded-lg border border-zinc-200 bg-white p-5 hover:border-emerald-300 hover:shadow-sm"
                >
                    <p class="text-sm font-semibold text-zinc-950">{{ category.name }}</p>
                    <p class="mt-2 text-xs text-zinc-500">{{ category.name_id || 'International catalog' }}</p>
                </Link>
            </div>
        </section>

        <section class="bg-white">
            <div class="mx-auto grid max-w-7xl gap-8 px-4 py-10 sm:px-6 lg:grid-cols-[0.8fr_1.2fr] lg:px-8">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-normal text-rose-600">Flash sale</p>
                    <h2 class="mt-2 text-3xl font-bold tracking-normal">Margin-friendly deals for fast movers</h2>
                    <p class="mt-4 text-sm leading-6 text-zinc-600">Highlighted products use compare pricing when available, keeping promotional value visible without exposing vendor cost.</p>
                    <Link :href="route('products.index', { sort: 'price_asc' })" class="mt-6 inline-flex rounded-md bg-zinc-950 px-4 py-2 text-sm font-semibold text-white">Browse deals</Link>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <ProductCard v-for="product in featuredProducts.slice(0, 4)" :key="product.id" :product="product" />
                </div>
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <div class="flex items-end justify-between">
                <div>
                    <h2 class="text-2xl font-bold tracking-normal">Featured products</h2>
                    <p class="mt-2 text-sm text-zinc-600">Selected products ready for cross-border buyers.</p>
                </div>
            </div>
            <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <ProductCard v-for="product in featuredProducts" :key="product.id" :product="product" />
            </div>
        </section>

        <section class="border-t border-zinc-200 bg-emerald-700 text-white">
            <div class="mx-auto flex max-w-7xl flex-col gap-6 px-4 py-10 sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8">
                <div>
                    <h2 class="text-2xl font-bold tracking-normal">Get supplier drops and platform updates</h2>
                    <p class="mt-2 text-sm text-emerald-50">Newsletter subscriptions are wired to the backend route.</p>
                </div>
                <form class="flex w-full gap-2 lg:max-w-md" @submit.prevent="subscribe">
                    <input v-model="newsletterForm.email" type="email" required class="min-w-0 flex-1 rounded-md border-0 text-sm text-zinc-950" placeholder="buyer@example.com" />
                    <button class="rounded-md bg-zinc-950 px-4 py-2 text-sm font-semibold text-white">Subscribe</button>
                </form>
            </div>
        </section>
    </StorefrontLayout>
</template>
