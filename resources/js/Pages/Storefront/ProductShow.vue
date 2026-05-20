<script setup>
import ProductCard from '@/Components/ProductCard.vue';
import ImageZoom from '@/Components/Storefront/ImageZoom.vue';
import SizeGuideModal from '@/Components/Storefront/SizeGuideModal.vue';
import VariantSelector from '@/Components/Storefront/VariantSelector.vue';
import WishlistButton from '@/Components/Storefront/WishlistButton.vue';
import StorefrontLayout from '@/Layouts/StorefrontLayout.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
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
    canAnswerQuestions: {
        type: Boolean,
        default: false,
    },
});

const selectedVariant = ref(null);
const selectedImage = ref(null);
const showSizeGuide = ref(false);
const activeTab = ref('qa');
const variantError = ref('');
const stockMessage = ref('');
const priceMessage = ref('');
const questionMessage = ref('');
const answerMessages = ref({});
const answerForms = ref({});
const page = usePage();

const hasVariants = computed(() => (props.product.variants || []).length > 0);
const isAuthenticated = computed(() => Boolean(page.props.auth?.user));
const fallbackImage = 'https://images.unsplash.com/photo-1503341455253-b2e723bb3dbb?auto=format&fit=crop&w=1200&q=80';
const productImages = computed(() => {
    const images = [
        props.product.image,
        ...(props.product.videos || []).filter((item) => item.type === 'image').map((item) => item.url),
        ...(props.product.variants || []).map((variant) => variant.image),
    ].filter(Boolean);

    return [...new Set(images)];
});
const displayImage = computed(() => selectedVariant.value?.image || selectedImage.value || productImages.value[0] || fallbackImage);
const sizeGuide = computed(() => props.product.size_guide || props.product.sizeGuide || null);
const price = computed(() => Number(selectedVariant.value?.price || props.product.selling_price || 0));
const stock = computed(() => Number(selectedVariant.value?.stock ?? props.product.stock ?? 0));
const sku = computed(() => selectedVariant.value?.sku || props.product.sku || '');
const suggestedTargetPrice = computed(() => Math.max(price.value - 5, 0.01).toFixed(2));

const cartForm = useForm({
    product_id: props.product.id,
    product_variant_id: null,
    quantity: 1,
});
const stockForm = useForm({
    product_id: props.product.id,
    product_variant_id: null,
    guest_email: '',
});
const priceForm = useForm({
    product_id: props.product.id,
    product_variant_id: null,
    guest_email: '',
    target_price_usd: suggestedTargetPrice.value,
});
const questionForm = useForm({
    question: '',
});

const money = (value) => new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(Number(value || 0));
const answerFormFor = (questionId) => {
    if (!answerForms.value[questionId]) {
        answerForms.value[questionId] = useForm({ answer: '' });
    }

    return answerForms.value[questionId];
};

const addToCart = () => {
    variantError.value = '';

    if (hasVariants.value && !selectedVariant.value) {
        variantError.value = 'Please select a product variant before adding this item to cart.';

        return;
    }

    cartForm.product_variant_id = selectedVariant.value?.id || null;
    cartForm.post(route('cart.store'), { preserveScroll: true });
};

const onVariantChange = ({ variant, complete }) => {
    selectedVariant.value = variant;
    cartForm.product_variant_id = variant?.id || null;
    stockForm.product_variant_id = variant?.id || null;
    priceForm.product_variant_id = variant?.id || null;
    priceForm.target_price_usd = suggestedTargetPrice.value;
    cartForm.quantity = 1;
    stockForm.clearErrors();
    priceForm.clearErrors();
    stockMessage.value = '';
    priceMessage.value = '';
    variantError.value = complete && !variant ? 'This option combination is not available.' : '';

    if (variant?.image) {
        selectedImage.value = variant.image;
    }
};

const submitStockAlert = () => {
    stockForm.product_id = props.product.id;
    stockForm.product_variant_id = selectedVariant.value?.id || null;
    stockMessage.value = '';

    stockForm.post(route('notifications.stock.store'), {
        preserveScroll: true,
        onSuccess: () => {
            stockMessage.value = page.props.flash?.status || 'We will email you when this item is back in stock.';
        },
    });
};

const submitPriceAlert = () => {
    priceForm.product_id = props.product.id;
    priceForm.product_variant_id = selectedVariant.value?.id || null;
    priceMessage.value = '';

    priceForm.post(route('notifications.price-alert.store'), {
        preserveScroll: true,
        onSuccess: () => {
            priceMessage.value = page.props.flash?.status || 'We will email you if the price reaches your target.';
        },
    });
};

const submitQuestion = () => {
    questionMessage.value = '';

    questionForm.post(route('products.questions.store', props.product.id), {
        preserveScroll: true,
        onSuccess: () => {
            questionForm.reset();
            questionMessage.value = page.props.flash?.status || 'Your question was posted. The vendor will be notified.';
        },
    });
};

