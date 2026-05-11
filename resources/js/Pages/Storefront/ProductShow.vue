<script setup>
import ProductCard from '@/Components/ProductCard.vue';
import StorefrontLayout from '@/Layouts/StorefrontLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    product: {
        type: Object,
        required: true,
    },
    relatedProducts: {
        type: Array,
        default: () => [],
    },
});

const selectedVariant = ref(props.product.variants?.[0] || null);
const selectedImage = ref(props.product.image || props.product.variants?.[0]?.image || 'https://images.unsplash.com/photo-1503341455253-b2e723bb3dbb?auto=format&fit=crop&w=1200&q=80');
const showSizeGuide = ref(false);
const activeTab = ref('qa');

const price = computed(() => Number(selectedVariant.value?.price || props.product.selling_price || 0));
const stock = computed(() => Number(selectedVariant.value?.stock ?? props.product.stock ?? 0));

const cartForm = useForm({
    product_id: props.product.id,
    product_variant_id: selectedVariant.value?.id || null,
    quantity: 1,
});

const money = (value) => new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(Number(value || 0));

const addToCart = () => {
    cartForm.product_variant_id = selectedVariant.value?.id || null;
    cartForm.post(route('cart.store'), { preserveScroll: true });
};

const notifyStock = () => {
    router.post(route('notifications.stock.store'), { product_id: props.product.id, product_variant_id: selectedVariant.value?.id || null }, { preserveScroll: true });
};

const priceAlert = () => {
    router.post(route('notifications.price-alert.store'), { product_id: props.product.id, target_price_usd: Math.max(price.value - 5, 1) }, { preserveScroll: true });
};
</script>

