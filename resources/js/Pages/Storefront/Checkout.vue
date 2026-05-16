<script setup>
import StorefrontLayout from '@/Layouts/StorefrontLayout.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    cartItems: {
        type: Array,
        default: () => [],
    },
    addresses: {
        type: Array,
        default: () => [],
    },
});

const page = usePage();
const step = ref(1);
const form = useForm({
    address_id: props.addresses[0]?.id || null,
    guest_name: '',
    guest_email: '',
    guest_phone: '',
    guest_address_line1: '',
    guest_address_line2: '',
    guest_city: '',
    guest_state: '',
    guest_postal_code: '',
    guest_country: 'US',
    shipping_cost_usd: 12,
    buyer_currency: 'USD',
    payment_method: 'stripe',
    notes: '',
});

const money = (value) => new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(Number(value || 0));
const unitPrice = (item) => Number(item.productVariant?.price || item.product_variant?.price || item.product?.selling_price || 0);
const variant = (item) => item.productVariant || item.product_variant || null;
const variantText = (item) => Object.entries(variant(item)?.combination || {}).map(([key, value]) => `${key}: ${value}`).join(', ');
const subtotal = computed(() => props.cartItems.reduce((sum, item) => sum + unitPrice(item) * Number(item.quantity || 0), 0));
const total = computed(() => subtotal.value + Number(form.shipping_cost_usd || 0));
const isGuest = computed(() => !page.props.auth?.user);
const hasItems = computed(() => props.cartItems.length > 0);

const submit = () => {
    form.post(route(isGuest.value ? 'checkout.guest' : 'checkout.store'));
};
</script>