const submitAnswer = (questionId) => {
    const form = answerFormFor(questionId);
    answerMessages.value[questionId] = '';

    form.post(route('questions.answers.store', questionId), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            answerMessages.value[questionId] = page.props.flash?.status || 'Your answer was posted.';
        },
    });
};
</script>

<template>
    <StorefrontLayout>
        <Head :title="product.name" />

        <section class="mx-auto grid max-w-7xl gap-10 px-4 py-8 sm:px-6 lg:grid-cols-[1fr_0.9fr] lg:px-8">
            <div>
                <ImageZoom :src="displayImage" :alt="product.name" />
                <div class="mt-4 grid grid-cols-4 gap-3">
                    <button
                        v-for="image in productImages"
                        :key="image"
                        type="button"
                        class="overflow-hidden rounded-md border border-zinc-200 bg-white"
                        :class="displayImage === image ? 'ring-2 ring-zinc-950' : ''"
                        @click="selectedImage = image"
                    >
                        <img :src="image" :alt="product.name" class="aspect-square w-full object-cover" />
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
                    <VariantSelector
                        v-model="selectedVariant"
                        :attributes="product.attributes || []"
                        :variants="product.variants || []"
                        @change="onVariantChange"
                    />
                    <p v-if="variantError || cartForm.errors.product_variant_id" class="mt-3 text-sm text-rose-700">
                        {{ variantError || cartForm.errors.product_variant_id }}
                    </p>
                </div>

                <div class="mt-6 rounded-lg border border-zinc-200 bg-white p-4">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-semibold">Stock</p>
                        <p class="text-sm" :class="stock > 0 ? 'text-emerald-700' : 'text-rose-700'">{{ stock > 0 ? `${stock} available` : 'Out of stock' }}</p>
                    </div>
                    <p v-if="sku" class="mt-2 text-sm text-zinc-500">SKU: {{ sku }}</p>
                    <div class="mt-4 flex items-center gap-3">
                        <input v-model.number="cartForm.quantity" type="number" min="1" :max="Math.max(stock, 1)" class="w-24 rounded-md border-zinc-300 text-sm" />
                        <button class="rounded-md bg-zinc-950 px-4 py-2 text-sm font-semibold text-white disabled:opacity-50" :disabled="stock <= 0 || cartForm.processing" @click="addToCart">
                            Add to cart
                        </button>
                        <WishlistButton :product="product" show-label />
                    </div>
                    <p v-if="cartForm.errors.quantity" class="mt-2 text-sm text-rose-700">{{ cartForm.errors.quantity }}</p>
                    <div class="mt-3">
                        <button v-if="sizeGuide" class="rounded-md border border-zinc-300 px-3 py-2 text-sm" @click="showSizeGuide = true">Size guide</button>
                    </div>
                </div>

                <div v-if="stock <= 0" class="mt-4 rounded-lg border border-amber-200 bg-amber-50 p-4">
                    <p class="text-sm font-semibold text-amber-950">Notify me when available</p>
                    <form class="mt-3 flex flex-col gap-3 sm:flex-row" @submit.prevent="submitStockAlert">
                        <input
                            v-if="!isAuthenticated"
                            v-model="stockForm.guest_email"
                            type="email"
                            class="min-w-0 flex-1 rounded-md border-amber-200 text-sm"
                            placeholder="Email address"
                        />
                        <button
                            type="submit"
                            class="rounded-md bg-amber-600 px-4 py-2 text-sm font-semibold text-white disabled:opacity-50"
                            :disabled="stockForm.processing"
                        >
                            Notify me
                        </button>
                    </form>
                    <p v-if="stockForm.errors.guest_email" class="mt-2 text-sm text-rose-700">{{ stockForm.errors.guest_email }}</p>
                    <p v-if="stockForm.errors.product_id || stockForm.errors.product_variant_id" class="mt-2 text-sm text-rose-700">
                        {{ stockForm.errors.product_id || stockForm.errors.product_variant_id }}
                    </p>
                    <p v-if="stockMessage" class="mt-2 text-sm text-emerald-700">{{ stockMessage }}</p>
                </div>

                <div class="mt-4 rounded-lg border border-zinc-200 bg-white p-4">
                    <p class="text-sm font-semibold">Price drop alert</p>
                    <form class="mt-3 grid gap-3 sm:grid-cols-[minmax(0,1fr)_8rem_auto]" @submit.prevent="submitPriceAlert">
                        <input
                            v-if="!isAuthenticated"
                            v-model="priceForm.guest_email"
                            type="email"
                            class="min-w-0 rounded-md border-zinc-300 text-sm"
                            placeholder="Email address"
                        />
                        <div v-else class="hidden sm:block"></div>
                        <input
                            v-model="priceForm.target_price_usd"
                            type="number"
                            min="0.01"
                            step="0.01"
                            class="rounded-md border-zinc-300 text-sm"
                            aria-label="Target price in USD"
                        />
                        <button
                            type="submit"
                            class="rounded-md border border-zinc-950 px-4 py-2 text-sm font-semibold text-zinc-950 disabled:opacity-50"
                            :disabled="priceForm.processing"
                        >
                            Set alert
                        </button>
                    </form>
                    <p class="mt-2 text-xs text-zinc-500">Current price: {{ money(price) }}</p>
                    <p v-if="priceForm.errors.guest_email" class="mt-2 text-sm text-rose-700">{{ priceForm.errors.guest_email }}</p>
                    <p v-if="priceForm.errors.target_price_usd" class="mt-2 text-sm text-rose-700">{{ priceForm.errors.target_price_usd }}</p>
                    <p v-if="priceForm.errors.product_id || priceForm.errors.product_variant_id" class="mt-2 text-sm text-rose-700">
                        {{ priceForm.errors.product_id || priceForm.errors.product_variant_id }}
                    </p>
                    <p v-if="priceMessage" class="mt-2 text-sm text-emerald-700">{{ priceMessage }}</p>
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
                            <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-normal text-zinc-500">{{ question.asker_label || 'Customer' }}</p>
                                    <p class="mt-1 text-sm font-semibold text-zinc-950">{{ question.question }}</p>
                                </div>
                                <p v-if="question.created_at" class="text-xs text-zinc-500">{{ new Date(question.created_at).toLocaleDateString() }}</p>
                            </div>
                            <div v-if="question.answers?.length" class="mt-4 space-y-3 border-l-2 border-emerald-200 pl-4">
                                <div v-for="answer in question.answers" :key="answer.id" class="rounded-md bg-white p-3">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="text-xs font-semibold text-zinc-700">{{ answer.author_label || 'GlobalDrop team' }}</span>
                                        <span v-if="answer.is_vendor" class="rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-semibold text-emerald-800">Vendor</span>
                                        <span v-if="answer.is_verified" class="rounded-full bg-sky-100 px-2 py-0.5 text-xs font-semibold text-sky-800">Verified</span>
                                    </div>
                                    <p class="mt-2 text-sm leading-6 text-zinc-600">{{ answer.answer }}</p>
                                </div>
                            </div>
                            <p v-else class="mt-3 text-sm text-zinc-500">No answer yet.</p>
                            <form v-if="canAnswerQuestions" class="mt-4 space-y-2 border-t border-zinc-200 pt-4" @submit.prevent="submitAnswer(question.id)">
                                <textarea
                                    v-model="answerFormFor(question.id).answer"
                                    rows="3"
                                    class="w-full rounded-md border-zinc-300 text-sm"
                                    placeholder="Write a clear answer for this customer question."
                                />
                                <p v-if="answerFormFor(question.id).errors.answer" class="text-sm text-rose-700">{{ answerFormFor(question.id).errors.answer }}</p>
                                <p v-if="answerMessages[question.id]" class="text-sm text-emerald-700">{{ answerMessages[question.id] }}</p>
                                <button
                                    type="submit"
                                    class="rounded-md bg-emerald-700 px-4 py-2 text-sm font-semibold text-white disabled:opacity-50"
                                    :disabled="answerFormFor(question.id).processing"
                                >
                                    Post answer
                                </button>
                            </form>
                        </div>
                        <p v-if="!product.questions?.length" class="text-sm text-zinc-600">No public questions yet.</p>
                        <div class="rounded-md border border-zinc-200 bg-white p-4">
                            <template v-if="isAuthenticated">
                                <p class="text-sm font-semibold text-zinc-950">Ask a question</p>
                                <form class="mt-3 space-y-3" @submit.prevent="submitQuestion">
                                    <textarea
                                        v-model="questionForm.question"
                                        rows="4"
                                        class="w-full rounded-md border-zinc-300 text-sm"
                                        placeholder="Ask about sizing, materials, shipping, or anything you need before buying."
                                    />
                                    <p v-if="questionForm.errors.question" class="text-sm text-rose-700">{{ questionForm.errors.question }}</p>
                                    <p v-if="questionMessage" class="text-sm text-emerald-700">{{ questionMessage }}</p>
                                    <button
                                        type="submit"
                                        class="rounded-md bg-zinc-950 px-4 py-2 text-sm font-semibold text-white disabled:opacity-50"
                                        :disabled="questionForm.processing"
                                    >
                                        Post question
                                    </button>
                                </form>
                            </template>
                            <template v-else>
                                <p class="text-sm font-semibold text-zinc-950">Have a question?</p>
                                <p class="mt-1 text-sm text-zinc-600">Sign in or create an account to ask the vendor before buying.</p>
                                <div class="mt-3 flex flex-wrap gap-2">
                                    <Link :href="route('login')" class="rounded-md bg-zinc-950 px-4 py-2 text-sm font-semibold text-white">Sign in</Link>
                                    <Link :href="route('register')" class="rounded-md border border-zinc-300 px-4 py-2 text-sm font-semibold text-zinc-950">Create account</Link>
                                </div>
                            </template>
                        </div>
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

        <SizeGuideModal :show="showSizeGuide" :size-guide="sizeGuide" @close="showSizeGuide = false" />
    </StorefrontLayout>
</template>