<template>
    <StorefrontLayout>
        <Head :title="product.name" />

        <section class="mx-auto grid max-w-7xl gap-10 px-4 py-8 sm:px-6 lg:grid-cols-[1fr_0.9fr] lg:px-8">
            <div>
                <div class="overflow-hidden rounded-lg border border-zinc-200 bg-white">
                    <img :src="selectedImage" :alt="product.name" class="aspect-square w-full object-cover transition hover:scale-105" />
                </div>
                <div class="mt-4 grid grid-cols-4 gap-3">
                    <button
                        v-for="variant in product.variants || []"
                        :key="variant.id"
                        class="overflow-hidden rounded-md border border-zinc-200 bg-white"
                        @click="selectedVariant = variant; selectedImage = variant.image || selectedImage"
                    >
                        <img :src="variant.image || selectedImage" :alt="variant.sku || product.name" class="aspect-square w-full object-cover" />
                    </button>
                </div>
            </div>

            <div>
                <p class="text-sm font-semibold text-emerald-700">{{ product.category?.name || 'Catalog' }}</p>
                <h1 class="mt-2 text-3xl font-bold tracking-normal">{{ product.name }}</h1>
                <p class="mt-3 text-sm leading-6 text-zinc-600">{{ product.description }}</p>

                <div class="mt-6 flex items-end gap-3">
                    <p class="text-3xl font-bold">{{ money(price) }}</p>
                    <p v-if="product.compare_price" class="pb-1 text-sm text-zinc-400 line-through">{{ money(product.compare_price) }}</p>
                </div>

                <div class="mt-6">
                    <p class="text-sm font-semibold">Variants</p>
                    <div class="mt-3 flex flex-wrap gap-2">
                        <button
                            v-for="variant in product.variants || []"
                            :key="variant.id"
                            class="rounded-md border px-3 py-2 text-sm"
                            :class="selectedVariant?.id === variant.id ? 'border-zinc-950 bg-zinc-950 text-white' : 'border-zinc-300 bg-white'"
                            @click="selectedVariant = variant"
                        >
                            {{ Object.values(variant.combination || {}).join(' / ') || variant.sku || 'Variant' }}
                        </button>
                    </div>
                </div>

                <div class="mt-6 rounded-lg border border-zinc-200 bg-white p-4">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-semibold">Stock</p>
                        <p class="text-sm" :class="stock > 0 ? 'text-emerald-700' : 'text-rose-700'">{{ stock > 0 ? `${stock} available` : 'Out of stock' }}</p>
                    </div>
                    <div class="mt-4 flex items-center gap-3">
                        <input v-model.number="cartForm.quantity" type="number" min="1" max="99" class="w-24 rounded-md border-zinc-300 text-sm" />
                        <button class="rounded-md bg-zinc-950 px-4 py-2 text-sm font-semibold text-white disabled:opacity-50" :disabled="stock <= 0 || cartForm.processing" @click="addToCart">
                            Add to cart
                        </button>
                        <button class="rounded-md border border-zinc-300 px-4 py-2 text-sm font-semibold" @click="router.post(route('wishlist.toggle', product.id), {}, { preserveScroll: true })">
                            Wishlist
                        </button>
                    </div>
                    <div class="mt-3 flex flex-wrap gap-2">
                        <button v-if="stock <= 0" class="rounded-md border border-zinc-300 px-3 py-2 text-sm" @click="notifyStock">Notify me</button>
                        <button class="rounded-md border border-zinc-300 px-3 py-2 text-sm" @click="priceAlert">Price drop alert</button>
                        <button class="rounded-md border border-zinc-300 px-3 py-2 text-sm" @click="showSizeGuide = true">Size guide</button>
                    </div>
                </div>

                <div class="mt-6 rounded-lg border border-zinc-200 bg-white p-4">
                    <p class="text-sm font-semibold">Shipping estimator</p>
                    <div class="mt-3 flex gap-2">
                        <input class="min-w-0 flex-1 rounded-md border-zinc-300 text-sm" placeholder="Country code, e.g. US" />
                        <button class="rounded-md bg-emerald-600 px-4 py-2 text-sm font-semibold text-white">Estimate</button>
                    </div>
                    <div class="mt-3 flex gap-2 text-sm">
                        <button class="rounded-md border border-zinc-300 px-3 py-2">Share</button>
                        <button class="rounded-md border border-zinc-300 px-3 py-2">Copy link</button>
                    </div>
                </div>
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-4 pb-10 sm:px-6 lg:px-8">
            <div class="rounded-lg border border-zinc-200 bg-white">
                <div class="flex border-b border-zinc-200">
                    <button class="px-4 py-3 text-sm font-semibold" :class="activeTab === 'qa' ? 'text-emerald-700' : 'text-zinc-500'" @click="activeTab = 'qa'">Product Q&A</button>
                    <button class="px-4 py-3 text-sm font-semibold" :class="activeTab === 'reviews' ? 'text-emerald-700' : 'text-zinc-500'" @click="activeTab = 'reviews'">Reviews</button>
                </div>
                <div class="p-4">
                    <div v-if="activeTab === 'qa'" class="space-y-4">
                        <div v-for="question in product.questions || []" :key="question.id" class="rounded-md bg-zinc-50 p-4">
                            <p class="text-sm font-semibold">{{ question.question }}</p>
                            <p v-for="answer in question.answers || []" :key="answer.id" class="mt-2 text-sm text-zinc-600">{{ answer.answer }}</p>
                        </div>
                        <p v-if="!product.questions?.length" class="text-sm text-zinc-600">No public questions yet.</p>
                    </div>
                    <div v-else class="space-y-4">
                        <div v-for="review in product.reviews || []" :key="review.id" class="rounded-md bg-zinc-50 p-4">
                            <p class="text-sm font-semibold">{{ review.title || `${review.rating}/5 rating` }}</p>
                            <p class="mt-2 text-sm text-zinc-600">{{ review.comment }}</p>
                        </div>
                        <p v-if="!product.reviews?.length" class="text-sm text-zinc-600">No reviews yet.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-4 pb-12 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold tracking-normal">Related products</h2>
            <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <ProductCard v-for="item in relatedProducts" :key="item.id" :product="item" />
            </div>
        </section>

        <div v-if="showSizeGuide" class="fixed inset-0 z-50 flex items-center justify-center bg-zinc-950/60 p-4" @click.self="showSizeGuide = false">
            <div class="w-full max-w-2xl rounded-lg bg-white p-6">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-bold">Size guide</h2>
                    <button class="rounded-md border border-zinc-300 px-3 py-1 text-sm" @click="showSizeGuide = false">Close</button>
                </div>
                <pre class="mt-4 overflow-auto rounded-md bg-zinc-50 p-4 text-sm">{{ product.size_guide || product.sizeGuide || 'No size guide available.' }}</pre>
            </div>
        </div>
    </StorefrontLayout>
</template>