<template>
    <StorefrontLayout>
        <Head title="Checkout" />

        <section class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold tracking-normal">Checkout</h1>
            <div class="mt-6 grid gap-8 lg:grid-cols-[1fr_360px]">
                <div class="rounded-lg border border-zinc-200 bg-white p-5">
                    <div v-if="!hasItems" class="mb-6 rounded-md bg-zinc-50 p-4 text-sm text-zinc-600">
                        Your cart is empty.
                        <Link :href="route('products.index')" class="font-semibold text-emerald-700">Browse products</Link>
                        to start checkout.
                    </div>

                    <div class="mb-6 grid grid-cols-3 gap-2 text-sm font-semibold">
                        <button class="rounded-md px-3 py-2" :class="step === 1 ? 'bg-zinc-950 text-white' : 'bg-zinc-100'" @click="step = 1">1. Address</button>
                        <button class="rounded-md px-3 py-2" :class="step === 2 ? 'bg-zinc-950 text-white' : 'bg-zinc-100'" @click="step = 2">2. Shipping</button>
                        <button class="rounded-md px-3 py-2" :class="step === 3 ? 'bg-zinc-950 text-white' : 'bg-zinc-100'" @click="step = 3">3. Payment</button>
                    </div>

                    <div v-if="step === 1" class="space-y-4">
                        <div v-if="!isGuest" class="space-y-2">
                            <label class="text-sm font-semibold">Saved shipping address</label>
                            <select v-model="form.address_id" class="w-full rounded-md border-zinc-300 text-sm">
                                <option :value="null">Use account contact information</option>
                                <option v-for="address in addresses" :key="address.id" :value="address.id">
                                    {{ address.full_name }} - {{ address.city }}, {{ address.country }}
                                </option>
                            </select>
                            <p v-if="form.errors.address_id" class="text-sm text-rose-600">{{ form.errors.address_id }}</p>
                        </div>

                        <div v-else class="space-y-4">
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="text-sm font-semibold">Full name</label>
                                    <input v-model="form.guest_name" class="mt-1 w-full rounded-md border-zinc-300 text-sm" placeholder="Sarah Tan" />
                                    <p v-if="form.errors.guest_name" class="mt-1 text-sm text-rose-600">{{ form.errors.guest_name }}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-semibold">Email</label>
                                    <input v-model="form.guest_email" type="email" class="mt-1 w-full rounded-md border-zinc-300 text-sm" placeholder="buyer@example.com" />
                                    <p v-if="form.errors.guest_email" class="mt-1 text-sm text-rose-600">{{ form.errors.guest_email }}</p>
                                </div>
                            </div>

                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="text-sm font-semibold">Phone</label>
                                    <input v-model="form.guest_phone" class="mt-1 w-full rounded-md border-zinc-300 text-sm" placeholder="+1 555 0100" />
                                    <p v-if="form.errors.guest_phone" class="mt-1 text-sm text-rose-600">{{ form.errors.guest_phone }}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-semibold">Country code</label>
                                    <input v-model="form.guest_country" maxlength="2" class="mt-1 w-full rounded-md border-zinc-300 text-sm uppercase" placeholder="US" />
                                    <p v-if="form.errors.guest_country" class="mt-1 text-sm text-rose-600">{{ form.errors.guest_country }}</p>
                                </div>
                            </div>

                            <div>
                                <label class="text-sm font-semibold">Address line 1</label>
                                <input v-model="form.guest_address_line1" class="mt-1 w-full rounded-md border-zinc-300 text-sm" placeholder="123 Market Street" />
                                <p v-if="form.errors.guest_address_line1" class="mt-1 text-sm text-rose-600">{{ form.errors.guest_address_line1 }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-semibold">Address line 2</label>
                                <input v-model="form.guest_address_line2" class="mt-1 w-full rounded-md border-zinc-300 text-sm" placeholder="Apartment, suite, building" />
                                <p v-if="form.errors.guest_address_line2" class="mt-1 text-sm text-rose-600">{{ form.errors.guest_address_line2 }}</p>
                            </div>
                            <div class="grid gap-4 sm:grid-cols-3">
                                <div>
                                    <label class="text-sm font-semibold">City</label>
                                    <input v-model="form.guest_city" class="mt-1 w-full rounded-md border-zinc-300 text-sm" placeholder="Melbourne" />
                                    <p v-if="form.errors.guest_city" class="mt-1 text-sm text-rose-600">{{ form.errors.guest_city }}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-semibold">State</label>
                                    <input v-model="form.guest_state" class="mt-1 w-full rounded-md border-zinc-300 text-sm" placeholder="VIC" />
                                    <p v-if="form.errors.guest_state" class="mt-1 text-sm text-rose-600">{{ form.errors.guest_state }}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-semibold">Postal code</label>
                                    <input v-model="form.guest_postal_code" class="mt-1 w-full rounded-md border-zinc-300 text-sm" placeholder="3000" />
                                    <p v-if="form.errors.guest_postal_code" class="mt-1 text-sm text-rose-600">{{ form.errors.guest_postal_code }}</p>
                                </div>
                            </div>
                        </div>

                        <textarea v-model="form.notes" class="w-full rounded-md border-zinc-300 text-sm" rows="3" placeholder="Order notes"></textarea>
                        <button class="rounded-md bg-zinc-950 px-4 py-2 text-sm font-semibold text-white disabled:opacity-50" :disabled="!hasItems" @click="step = 2">Continue</button>
                    </div>

                    <div v-if="step === 2" class="space-y-3">
                        <label class="flex items-center justify-between rounded-md border border-zinc-300 p-4">
                            <span>
                                <span class="block text-sm font-semibold">Standard global shipping</span>
                                <span class="block text-sm text-zinc-500">7-14 business days via carrier aggregator</span>
                            </span>
                            <input v-model="form.shipping_cost_usd" type="radio" :value="12" />
                        </label>
                        <label class="flex items-center justify-between rounded-md border border-zinc-300 p-4">
                            <span>
                                <span class="block text-sm font-semibold">Express shipping</span>
                                <span class="block text-sm text-zinc-500">3-7 business days</span>
                            </span>
                            <input v-model="form.shipping_cost_usd" type="radio" :value="28" />
                        </label>
                        <button class="rounded-md bg-zinc-950 px-4 py-2 text-sm font-semibold text-white" @click="step = 3">Continue</button>
                    </div>

                    <div v-if="step === 3" class="space-y-4">
                        <div class="grid gap-3 sm:grid-cols-2">
                            <button class="rounded-md border px-4 py-3 text-sm font-semibold" :class="form.payment_method === 'stripe' ? 'border-zinc-950 bg-zinc-950 text-white' : 'border-zinc-300'" @click="form.payment_method = 'stripe'">
                                Stripe Elements
                            </button>
                            <button class="rounded-md border px-4 py-3 text-sm font-semibold" :class="form.payment_method === 'paypal' ? 'border-zinc-950 bg-zinc-950 text-white' : 'border-zinc-300'" @click="form.payment_method = 'paypal'">
                                PayPal
                            </button>
                        </div>
                        <div class="rounded-md bg-zinc-50 p-4 text-sm text-zinc-600">
                            Payment widgets are represented as frontend-ready placeholders; backend intent routes are available for Stripe and PayPal.
                        </div>
                        <button class="rounded-md bg-emerald-600 px-4 py-2 text-sm font-semibold text-white disabled:opacity-50" :disabled="form.processing || !hasItems" @click="submit">
                            Place order
                        </button>
                    </div>
                </div>

                <aside class="self-start rounded-lg border border-zinc-200 bg-white p-5">
                    <h2 class="text-lg font-bold">Summary</h2>
                    <div class="mt-4 space-y-3">
                        <div v-for="item in cartItems" :key="item.id" class="flex justify-between gap-4 text-sm">
                            <span>
                                <span class="block">{{ item.product?.name }} x {{ item.quantity }}</span>
                                <span v-if="variantText(item)" class="block text-xs text-zinc-500">{{ variantText(item) }}</span>
                            </span>
                            <span>{{ money(unitPrice(item) * item.quantity) }}</span>
                        </div>
                    </div>
                    <div class="mt-5 space-y-2 border-t border-zinc-200 pt-4 text-sm">
                        <div class="flex justify-between"><span>Subtotal</span><span>{{ money(subtotal) }}</span></div>
                        <div class="flex justify-between"><span>Shipping</span><span>{{ money(form.shipping_cost_usd) }}</span></div>
                        <div class="flex justify-between text-base font-bold"><span>Total</span><span>{{ money(total) }}</span></div>
                    </div>
                </aside>
            </div>
        </section>
    </StorefrontLayout>
</template>
